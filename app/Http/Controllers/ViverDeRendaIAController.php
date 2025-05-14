<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;

class ViverDeRendaIAController extends Controller
{
    public function analyze(Request $request)
    {
        try {
            Log::info('Iniciando análise com dados:', $request->all());

            $validated = $request->validate([
                'capital' => 'required|numeric|min:1000',
                'metaRenda' => 'required|numeric|min:100',
                'idade' => 'required|integer|min:18|max:100',
                'idadeMeta' => 'required|integer|min:18|max:100',
                'perfil' => 'required|in:conservador,moderado,arrojado'
            ]);

            try {
                $prompt = $this->buildPrompt($validated);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em planejamento financeiro e independência financeira. Forneça análises profissionais e detalhadas, mantendo um tom acessível e explicativo.'],
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

            $metrics = $this->calculateMetrics($validated);

            $response = [
                'success' => true,
                'data' => [
                    'metrics' => $metrics,
                    'detailed_analysis' => $analysis['analysis'],
                    'recommendations' => $analysis['recommendations'],
                    'considerations' => $analysis['considerations'],
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
            Log::error('Erro na análise: ' . $e->getMessage());
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
            Log::info('Recebendo pergunta para o chat:', [
                'request_data' => $request->all(),
                'request_json' => json_encode($request->all(), JSON_PRETTY_PRINT)
            ]);
            
            // Validar os dados de entrada com mensagens personalizadas
            $validated = $request->validate([
                'question' => 'required|string|min:5',
                'context' => 'required|array',
                'context.analysis' => 'required|string',
                'context.recommendations' => 'required|array|min:1',
                'context.considerations' => 'required|array|min:1',
                'context.next_steps' => 'required|array|min:1',
                'context.data' => 'required|array',
                'context.data.capital' => 'required|numeric|min:1000',
                'context.data.metaRenda' => 'required|numeric|min:100',
                'context.data.idade' => 'required|integer|min:18|max:100',
                'context.data.idadeMeta' => 'required|integer|min:18|max:100',
                'context.data.perfil' => 'required|string|in:conservador,moderado,arrojado'
            ], [
                'question.required' => 'A pergunta é obrigatória',
                'question.min' => 'A pergunta deve ter pelo menos 5 caracteres',
                'context.required' => 'O contexto é obrigatório',
                'context.array' => 'O contexto deve ser um objeto válido',
                'context.analysis.required' => 'A análise é obrigatória no contexto',
                'context.analysis.string' => 'A análise deve ser um texto',
                'context.recommendations.required' => 'As recomendações são obrigatórias',
                'context.recommendations.array' => 'As recomendações devem ser uma lista',
                'context.recommendations.min' => 'Deve haver pelo menos uma recomendação',
                'context.considerations.required' => 'As considerações são obrigatórias',
                'context.considerations.array' => 'As considerações devem ser uma lista',
                'context.considerations.min' => 'Deve haver pelo menos uma consideração',
                'context.next_steps.required' => 'Os próximos passos são obrigatórios',
                'context.next_steps.array' => 'Os próximos passos devem ser uma lista',
                'context.next_steps.min' => 'Deve haver pelo menos um próximo passo',
                'context.data.required' => 'Os dados do diagnóstico são obrigatórios',
                'context.data.array' => 'Os dados do diagnóstico devem ser um objeto válido',
                'context.data.capital.required' => 'O capital é obrigatório',
                'context.data.capital.numeric' => 'O capital deve ser um número',
                'context.data.capital.min' => 'O capital deve ser de pelo menos R$ 1.000,00',
                'context.data.metaRenda.required' => 'A meta de renda é obrigatória',
                'context.data.metaRenda.numeric' => 'A meta de renda deve ser um número',
                'context.data.metaRenda.min' => 'A meta de renda deve ser de pelo menos R$ 100,00',
                'context.data.idade.required' => 'A idade é obrigatória',
                'context.data.idade.integer' => 'A idade deve ser um número inteiro',
                'context.data.idade.min' => 'A idade deve ser pelo menos 18 anos',
                'context.data.idade.max' => 'A idade deve ser no máximo 100 anos',
                'context.data.idadeMeta.required' => 'A idade meta é obrigatória',
                'context.data.idadeMeta.integer' => 'A idade meta deve ser um número inteiro',
                'context.data.idadeMeta.min' => 'A idade meta deve ser pelo menos 18 anos',
                'context.data.idadeMeta.max' => 'A idade meta deve ser no máximo 100 anos',
                'context.data.perfil.required' => 'O perfil de investimento é obrigatório',
                'context.data.perfil.in' => 'O perfil deve ser conservador, moderado ou arrojado'
            ]);

            Log::info('Dados validados com sucesso:', ['validated' => $validated]);

            try {
                $prompt = $this->buildChatPrompt($validated['question'], $validated['context']);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'Você é um especialista em planejamento financeiro e independência financeira. Forneça respostas profissionais e detalhadas, mantendo um tom acessível e explicativo. Use o contexto fornecido para personalizar suas respostas.'
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
                'request_data' => $request->all(),
                'validation_rules' => $request->rules(),
                'debug_backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos para o chat',
                'errors' => $e->errors(),
                'debug_info' => [
                    'received_data' => $request->all(),
                    'validation_rules' => $request->rules()
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro no chat: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua pergunta.',
                'debug_message' => $e->getMessage(),
                'debug_info' => [
                    'received_data' => $request->all(),
                    'stack_trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    private function buildPrompt($data)
    {
        $anosParaMeta = $data['idadeMeta'] - $data['idade'];
        
        return "Análise de Viabilidade Financeira\n\n" .
               "Dados do Investidor:\n" .
               "- Capital inicial disponível: R$ " . number_format($data['capital'], 2, ',', '.') . "\n" .
               "- Meta de renda mensal: R$ " . number_format($data['metaRenda'], 2, ',', '.') . "\n" .
               "- Idade atual: {$data['idade']} anos\n" .
               "- Idade desejada para independência: {$data['idadeMeta']} anos\n" .
               "- Tempo até a meta: {$anosParaMeta} anos\n" .
               "- Perfil de investimento: {$data['perfil']}\n\n" .
               "Por favor, forneça:\n" .
               "1. Análise de Viabilidade\n" .
               "   - Avalie se é possível atingir a meta no prazo\n" .
               "   - Calcule o montante necessário para gerar a renda desejada\n" .
               "   - Considere inflação e rentabilidade realista\n\n" .
               "2. Recomendações\n" .
               "   - Sugestão de alocação de ativos\n" .
               "   - Produtos financeiros recomendados\n" .
               "   - Cronograma de investimentos\n\n" .
               "3. Considerações\n" .
               "   - Cenário conservador (rentabilidade menor)\n" .
               "   - Cenário realista (rentabilidade média)\n" .
               "   - Cenário otimista (rentabilidade maior)\n\n" .
               "4. Próximos Passos\n" .
               "   - Ajustes necessários se a meta não for viável\n" .
               "   - Sugestões para otimizar o plano\n" .
               "   - Considerações sobre riscos e proteção patrimonial";
    }

    private function buildChatPrompt($question, $context)
    {
        $anosParaMeta = $context['data']['idadeMeta'] - $context['data']['idade'];
        
        $recommendations = collect($context['recommendations'])->join("\n- ");
        $considerations = collect($context['considerations'])->join("\n- ");
        $nextSteps = collect($context['next_steps'])->join("\n- ");

        return "CONTEXTO DO DIAGNÓSTICO FINANCEIRO:

        DADOS DO INVESTIDOR:
        - Capital inicial: R$ " . number_format($context['data']['capital'], 2, ',', '.') . "
        - Meta de renda mensal: R$ " . number_format($context['data']['metaRenda'], 2, ',', '.') . "
        - Idade atual: {$context['data']['idade']} anos
        - Idade meta: {$context['data']['idadeMeta']} anos
        - Tempo até a meta: {$anosParaMeta} anos
        - Perfil de investimento: {$context['data']['perfil']}

        ANÁLISE ANTERIOR:
        {$context['analysis']}

        RECOMENDAÇÕES:
        - {$recommendations}

        CONSIDERAÇÕES:
        - {$considerations}

        PRÓXIMOS PASSOS:
        - {$nextSteps}

        PERGUNTA DO USUÁRIO:
        {$question}

        Por favor, responda à pergunta do usuário considerando todo o contexto acima. Mantenha um tom profissional mas acessível, e forneça explicações claras baseadas nas informações disponíveis.";
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

    private function generateFallbackAnalysis($data)
    {
        $anosParaMeta = $data['idadeMeta'] - $data['idade'];
        $montanteNecessario = $data['metaRenda'] * 12 * 33.33; // Regra dos 3% ao ano
        $rentabilidadeEsperada = [
            'conservador' => 0.08,
            'moderado' => 0.10,
            'arrojado' => 0.12
        ][$data['perfil']];
        
        return [
            'analysis' => "Com base nos dados fornecidos, para atingir uma renda mensal de R$ " . 
                         number_format($data['metaRenda'], 2, ',', '.') . 
                         " em {$anosParaMeta} anos, será necessário um montante aproximado de R$ " . 
                         number_format($montanteNecessario, 2, ',', '.') . 
                         ". Considerando seu perfil {$data['perfil']}, a rentabilidade média esperada é de " . 
                         ($rentabilidadeEsperada * 100) . "% ao ano.",
            
            'recommendations' => [
                'Diversificar investimentos de acordo com o perfil de risco',
                'Considerar uma carteira balanceada entre renda fixa e variável',
                'Manter reserva de emergência equivalente a 6-12 meses de despesas'
            ],
            
            'considerations' => [
                'Inflação pode impactar o poder de compra da renda futura',
                'Rentabilidade passada não garante rentabilidade futura',
                'Importante revisar e rebalancear a carteira periodicamente'
            ],
            
            'next_steps' => [
                'Estabelecer um plano de investimentos mensal',
                'Consultar um assessor financeiro para orientação específica',
                'Começar a investir o quanto antes para aproveitar os juros compostos'
            ]
        ];
    }

    private function calculateMetrics($data)
    {
        $anosParaMeta = $data['idadeMeta'] - $data['idade'];
        $montanteNecessario = $data['metaRenda'] * 12 * 33.33; // Regra dos 3% ao ano
        $gap = $montanteNecessario - $data['capital'];
        $aporteNecessario = $gap / ($anosParaMeta * 12);
        
        $rentabilidadeBase = [
            'conservador' => 0.08,
            'moderado' => 0.10,
            'arrojado' => 0.12
        ][$data['perfil']];

        return [
            'montante_necessario' => round($montanteNecessario, 2),
            'gap_patrimonial' => round($gap, 2),
            'aporte_mensal_sugerido' => round($aporteNecessario, 2),
            'rentabilidade_alvo' => round($rentabilidadeBase * 100, 2),
            'tempo_meta' => $anosParaMeta,
            'viabilidade_score' => $this->calculateViabilityScore($data, $montanteNecessario, $aporteNecessario)
        ];
    }

    private function calculateViabilityScore($data, $montanteNecessario, $aporteNecessario)
    {
        $score = 100;
        
        // Reduz o score baseado na diferença entre capital atual e necessário
        $capitalRatio = $data['capital'] / $montanteNecessario;
        if ($capitalRatio < 0.1) $score -= 30;
        elseif ($capitalRatio < 0.3) $score -= 20;
        elseif ($capitalRatio < 0.5) $score -= 10;

        // Reduz o score baseado no aporte necessário vs renda mensal
        $rendaMensal = $data['metaRenda']; // Usando meta de renda como proxy
        if ($aporteNecessario > $rendaMensal * 0.5) $score -= 30;
        elseif ($aporteNecessario > $rendaMensal * 0.3) $score -= 20;
        elseif ($aporteNecessario > $rendaMensal * 0.2) $score -= 10;

        // Ajusta baseado no tempo disponível
        $anosParaMeta = $data['idadeMeta'] - $data['idade'];
        if ($anosParaMeta < 5) $score -= 20;
        elseif ($anosParaMeta < 10) $score -= 10;
        elseif ($anosParaMeta > 30) $score += 10;

        return max(0, min(100, $score));
    }
} 