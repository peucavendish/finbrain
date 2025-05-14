<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;

class DiagnosticoTributarioController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = Config::get('tax');
    }

    public function analyze(Request $request)
    {
        try {
            Log::info('Recebendo requisição de diagnóstico tributário:', $request->all());
            
            // Validar os dados de entrada
            $validated = $request->validate([
                'total_income' => 'required|numeric|min:0',
                'income_sources' => 'required|array',
                'income_sources.*.type' => 'required|string',
                'income_sources.*.value' => 'required|numeric|min:0',
                'assets' => 'required|array',
                'assets.*.type' => 'required|string',
                'assets.*.value' => 'required|numeric|min:0',
                'tax_regime' => 'required|string',
                'has_company' => 'required|boolean',
                'company_details' => 'required_if:has_company,true|array',
                'deductions' => 'array',
                'investments' => 'array'
            ]);

            Log::info('Dados validados:', $validated);

            // Calcular métricas tributárias
            $taxBurden = $this->calculateTaxBurden($validated);
            $optimizationScore = $this->calculateOptimizationScore($validated);
            $potentialSavings = $this->calculatePotentialSavings($validated);
            
            // Tentar obter análise detalhada da IA
            try {
                $prompt = $this->buildPrompt($validated);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em planejamento tributário. Forneça análises profissionais e detalhadas, mantendo um tom acessível e explicativo.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500
                ]);

                $analysis = $this->processResponse($result->choices[0]->message->content);
                Log::info('Resposta da OpenAI recebida com sucesso');
            } catch (OpenAIErrorException $e) {
                Log::error('Erro na chamada da OpenAI: ' . $e->getMessage());
                $analysis = $this->generateFallbackAnalysis($validated);
            }

            $response = [
                'success' => true,
                'data' => [
                    'tax_burden' => $taxBurden,
                    'optimization_score' => $optimizationScore,
                    'potential_savings' => $potentialSavings,
                    'detailed_analysis' => $analysis['analysis'],
                    'recommendations' => $analysis['recommendations'],
                    'tax_considerations' => $analysis['tax_considerations'],
                    'next_steps' => $analysis['next_steps']
                ]
            ];

            Log::info('Resposta:', $response);
            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro no diagnóstico tributário: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação.',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateTaxBurden($data)
    {
        $totalTax = 0;
        $totalIncome = $data['total_income'];

        // Cálculo de impostos por fonte de renda
        foreach ($data['income_sources'] as $source) {
            switch ($source['type']) {
                case 'salario':
                    $totalTax += $this->calculateIncomeTax($source['value']);
                    break;
                case 'aluguel':
                    $totalTax += $source['value'] * 0.275; // IRPF máximo
                    break;
                case 'dividendos':
                    // Isento de IR pessoa física
                    break;
                case 'investimentos':
                    $totalTax += $source['value'] * 0.225; // Taxa média de IR
                    break;
                default:
                    $totalTax += $source['value'] * 0.275;
            }
        }

        return [
            'total' => round($totalTax, 2),
            'percentage' => round(($totalTax / $totalIncome) * 100, 2),
            'monthly_burden' => round($totalTax / 12, 2)
        ];
    }

    private function calculateIncomeTax($income)
    {
        // Tabela progressiva IRPF 2024
        if ($income <= 24751.74) return 0;
        if ($income <= 32919.00) return ($income * 0.075) - 1856.38;
        if ($income <= 41566.16) return ($income * 0.150) - 4316.94;
        if ($income <= 50526.92) return ($income * 0.225) - 7353.88;
        return ($income * 0.275) - 9875.16;
    }

    private function calculateOptimizationScore($data)
    {
        $score = 100; // Pontuação máxima

        // Fatores que reduzem a otimização
        if (!isset($data['deductions']) || empty($data['deductions'])) $score -= 20;
        if (!isset($data['investments']) || empty($data['investments'])) $score -= 15;
        if ($data['has_company'] && empty($data['company_details'])) $score -= 25;
        if ($data['tax_regime'] === 'simples' && $data['total_income'] > 500000) $score -= 30;

        return max(0, $score);
    }

    private function calculatePotentialSavings($data)
    {
        $currentTax = $this->calculateTaxBurden($data)['total'];
        $potentialSavings = 0;

        // Análise de possíveis deduções não utilizadas
        $maxDeductions = $data['total_income'] * 0.20; // Estimativa de deduções possíveis
        $currentDeductions = isset($data['deductions']) ? array_sum(array_column($data['deductions'], 'value')) : 0;
        $potentialSavings += ($maxDeductions - $currentDeductions) * 0.275;

        // Análise de estruturação societária
        if (!$data['has_company'] && $data['total_income'] > 300000) {
            $potentialSavings += $currentTax * 0.30; // Estimativa de economia com PJ
        }

        return [
            'annual' => round($potentialSavings, 2),
            'monthly' => round($potentialSavings / 12, 2),
            'percentage' => round(($potentialSavings / $currentTax) * 100, 2)
        ];
    }

    private function buildPrompt($data)
    {
        $incomeInfo = collect($data['income_sources'])
            ->map(fn($source) => "- {$source['type']}: R$ " . number_format($source['value'], 2, ',', '.'))
            ->join("\n");

        $assetsInfo = collect($data['assets'])
            ->map(fn($asset) => "- {$asset['type']}: R$ " . number_format($asset['value'], 2, ',', '.'))
            ->join("\n");

        $hasCompany = $data['has_company'] ? 'Sim' : 'Não';

        return "Por favor, analise este cenário tributário:

        RENDA:
        Total Anual: R$ {$data['total_income']}
        Composição:
        {$incomeInfo}

        PATRIMÔNIO:
        {$assetsInfo}

        INFORMAÇÕES ADICIONAIS:
        - Regime Tributário: {$data['tax_regime']}
        - Possui Empresa: {$hasCompany}

        Por favor, forneça:
        1. Análise detalhada da situação tributária atual
        2. Principais oportunidades de otimização fiscal
        3. Recomendações específicas para redução da carga tributária
        4. Considerações sobre planejamento tributário
        5. Próximos passos recomendados

        Mantenha um tom profissional mas acessível, e forneça explicações claras para suas conclusões.";
    }

    private function processResponse($content)
    {
        $sections = [
            'analysis' => '',
            'recommendations' => [],
            'tax_considerations' => [],
            'next_steps' => []
        ];

        $lines = explode("\n", $content);
        $currentSection = 'analysis';

        foreach ($lines as $line) {
            if (strpos($line, 'Recomendações:') !== false) {
                $currentSection = 'recommendations';
                continue;
            } elseif (strpos($line, 'Considerações Tributárias:') !== false) {
                $currentSection = 'tax_considerations';
                continue;
            } elseif (strpos($line, 'Próximos Passos:') !== false) {
                $currentSection = 'next_steps';
                continue;
            }

            if ($line = trim($line)) {
                if (in_array($currentSection, ['recommendations', 'tax_considerations', 'next_steps'])) {
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

    private function generateFallbackAnalysis($data)
    {
        $taxBurden = $this->calculateTaxBurden($data);
        $optimizationScore = $this->calculateOptimizationScore($data);
        
        return [
            'analysis' => "Com base nos dados fornecidos, sua carga tributária atual é de {$taxBurden['percentage']}% da renda total, " .
                         "com um score de otimização de {$optimizationScore}%. " .
                         "Existem oportunidades para melhorar sua eficiência tributária através de um planejamento adequado.",
            'recommendations' => [
                'Avalie a possibilidade de constituir uma pessoa jurídica',
                'Documente adequadamente todas as despesas dedutíveis',
                'Considere diversificar seus investimentos para otimização fiscal'
            ],
            'tax_considerations' => [
                'Enquadramento no regime tributário adequado',
                'Aproveitamento integral de deduções legais',
                'Estruturação patrimonial eficiente'
            ],
            'next_steps' => [
                'Reunir documentação fiscal completa',
                'Consultar um especialista em planejamento tributário',
                'Implementar controles financeiros detalhados'
            ]
        ];
    }
} 