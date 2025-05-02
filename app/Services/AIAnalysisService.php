<?php

namespace App\Services;

use App\Models\AIAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class AIAnalysisService
{
    protected $openaiConfig;

    public function __construct()
    {
        $this->openaiConfig = Config::get('services.openai');
        Log::info('OpenAI Config:', $this->openaiConfig);
    }

    public function performAnalysis(array $data)
    {
        try {
            Log::info('Iniciando análise com dados:', $data);
            
            $analysisPrompt = $this->prepareAnalysisPrompt($data);
            Log::info('Prompt preparado:', $analysisPrompt);
            
            $aiResponse = $this->getAIAnalysis($analysisPrompt);
            Log::info('Resposta da OpenAI:', $aiResponse);
            
            $processedResponse = $this->processAIResponse($aiResponse);
            Log::info('Resposta processada:', $processedResponse);
            return $processedResponse;
        } catch (\Exception $e) {
            Log::error('Erro na análise de AI: ' . $e->getMessage());
            Log::error('Stack trace:', ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    protected function prepareAnalysisPrompt(array $data)
    {
        $systemPrompt = <<<EOT
Você é um especialista em análise de seguros de vida com mais de 20 anos de experiência em avaliação de riscos e recomendações de cobertura. Sua expertise inclui:
- Análise detalhada de fatores de risco de saúde
- Avaliação de expectativa de vida baseada em dados estatísticos
- Cálculo de cobertura ideal baseado em renda e dependentes
- Estimativa de prêmios mensais considerando perfil de risco

Formate sua resposta seguindo estritamente esta estrutura:
SCORE_DE_RISCO: [número entre 0-100]
COBERTURA_SUGERIDA: [valor em reais]
PREMIO_MENSAL: [valor em reais]
ANALISE_DETALHADA:
[sua análise detalhada aqui]
RECOMENDACOES:
[suas recomendações específicas aqui]
EOT;

        $userPrompt = $this->formatUserData($data);

        return [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt
                ]
            ]
        ];
    }

    protected function formatUserData(array $data)
    {
        // Formatar condições de saúde para melhor legibilidade
        $healthConditions = implode(", ", $data['health_conditions']);
        $lifestyleFactors = implode(", ", $data['lifestyle_factors']);
        $familyHistory = implode(", ", $data['family_history']);

        return <<<EOT
Por favor, analise o seguinte perfil para seguro de vida:

DADOS PESSOAIS:
- Idade: {$data['age']} anos
- Ocupação: {$data['occupation']}
- Renda Anual: R$ {$data['income']}

SAÚDE:
Condições Atuais: {$healthConditions}

ESTILO DE VIDA:
Fatores: {$lifestyleFactors}

HISTÓRICO FAMILIAR:
Condições: {$familyHistory}

Baseado nestes dados, forneça:
1. Um score de risco de 0 a 100 (onde 0 é risco mínimo e 100 é risco máximo)
2. Valor de cobertura recomendado baseado na renda e perfil
3. Estimativa de prêmio mensal
4. Análise detalhada do perfil
5. Recomendações específicas para melhorar o perfil de risco
EOT;
    }

    protected function getAIAnalysis(array $prompt)
    {
        Log::info('Fazendo chamada para OpenAI...');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiConfig['api_key'],
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->openaiConfig['model'],
            'messages' => $prompt['messages'],
            'temperature' => $this->openaiConfig['temperature'],
            'max_tokens' => (int) $this->openaiConfig['max_tokens']
        ]);

        if (!$response->successful()) {
            Log::error('Erro na chamada da OpenAI:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Erro ao processar análise com IA: ' . $response->body());
        }

        return $response->json();
    }

    protected function processAIResponse($aiResponse)
    {
        $content = $aiResponse['choices'][0]['message']['content'];
        
        // Extrair informações usando regex
        preg_match('/SCORE_DE_RISCO:\s*(\d+)/', $content, $scoreMatches);
        preg_match('/COBERTURA_SUGERIDA:\s*R?\$?\s*([\d,.]+)/', $content, $coverageMatches);
        preg_match('/PREMIO_MENSAL:\s*R?\$?\s*([\d,.]+)/', $content, $premiumMatches);
        
        // Extrair análise detalhada e recomendações
        preg_match('/ANALISE_DETALHADA:(.*?)(?=RECOMENDACOES:)/s', $content, $analysisMatches);
        preg_match('/RECOMENDACOES:(.*?)$/s', $content, $recommendationsMatches);

        // Limpar e formatar valores numéricos
        $riskScore = isset($scoreMatches[1]) ? (float)$scoreMatches[1] : 50.0;
        $coverage = isset($coverageMatches[1]) ? 
            (float)str_replace(['R$', '.', ','], ['', '', '.'], $coverageMatches[1]) : 
            500000.00;
        $premium = isset($premiumMatches[1]) ? 
            (float)str_replace(['R$', '.', ','], ['', '', '.'], $premiumMatches[1]) : 
            150.00;

        // Combinar análise e recomendações
        $fullRecommendation = "ANÁLISE DETALHADA:\n";
        $fullRecommendation .= isset($analysisMatches[1]) ? trim($analysisMatches[1]) : '';
        $fullRecommendation .= "\n\nRECOMENDAÇÕES:\n";
        $fullRecommendation .= isset($recommendationsMatches[1]) ? trim($recommendationsMatches[1]) : '';

        return [
            'risk_score' => $riskScore,
            'recommendation' => $fullRecommendation,
            'suggested_coverage' => $coverage,
            'monthly_premium_estimate' => $premium
        ];
    }
} 