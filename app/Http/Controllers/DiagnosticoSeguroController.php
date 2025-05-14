<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;

class DiagnosticoSeguroController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = Config::get('insurance.life_insurance');
    }

    public function analyze(Request $request)
    {
        try {
            Log::info('Recebendo requisição:', $request->all());
            
            // Validar os dados de entrada
            $validated = $request->validate([
                'age' => 'required|integer|min:18',
                'occupation' => 'required|string|max:100',
                'income' => 'required|numeric|min:0',
                'health_conditions' => 'present|array',
                'lifestyle_factors' => 'present|array',
                'family_history' => 'present|array',
            ]);

            // Garantir que os arrays existam mesmo que vazios
            $validated['health_conditions'] = $validated['health_conditions'] ?? [];
            $validated['lifestyle_factors'] = $validated['lifestyle_factors'] ?? [];
            $validated['family_history'] = $validated['family_history'] ?? [];

            Log::info('Dados validados:', $validated);

            // Verificar se temos as configurações necessárias
            if (!$this->config || !isset($this->config['risk_analysis'])) {
                Log::error('Configurações não encontradas');
                throw new \Exception('Erro nas configurações do sistema');
            }

            // Calcular valores baseados na análise
            $riskScore = $this->calculateRiskScore($validated);
            Log::info('Risk Score calculado:', ['score' => $riskScore]);

            $suggestedCoverage = $this->calculateSuggestedCoverage($validated['income'], $riskScore);
            Log::info('Cobertura sugerida:', ['coverage' => $suggestedCoverage]);

            $monthlyPremium = $this->calculateMonthlyPremium($suggestedCoverage, $riskScore, $validated['age']);
            Log::info('Prêmio mensal calculado:', ['premium' => $monthlyPremium]);

            // Tentar obter análise detalhada da IA
            try {
                $prompt = $this->buildPrompt($validated);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em análise de risco para seguros de vida. Forneça análises profissionais e detalhadas, mantendo um tom acessível e explicativo.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]);

                $analysis = $this->processResponse($result->choices[0]->message->content);
                Log::info('Resposta da OpenAI recebida com sucesso');
            } catch (OpenAIErrorException $e) {
                Log::error('Erro na chamada da OpenAI: ' . $e->getMessage());
                // Fallback para análise simplificada
                $analysis = "Baseado nos dados fornecidos, foi realizada uma análise do seu perfil de risco para seguro de vida. ";
                $analysis .= "Seu score de risco é de {$riskScore}%, o que indica um nível " . 
                            ($riskScore < 30 ? "baixo" : ($riskScore < 70 ? "médio" : "alto")) . " de risco. ";
                
                if (!empty($validated['health_conditions'])) {
                    $analysis .= "Foram identificadas condições de saúde que podem impactar o risco: " . 
                               implode(", ", $validated['health_conditions']) . ". ";
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'risk_score' => $riskScore,
                    'risk_comparison' => $this->getRiskComparison($riskScore, $validated['age']),
                    'suggested_coverage' => $this->formatCurrency($suggestedCoverage),
                    'monthly_premium' => $this->formatCurrency($monthlyPremium),
                    'detailed_analysis' => $analysis
                ]
            ];

            Log::info('Resposta:', $response);
            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação: ' . $e->getMessage());
            Log::error('Detalhes:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro na análise do seguro de vida: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    private function buildPrompt($data)
    {
        $healthConditions = !empty($data['health_conditions']) ? implode(", ", $data['health_conditions']) : "nenhuma condição reportada";
        $lifestyleFactors = !empty($data['lifestyle_factors']) ? implode(", ", $data['lifestyle_factors']) : "nenhum fator reportado";
        $familyHistory = !empty($data['family_history']) ? implode(", ", $data['family_history']) : "nenhum histórico reportado";

        return <<<EOT
        Por favor, analise o perfil de risco para seguro de vida com base nos seguintes dados:

        PERFIL DO CLIENTE:
        - Idade: {$data['age']} anos
        - Ocupação: {$data['occupation']}
        - Renda Anual: {$this->formatCurrency($data['income'])}

        FATORES DE SAÚDE:
        - Condições de Saúde: {$healthConditions}
        - Fatores de Estilo de Vida: {$lifestyleFactors}
        - Histórico Familiar: {$familyHistory}

        Por favor, forneça uma análise detalhada incluindo:
        1. Avaliação geral do perfil de risco
        2. Principais fatores que influenciam o risco
        3. Recomendações específicas para melhorar o perfil
        4. Considerações sobre a cobertura ideal
        5. Sugestões para mitigação de riscos identificados

        Mantenha um tom profissional mas acessível, e forneça explicações claras para suas conclusões.
        EOT;
    }

    private function processResponse($content)
    {
        // Limpar e formatar a resposta da API
        $content = trim($content);
        $content = str_replace("\n\n", "\n", $content);
        return $content;
    }

    private function calculateRiskScore(array $data): float
    {
        $baseScore = 50.0;
        
        // Ajuste por idade
        $ageRisk = ($data['age'] - 18) * 0.5;
        $baseScore += $ageRisk;

        // Ajuste por condições de saúde
        $healthConditionsCount = count($data['health_conditions']);
        $baseScore += ($healthConditionsCount * 5);

        // Ajuste por histórico familiar
        $familyHistoryCount = count($data['family_history']);
        $baseScore += ($familyHistoryCount * 3);

        // Ajuste por fatores de estilo de vida
        $lifestyleFactorsCount = count($data['lifestyle_factors']);
        $baseScore += ($lifestyleFactorsCount * 2);

        // Limitar o score entre 0 e 100
        return max(0, min(100, $baseScore));
    }

    private function calculateSuggestedCoverage(float $income, float $riskScore): float
    {
        // Base: 10x a renda anual
        $baseCoverage = $income * 12 * 10;
        
        // Ajuste baseado no score de risco
        $riskMultiplier = 1 + ((100 - $riskScore) / 100);
        
        return $baseCoverage * $riskMultiplier;
    }

    private function calculateMonthlyPremium(float $coverage, float $riskScore, int $age): float
    {
        // Base: 0.1% do valor da cobertura
        $basePremium = $coverage * 0.001;
        
        // Ajuste por idade
        $ageMultiplier = 1 + (($age - 18) * 0.02);
        
        // Ajuste por risco
        $riskMultiplier = 1 + ($riskScore / 100);
        
        return $basePremium * $ageMultiplier * $riskMultiplier;
    }

    private function getRiskComparison(float $riskScore, int $age): string
    {
        if ($riskScore < 30) {
            return "Seu risco está abaixo da média para sua faixa etária";
        } elseif ($riskScore < 70) {
            return "Seu risco está dentro da média para sua faixa etária";
        } else {
            return "Seu risco está acima da média para sua faixa etária";
        }
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
} 