<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;

class DiagnosticoHoldingController extends Controller
{
    public function analyze(Request $request)
    {
        try {
            Log::info('Recebendo requisição de diagnóstico holding:', $request->all());
            
            // Validar os dados de entrada
            $validated = $request->validate([
                'total_assets' => 'required|numeric|min:0',
                'assets' => 'required|array',
                'assets.*.type' => 'required|string',
                'assets.*.value' => 'required|numeric|min:0',
                'business_count' => 'required|integer|min:0',
                'real_estate_count' => 'required|integer|min:0',
                'family_members' => 'required|integer|min:1',
                'has_succession_plan' => 'required|boolean',
                'has_international_assets' => 'required|boolean',
                'current_tax_exposure' => 'required|numeric|min:0',
                'risk_factors' => 'array'
            ]);

            // Calcular métricas
            $complexityScore = $this->calculateComplexityScore($validated);
            $holdingRecommendation = $this->evaluateHoldingNeed($validated, $complexityScore);
            $potentialBenefits = $this->calculatePotentialBenefits($validated);
            
            try {
                $prompt = $this->buildPrompt($validated, $complexityScore, $holdingRecommendation);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em planejamento patrimonial e estruturação de holdings. Forneça análises profissionais e detalhadas, mantendo um tom acessível e explicativo.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500
                ]);

                $analysis = $this->processResponse($result->choices[0]->message->content);
                Log::info('Resposta da OpenAI recebida com sucesso');
            } catch (OpenAIErrorException $e) {
                Log::error('Erro na chamada da OpenAI: ' . $e->getMessage());
                $analysis = $this->generateFallbackAnalysis($validated, $complexityScore, $holdingRecommendation);
            }

            $response = [
                'success' => true,
                'data' => [
                    'complexity_score' => $complexityScore,
                    'holding_recommendation' => $holdingRecommendation,
                    'potential_benefits' => $potentialBenefits,
                    'detailed_analysis' => $analysis['analysis'],
                    'recommendations' => $analysis['recommendations'],
                    'considerations' => $analysis['considerations'],
                    'next_steps' => $analysis['next_steps']
                ]
            ];

            Log::info('Resposta:', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erro no diagnóstico holding: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação.',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateComplexityScore($data)
    {
        $score = 0;
        
        // Pontuação baseada no valor total dos ativos
        $totalAssets = $data['total_assets'];
        if ($totalAssets > 10000000) $score += 30;
        elseif ($totalAssets > 5000000) $score += 20;
        elseif ($totalAssets > 1000000) $score += 10;

        // Pontuação por quantidade de empresas
        $score += min(30, $data['business_count'] * 5);

        // Pontuação por imóveis
        $score += min(20, $data['real_estate_count'] * 3);

        // Pontuação por membros da família
        $score += min(10, ($data['family_members'] - 1) * 2);

        // Fatores adicionais
        if (!$data['has_succession_plan']) $score += 15;
        if ($data['has_international_assets']) $score += 20;

        return min(100, $score);
    }

    private function evaluateHoldingNeed($data, $complexityScore)
    {
        if ($complexityScore >= 70) {
            return [
                'recommendation' => 'Alta Necessidade',
                'urgency' => 'Urgente',
                'confidence' => 'Alta'
            ];
        } elseif ($complexityScore >= 40) {
            return [
                'recommendation' => 'Média Necessidade',
                'urgency' => 'Importante',
                'confidence' => 'Média'
            ];
        } else {
            return [
                'recommendation' => 'Baixa Necessidade',
                'urgency' => 'Não Urgente',
                'confidence' => 'Alta'
            ];
        }
    }

    private function calculatePotentialBenefits($data)
    {
        $benefits = [
            'tax_savings' => 0,
            'protection_level' => 0,
            'succession_efficiency' => 0
        ];

        // Estimativa de economia fiscal
        $currentTaxExposure = $data['current_tax_exposure'];
        $benefits['tax_savings'] = $currentTaxExposure * 0.3; // Estimativa de 30% de economia

        // Nível de proteção patrimonial (0-100)
        $benefits['protection_level'] = min(100, 
            40 + // Base
            ($data['business_count'] * 5) + // Empresas
            ($data['real_estate_count'] * 3) + // Imóveis
            ($data['has_international_assets'] ? 20 : 0) // Ativos internacionais
        );

        // Eficiência sucessória (0-100)
        $benefits['succession_efficiency'] = min(100,
            50 + // Base
            ($data['has_succession_plan'] ? 30 : 0) + // Plano sucessório
            (min(20, $data['family_members'] * 5)) // Membros da família
        );

        return $benefits;
    }

    private function buildPrompt($data, $complexityScore, $recommendation)
    {
        $assetsInfo = collect($data['assets'])
            ->map(fn($asset) => "- {$asset['type']}: R$ " . number_format($asset['value'], 2, ',', '.'))
            ->join("\n");

        return "Por favor, analise a necessidade de uma holding patrimonial com base nos seguintes dados:

        PERFIL PATRIMONIAL:
        - Patrimônio Total: R$ " . number_format($data['total_assets'], 2, ',', '.') . "
        - Quantidade de Empresas: {$data['business_count']}
        - Quantidade de Imóveis: {$data['real_estate_count']}
        - Membros da Família: {$data['family_members']}

        COMPOSIÇÃO PATRIMONIAL:
        {$assetsInfo}

        FATORES ADICIONAIS:
        - Possui Plano Sucessório: " . ($data['has_succession_plan'] ? 'Sim' : 'Não') . "
        - Ativos Internacionais: " . ($data['has_international_assets'] ? 'Sim' : 'Não') . "
        - Score de Complexidade: {$complexityScore}
        - Recomendação: {$recommendation['recommendation']}

        Por favor, forneça:
        1. Análise detalhada da necessidade de holding patrimonial
        2. Principais benefícios e vantagens da estruturação
        3. Considerações específicas para este perfil
        4. Próximos passos recomendados
        5. Riscos e pontos de atenção

        Mantenha um tom profissional mas acessível, e forneça explicações claras para suas conclusões.";
    }

    private function processResponse($content)
    {
        $sections = [
            'analysis' => '',
            'recommendations' => [],
            'considerations' => [],
            'next_steps' => []
        ];

        $lines = explode("\n", $content);
        $currentSection = 'analysis';

        foreach ($lines as $line) {
            if (strpos($line, 'Recomendações:') !== false) {
                $currentSection = 'recommendations';
                continue;
            } elseif (strpos($line, 'Considerações:') !== false) {
                $currentSection = 'considerations';
                continue;
            } elseif (strpos($line, 'Próximos Passos:') !== false) {
                $currentSection = 'next_steps';
                continue;
            }

            if ($line = trim($line)) {
                if (in_array($currentSection, ['recommendations', 'considerations', 'next_steps'])) {
                    if (strpos($line, '- ') === 0 || strpos($line, '• ') === 0) {
                        $sections[$currentSection][] = trim(substr($line, 2));
                    }
                } else {
                    $sections[$currentSection] .= $line . "\n";
                }
            }
        }

        return $sections;
    }

    private function generateFallbackAnalysis($data, $complexityScore, $recommendation)
    {
        return [
            'analysis' => "Com base nos dados fornecidos, sua estrutura patrimonial apresenta um nível de complexidade de {$complexityScore}%, " .
                         "indicando {$recommendation['recommendation']} de estruturação via holding. " .
                         "O patrimônio total de R$ " . number_format($data['total_assets'], 2, ',', '.') . " " .
                         "distribuído entre {$data['business_count']} empresas e {$data['real_estate_count']} imóveis " .
                         "sugere uma necessidade de organização estrutural para otimização fiscal e proteção patrimonial.",
            
            'recommendations' => [
                'Avaliar a constituição de uma holding patrimonial',
                'Realizar planejamento sucessório estruturado',
                'Considerar segregação de ativos por natureza',
                'Implementar governança familiar'
            ],
            
            'considerations' => [
                'Impactos fiscais da reorganização societária',
                'Custos de manutenção da estrutura',
                'Necessidade de compliance e governança',
                'Proteção patrimonial e blindagem de ativos'
            ],
            
            'next_steps' => [
                'Consultar especialista em planejamento patrimonial',
                'Levantar documentação completa dos ativos',
                'Avaliar custos e benefícios da estruturação',
                'Definir modelo de governança familiar'
            ]
        ];
    }
} 