<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;

class DiagnosticoSucessorioController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = Config::get('succession');
    }

    public function analyze(Request $request)
    {
        try {
            Log::info('Recebendo requisição de planejamento sucessório:', $request->all());
            
            // Validar os dados de entrada
            $validated = $request->validate([
                'total_assets' => 'required|numeric|min:0',
                'asset_types' => 'required|array',
                'asset_types.*.type' => 'required|string',
                'asset_types.*.value' => 'required|numeric|min:0',
                'heirs' => 'required|array',
                'heirs.*.name' => 'required|string',
                'heirs.*.age' => 'required|integer|min:0',
                'heirs.*.relationship' => 'required|string',
                'marital_status' => 'required|string',
                'has_will' => 'required|boolean',
                'has_company' => 'required|boolean',
                'company_details' => 'required_if:has_company,true|array',
                'special_conditions' => 'array'
            ]);

            Log::info('Dados validados:', $validated);

            // Calcular métricas do planejamento sucessório
            $complexityScore = $this->calculateComplexityScore($validated);
            $riskScore = $this->calculateRiskScore($validated);
            $taxExposure = $this->calculateTaxExposure($validated);
            
            // Tentar obter análise detalhada da IA
            try {
                $prompt = $this->buildPrompt($validated);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em planejamento sucessório e direito das sucessões. Forneça análises profissionais e detalhadas, mantendo um tom acessível e explicativo.'],
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
                    'complexity_score' => $complexityScore,
                    'risk_score' => $riskScore,
                    'tax_exposure' => $taxExposure,
                    'detailed_analysis' => $analysis['analysis'],
                    'recommendations' => $analysis['recommendations'],
                    'legal_considerations' => $analysis['legal_considerations'],
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
            Log::error('Erro no planejamento sucessório: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação.',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    public function chat(Request $request)
    {
        try {
            Log::info('Recebendo pergunta para o chat:', $request->all());
            
            // Validar os dados de entrada com mensagens personalizadas
            $validated = $request->validate([
                'question' => 'required|string|min:5',
                'context' => 'required|array',
                'context.analysis' => 'required|string',
                'context.recommendations' => 'required|array|min:1',
                'context.recommendations.*' => 'required|string',
                'context.legal_considerations' => 'required|array|min:1',
                'context.legal_considerations.*' => 'required|string',
                'context.next_steps' => 'required|array|min:1',
                'context.next_steps.*' => 'required|string',
                'context.data' => 'required|array',
                'context.data.total_assets' => 'required|numeric|min:0',
                'context.data.heirs' => 'required|array|min:1',
                'context.data.heirs.*.name' => 'required|string',
                'context.data.heirs.*.age' => 'required|integer|min:0',
                'context.data.heirs.*.relationship' => 'required|string',
                'context.data.asset_types' => 'required|array|min:1',
                'context.data.asset_types.*.type' => 'required|string',
                'context.data.asset_types.*.value' => 'required|numeric|min:0'
            ], [
                'question.required' => 'A pergunta é obrigatória',
                'question.min' => 'A pergunta deve ter pelo menos 5 caracteres',
                'context.required' => 'O contexto do diagnóstico é obrigatório',
                'context.array' => 'O contexto deve ser um objeto válido',
                'context.analysis.required' => 'A análise do diagnóstico é obrigatória',
                'context.analysis.string' => 'A análise deve ser um texto',
                'context.recommendations.required' => 'As recomendações são obrigatórias',
                'context.recommendations.array' => 'As recomendações devem ser uma lista',
                'context.recommendations.min' => 'É necessário pelo menos uma recomendação',
                'context.recommendations.*.required' => 'Todas as recomendações devem ser preenchidas',
                'context.recommendations.*.string' => 'As recomendações devem ser textos',
                'context.legal_considerations.required' => 'As considerações legais são obrigatórias',
                'context.legal_considerations.array' => 'As considerações legais devem ser uma lista',
                'context.legal_considerations.min' => 'É necessário pelo menos uma consideração legal',
                'context.legal_considerations.*.required' => 'Todas as considerações legais devem ser preenchidas',
                'context.legal_considerations.*.string' => 'As considerações legais devem ser textos',
                'context.next_steps.required' => 'Os próximos passos são obrigatórios',
                'context.next_steps.array' => 'Os próximos passos devem ser uma lista',
                'context.next_steps.min' => 'É necessário pelo menos um próximo passo',
                'context.next_steps.*.required' => 'Todos os próximos passos devem ser preenchidos',
                'context.next_steps.*.string' => 'Os próximos passos devem ser textos',
                'context.data.required' => 'Os dados do diagnóstico são obrigatórios',
                'context.data.array' => 'Os dados devem ser um objeto válido',
                'context.data.total_assets.required' => 'O valor total do patrimônio é obrigatório',
                'context.data.total_assets.numeric' => 'O valor total do patrimônio deve ser um número',
                'context.data.total_assets.min' => 'O valor total do patrimônio deve ser maior ou igual a zero',
                'context.data.heirs.required' => 'A lista de herdeiros é obrigatória',
                'context.data.heirs.array' => 'Os herdeiros devem ser uma lista',
                'context.data.heirs.min' => 'É necessário pelo menos um herdeiro',
                'context.data.heirs.*.name.required' => 'O nome do herdeiro é obrigatório',
                'context.data.heirs.*.name.string' => 'O nome do herdeiro deve ser um texto',
                'context.data.heirs.*.age.required' => 'A idade do herdeiro é obrigatória',
                'context.data.heirs.*.age.integer' => 'A idade do herdeiro deve ser um número inteiro',
                'context.data.heirs.*.age.min' => 'A idade do herdeiro deve ser maior ou igual a zero',
                'context.data.heirs.*.relationship.required' => 'O parentesco do herdeiro é obrigatório',
                'context.data.heirs.*.relationship.string' => 'O parentesco do herdeiro deve ser um texto',
                'context.data.asset_types.required' => 'A lista de bens é obrigatória',
                'context.data.asset_types.array' => 'Os bens devem ser uma lista',
                'context.data.asset_types.min' => 'É necessário pelo menos um bem',
                'context.data.asset_types.*.type.required' => 'O tipo do bem é obrigatório',
                'context.data.asset_types.*.type.string' => 'O tipo do bem deve ser um texto',
                'context.data.asset_types.*.value.required' => 'O valor do bem é obrigatório',
                'context.data.asset_types.*.value.numeric' => 'O valor do bem deve ser um número',
                'context.data.asset_types.*.value.min' => 'O valor do bem deve ser maior ou igual a zero'
            ]);

            Log::info('Dados validados:', $validated);

            try {
                $prompt = $this->buildChatPrompt($validated['question'], $validated['context']);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'Você é um especialista em planejamento sucessório e direito das sucessões. Forneça respostas profissionais e detalhadas, mantendo um tom acessível e explicativo. Use o contexto fornecido para personalizar suas respostas.'
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]);

                $response = $result->choices[0]->message->content;
                Log::info('Resposta da OpenAI recebida com sucesso');

                return response()->json([
                    'success' => true,
                    'data' => [
                        'answer' => $response
                    ]
                ]);

            } catch (OpenAIErrorException $e) {
                Log::error('Erro na chamada da OpenAI: ' . $e->getMessage());
                throw new \Exception('Não foi possível processar sua pergunta no momento. Por favor, tente novamente em alguns instantes.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação no chat:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos para o chat',
                'errors' => $e->errors(),
                'debug_info' => [
                    'received_data' => $request->all()
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro no chat sucessório: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua pergunta.',
                'debug_message' => $e->getMessage(),
                'debug_info' => [
                    'received_data' => $request->all()
                ]
            ], 500);
        }
    }

    private function calculateComplexityScore($data)
    {
        $score = 50; // Base score

        // Fatores que aumentam a complexidade
        if ($data['has_company']) $score += 20;
        if (count($data['heirs']) > 2) $score += 10;
        if ($data['total_assets'] > 1000000) $score += 15;
        if (count($data['asset_types']) > 3) $score += 10;
        if (!$data['has_will']) $score += 15;
        if (!empty($data['special_conditions'])) $score += 15;

        return min(100, $score);
    }

    private function calculateRiskScore($data)
    {
        $score = 30; // Base score

        // Fatores de risco
        if (!$data['has_will']) $score += 25;
        if ($data['has_company'] && empty($data['company_details']['succession_plan'])) $score += 20;
        if (count($data['heirs']) > 3) $score += 15;
        if ($data['total_assets'] > 5000000) $score += 10;

        return min(100, $score);
    }

    private function calculateTaxExposure($data)
    {
        $totalTaxExposure = 0;
        $totalAssets = $data['total_assets'];

        // Cálculo básico de exposição tributária
        foreach ($data['asset_types'] as $asset) {
            switch ($asset['type']) {
                case 'imoveis':
                    $totalTaxExposure += $asset['value'] * 0.04; // ITCMD médio
                    break;
                case 'investimentos':
                    $totalTaxExposure += $asset['value'] * 0.03;
                    break;
                case 'empresa':
                    $totalTaxExposure += $asset['value'] * 0.05;
                    break;
                default:
                    $totalTaxExposure += $asset['value'] * 0.04;
            }
        }

        return [
            'total' => round($totalTaxExposure, 2),
            'percentage' => round(($totalTaxExposure / $totalAssets) * 100, 2)
        ];
    }

    private function buildPrompt($data)
    {
        $heirsInfo = collect($data['heirs'])
            ->map(fn($heir) => "- {$heir['name']}: {$heir['age']} anos, {$heir['relationship']}")
            ->join("\n");

        $assetsInfo = collect($data['asset_types'])
            ->map(fn($asset) => "- {$asset['type']}: R$ " . number_format($asset['value'], 2, ',', '.'))
            ->join("\n");

        $hasWill = $data['has_will'] ? 'Sim' : 'Não';
        $hasCompany = $data['has_company'] ? 'Sim' : 'Não';

        return "Por favor, analise este cenário de planejamento sucessório:

        PATRIMÔNIO:
        Total: R$ {$data['total_assets']}
        Composição:
        {$assetsInfo}

        HERDEIROS:
        {$heirsInfo}

        INFORMAÇÕES ADICIONAIS:
        - Estado Civil: {$data['marital_status']}
        - Possui testamento: {$hasWill}
        - Possui empresa: {$hasCompany}

        Por favor, forneça:
        1. Análise detalhada da situação sucessória
        2. Principais riscos e pontos de atenção
        3. Recomendações específicas para otimização sucessória
        4. Considerações legais relevantes
        5. Próximos passos recomendados

        Mantenha um tom profissional mas acessível, e forneça explicações claras para suas conclusões.";
    }

    private function processResponse($content)
    {
        $sections = [
            'analysis' => '',
            'recommendations' => [],
            'legal_considerations' => [],
            'next_steps' => []
        ];

        $lines = explode("\n", $content);
        $currentSection = 'analysis';

        foreach ($lines as $line) {
            if (strpos($line, 'Recomendações:') !== false) {
                $currentSection = 'recommendations';
                continue;
            } elseif (strpos($line, 'Considerações Legais:') !== false) {
                $currentSection = 'legal_considerations';
                continue;
            } elseif (strpos($line, 'Próximos Passos:') !== false) {
                $currentSection = 'next_steps';
                continue;
            }

            if ($line = trim($line)) {
                if (in_array($currentSection, ['recommendations', 'legal_considerations', 'next_steps'])) {
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
        $complexityScore = $this->calculateComplexityScore($data);
        $riskScore = $this->calculateRiskScore($data);
        
        return [
            'analysis' => "Com base nos dados fornecidos, seu planejamento sucessório apresenta complexidade {$complexityScore}% e nível de risco {$riskScore}%. " .
                         "É importante considerar a distribuição adequada do patrimônio entre os herdeiros e a proteção dos ativos.",
            'recommendations' => [
                'Considere elaborar um testamento para garantir seus desejos',
                'Documente claramente a propriedade de todos os bens',
                'Consulte um advogado especializado em direito sucessório'
            ],
            'legal_considerations' => [
                'Respeito à legítima dos herdeiros necessários',
                'Procedimentos de inventário',
                'Tributação aplicável (ITCMD)'
            ],
            'next_steps' => [
                'Reunir documentação completa dos bens',
                'Consultar profissionais especializados',
                'Iniciar processo de planejamento formal'
            ]
        ];
    }

    private function buildChatPrompt($question, $context)
    {
        $heirsInfo = collect($context['data']['heirs'])
            ->map(fn($heir) => "- {$heir['name']}: {$heir['age']} anos, {$heir['relationship']}")
            ->join("\n");

        $assetsInfo = collect($context['data']['asset_types'])
            ->map(fn($asset) => "- {$asset['type']}: R$ " . number_format($asset['value'], 2, ',', '.'))
            ->join("\n");

        $recommendations = collect($context['recommendations'])->join("\n- ");
        $legalConsiderations = collect($context['legal_considerations'])->join("\n- ");
        $nextSteps = collect($context['next_steps'])->join("\n- ");

        return "CONTEXTO DO DIAGNÓSTICO SUCESSÓRIO:

        PATRIMÔNIO:
        Total: R$ " . number_format($context['data']['total_assets'], 2, ',', '.') . "
        Composição:
        {$assetsInfo}

        HERDEIROS:
        {$heirsInfo}

        ANÁLISE ANTERIOR:
        {$context['analysis']}

        RECOMENDAÇÕES:
        - {$recommendations}

        CONSIDERAÇÕES LEGAIS:
        - {$legalConsiderations}

        PRÓXIMOS PASSOS:
        - {$nextSteps}

        PERGUNTA DO USUÁRIO:
        {$question}

        Por favor, responda à pergunta do usuário considerando todo o contexto acima. Mantenha um tom profissional mas acessível, e forneça explicações claras baseadas nas informações disponíveis.";
    }
} 