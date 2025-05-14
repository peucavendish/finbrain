<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use OpenAI\Exceptions\ErrorException as OpenAIErrorException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class DiagnosticoCarteiraController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = Config::get('portfolio.analysis');
    }

    public function analyze(Request $request)
    {
        try {
            Log::info('Recebendo requisição de análise de carteira:', $request->all());
            
            // Validar os dados de entrada
            $validated = $request->validate([
                'current_portfolio' => 'required|array',
                'investment_profile' => 'required|string|in:conservador,moderado,arrojado',
                'investment_horizon' => 'required|integer|min:1',
                'monthly_contribution' => 'required|numeric|min:0',
                'total_equity' => 'required|numeric|min:0',
                'financial_goals' => 'present|array',
                'risk_tolerance' => 'required|integer|min:1|max:10',
            ]);

            // Garantir que os arrays existam mesmo que vazios
            $validated['current_portfolio'] = $validated['current_portfolio'] ?? [];
            $validated['financial_goals'] = $validated['financial_goals'] ?? [];

            Log::info('Dados validados:', $validated);

            // Verificar configurações
            if (!$this->config || !isset($this->config['portfolio_analysis'])) {
                Log::error('Configurações não encontradas');
                throw new \Exception('Erro nas configurações do sistema');
            }

            // Calcular métricas da carteira
            $portfolioMetrics = $this->calculatePortfolioMetrics($validated);
            Log::info('Métricas da carteira calculadas:', ['metrics' => $portfolioMetrics]);

            $diversificationScore = $this->calculateDiversificationScore($validated['current_portfolio']);
            Log::info('Score de diversificação calculado:', ['score' => $diversificationScore]);

            $riskReturnRatio = $this->calculateRiskReturnRatio($validated);
            Log::info('Relação risco-retorno calculada:', ['ratio' => $riskReturnRatio]);

            // Análise da IA
            try {
                $prompt = $this->buildPrompt($validated, $portfolioMetrics);
                Log::info('Enviando prompt para OpenAI:', ['prompt' => $prompt]);

                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um especialista em análise de investimentos e mercado financeiro brasileiro. Forneça análises profissionais e recomendações detalhadas, considerando o cenário macroeconômico atual do Brasil.'],
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
                $analysis = "Com base nos dados fornecidos, foi realizada uma análise da sua carteira de investimentos. ";
                $analysis .= "Seu score de diversificação é {$diversificationScore}%, o que indica um nível " . 
                            ($diversificationScore < 30 ? "baixo" : ($diversificationScore < 70 ? "médio" : "alto")) . " de diversificação. ";
                $analysis .= "Considerando seu perfil {$validated['investment_profile']}, recomendamos ajustes na alocação de ativos.";
            }

            $recommendations = $this->generateRecommendations($validated, $portfolioMetrics);

            $response = [
                'success' => true,
                'data' => [
                    'portfolio_metrics' => $portfolioMetrics,
                    'diversification_score' => $diversificationScore,
                    'risk_return_ratio' => $riskReturnRatio,
                    'market_alignment' => $this->assessMarketAlignment($validated['current_portfolio']),
                    'recommendations' => $recommendations,
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
            Log::error('Erro na análise da carteira: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.',
                'debug_message' => $e->getMessage()
            ], 500);
        }
    }

    public function analyzePdf(Request $request)
    {
        try {
            // Log detalhado da requisição
            Log::info('Dados da requisição:', [
                'all' => $request->all(),
                'files' => $request->allFiles(),
                'headers' => $request->headers->all()
            ]);

            // Validação inicial dos dados
            if (!$request->hasFile('pdf')) {
                throw new \Exception('Arquivo PDF não encontrado na requisição');
            }

            // Extrair e validar os dados do formulário
            $investmentProfile = $request->get('investment_profile');
            $investmentHorizon = $request->get('investment_horizon');

            Log::info('Dados extraídos:', [
                'profile' => $investmentProfile,
                'horizon' => $investmentHorizon
            ]);

            // Validação formal
            $validated = $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:10240',
                'investment_profile' => 'required|string|in:conservador,moderado,arrojado',
                'investment_horizon' => 'required|integer|min:1'
            ]);

            Log::info('Dados validados:', $validated);

            // Processar o PDF
            $pdf = $request->file('pdf');
            $parser = new Parser();
            $parsedPdf = $parser->parseFile($pdf->path());
            $text = $parsedPdf->getText();

            Log::info('PDF processado:', [
                'text_length' => strlen($text),
                'preview' => substr($text, 0, 200)
            ]);

            // Extrair dados do portfólio
            $portfolioData = $this->extractPortfolioFromPdf($text);
            Log::info('Dados extraídos do PDF:', $portfolioData);
            
            // Criar dados completos para análise
            $analysisData = [
                'current_portfolio' => $portfolioData['current_portfolio'],
                'total_equity' => $portfolioData['total_equity'],
                'monthly_contribution' => $portfolioData['monthly_contribution'],
                'investment_profile' => $investmentProfile,
                'investment_horizon' => (int)$investmentHorizon,
                'risk_tolerance' => 5, // Valor padrão
                'financial_goals' => ['crescimento_patrimonio'] // Valor padrão
            ];

            Log::info('Dados completos para análise:', $analysisData);

            // Realizar análise
            $analysis = $this->performAnalysis($analysisData);

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro no processamento:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractPortfolioFromPdf($text)
    {
        try {
            Log::info('Iniciando extração detalhada do PDF', ['text_length' => strlen($text)]);
            
            $portfolio = [];
            $totalEquity = 0;
            $detailedAssets = [];
            
            // Padrões detalhados para cada tipo de ativo
            $patterns = [
                'renda_fixa' => [
                    'cdb' => '/CDB\s+(?:do\s+)?([A-Za-z\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'lci' => '/LCI\s+(?:do\s+)?([A-Za-z\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'lca' => '/LCA\s+(?:do\s+)?([A-Za-z\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'tesouro' => '/Tesouro\s+([A-Za-zÀ-ú\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'debentures' => '/Debênture[s]?\s+([A-Za-z\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'poupanca' => '/Poupança\s+([A-Za-z\s]+)?[\s\-]*R?\$?\s*([\d\.,]+)/'
                ],
                'renda_variavel' => [
                    'acoes' => '/([A-Z]{4}\d{1,2})\s*[-\s]*R?\$?\s*([\d\.,]+)/',
                    'fii' => '/([A-Z]{4}11)\s*[-\s]*R?\$?\s*([\d\.,]+)/',
                    'etf' => '/(BOVA|SMAL|IVVB)11\s*[-\s]*R?\$?\s*([\d\.,]+)/'
                ],
                'fundos' => [
                    'fundo_di' => '/FI[DC]?\s+(?:DE\s+)?(?:INVESTIMENTO\s+)?(?:EM\s+)?DI\s+([A-Za-zÀ-ú\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'fundo_rf' => '/FI[DC]?\s+(?:DE\s+)?(?:INVESTIMENTO\s+)?(?:EM\s+)?RENDA\s+FIXA\s+([A-Za-zÀ-ú\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'fundo_acoes' => '/FI[DC]?\s+(?:DE\s+)?(?:INVESTIMENTO\s+)?(?:EM\s+)?AÇÕES\s+([A-Za-zÀ-ú\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/',
                    'fundo_multi' => '/FI[DC]?\s+(?:DE\s+)?(?:INVESTIMENTO\s+)?MULTIMERCADO\s+([A-Za-zÀ-ú\s]+)[\s\-]*R?\$?\s*([\d\.,]+)/'
                ],
                'alternativos' => [
                    'cripto' => '/(Bitcoin|Ethereum|BTC|ETH)\s*[-\s]*R?\$?\s*([\d\.,]+)/',
                    'ouro' => '/Ouro\s+([A-Za-z\s]+)?[\s\-]*R?\$?\s*([\d\.,]+)/'
                ]
            ];

            // Primeiro passo: extrair todos os ativos e calcular o total
            foreach ($patterns as $type => $subpatterns) {
                foreach ($subpatterns as $subtype => $pattern) {
                    if (preg_match_all($pattern, $text, $matches)) {
                        for ($i = 0; $i < count($matches[1]); $i++) {
                            $name = trim($matches[1][$i]);
                            // Limpar e converter o valor para float
                            $valueStr = trim($matches[2][$i]);
                            $valueStr = str_replace(['.', ','], ['', '.'], $valueStr);
                            $value = (float) $valueStr;
                            
                            if ($value > 0) {
                                Log::info("Ativo encontrado:", [
                                    'type' => $type,
                                    'subtype' => $subtype,
                                    'name' => $name,
                                    'value' => $value,
                                    'original_value' => $matches[2][$i]
                                ]);

                                $detailedAssets[] = [
                                    'type' => $type,
                                    'subtype' => $subtype,
                                    'name' => $name,
                                    'value' => $value
                                ];
                                $totalEquity += $value;
                            }
                        }
                    }
                }
            }

            Log::info("Total da carteira calculado:", ['total_equity' => $totalEquity]);

            // Segundo passo: calcular totais por tipo de ativo
            $typePortfolio = [];
            foreach ($detailedAssets as $asset) {
                if (!isset($typePortfolio[$asset['type']])) {
                    $typePortfolio[$asset['type']] = 0;
                }
                $typePortfolio[$asset['type']] += $asset['value'];
            }

            // Terceiro passo: calcular percentuais e formatar portfólio
            $formattedPortfolio = [];
            foreach ($typePortfolio as $type => $value) {
                $percentage = ($value / $totalEquity) * 100;
                $formattedPortfolio[] = [
                    'type' => $type,
                    'value' => $value,
                    'percentage' => $percentage
                ];

                Log::info("Percentual calculado para tipo de ativo:", [
                    'type' => $type,
                    'value' => $value,
                    'percentage' => $percentage,
                    'total_equity' => $totalEquity
                ]);
            }

            // Calcular aporte mensal estimado (10% do patrimônio total dividido por 12)
            $monthlyContribution = $totalEquity * 0.10 / 12;

            // Atualizar os percentuais nos ativos detalhados
            foreach ($detailedAssets as &$asset) {
                $asset['percentage'] = ($asset['value'] / $totalEquity) * 100;
            }

            $result = [
                'current_portfolio' => $formattedPortfolio,
                'detailed_assets' => $detailedAssets,
                'total_equity' => $totalEquity,
                'monthly_contribution' => $monthlyContribution,
                'portfolio_summary' => $typePortfolio
            ];

            Log::info('Extração do PDF concluída:', [
                'total_assets' => count($detailedAssets),
                'total_equity' => $totalEquity,
                'portfolio_distribution' => $typePortfolio
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erro na extração do PDF:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro ao processar o PDF: ' . $e->getMessage());
        }
    }

    private function performAnalysis($data)
    {
        try {
            Log::info('Iniciando análise detalhada com IA', ['data' => $data]);

            // Calcular métricas básicas
            $metrics = $this->calculatePortfolioMetrics($data);
            Log::info('Métricas calculadas:', ['metrics' => $metrics]);

            // Construir prompt para a OpenAI
            $prompt = $this->buildPrompt($data, $metrics);
            Log::info('Prompt construído para análise:', ['prompt' => $prompt]);

            // Fazer a chamada para a OpenAI
            try {
                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Você é um especialista em análise de investimentos no mercado brasileiro, com profundo conhecimento em análise fundamentalista, técnica e macroeconômica. Forneça análises profissionais e recomendações detalhadas, considerando cada ativo individual e o cenário atual.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000
                ]);

                $analysis = $this->processResponse($result->choices[0]->message->content);
                Log::info('Análise da IA recebida com sucesso');
            } catch (OpenAIErrorException $e) {
                Log::error('Erro na chamada da OpenAI:', [
                    'error' => $e->getMessage(),
                    'prompt' => $prompt
                ]);
                throw $e;
            }

            // Calcular score de diversificação
            $diversificationScore = $this->calculateDiversificationScore($data['current_portfolio']);
            Log::info('Score de diversificação:', ['score' => $diversificationScore]);

            // Calcular relação risco/retorno
            $riskReturnRatio = $this->calculateRiskReturnRatio($data);
            Log::info('Relação risco/retorno:', ['ratio' => $riskReturnRatio]);

            // Avaliar alinhamento com mercado
            $marketAlignment = $this->evaluateMarketAlignment($data['current_portfolio']);
            Log::info('Alinhamento com mercado:', ['alignment' => $marketAlignment]);

            // Extrair recomendações específicas da análise da IA
            $recommendations = $this->extractRecommendations($analysis);
            Log::info('Recomendações extraídas');

            return [
                'diversification_score' => $diversificationScore,
                'risk_return_ratio' => $riskReturnRatio,
                'market_alignment' => $marketAlignment,
                'portfolio_metrics' => $metrics,
                'detailed_analysis' => $analysis,
                'recommendations' => $recommendations
            ];

        } catch (\Exception $e) {
            Log::error('Erro na análise:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    private function extractRecommendations($analysis)
    {
        // Extrair recomendações específicas do texto da análise
        $recommendations = [];
        
        // Dividir o texto em parágrafos
        $paragraphs = explode("\n", $analysis);
        
        $isRecommendation = false;
        $currentRecommendation = '';
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            
            // Identificar seções de recomendações
            if (preg_match('/^(Recomendação|Sugestão|Ação recomendada):/i', $paragraph)) {
                if (!empty($currentRecommendation)) {
                    $recommendations[] = trim($currentRecommendation);
                }
                $isRecommendation = true;
                $currentRecommendation = $paragraph;
            }
            // Identificar pontos de ação específicos
            elseif (preg_match('/^[\d\-\•]\s+(.+)$/', $paragraph, $matches)) {
                $recommendations[] = trim($matches[1]);
            }
            // Continuar recomendação atual
            elseif ($isRecommendation && !empty($paragraph)) {
                $currentRecommendation .= "\n" . $paragraph;
            }
        }
        
        // Adicionar última recomendação
        if (!empty($currentRecommendation)) {
            $recommendations[] = trim($currentRecommendation);
        }
        
        // Filtrar e limpar recomendações
        $recommendations = array_filter($recommendations, function($rec) {
            return strlen($rec) > 10; // Remover recomendações muito curtas
        });
        
        // Formatar recomendações
        $recommendations = array_map(function($rec) {
            // Adicionar ícones ou formatação especial
            if (stripos($rec, 'atenção') !== false || stripos($rec, 'risco') !== false) {
                $rec = '⚠️ ' . $rec;
            } elseif (stripos($rec, 'oportunidade') !== false || stripos($rec, 'sugestão') !== false) {
                $rec = '💡 ' . $rec;
            }
            return $rec;
        }, $recommendations);

        return array_values($recommendations); // Reindexar array
    }

    private function processResponse($content)
    {
        // Formatar a resposta para melhor apresentação
        $content = trim($content);
        
        // Adicionar emojis para pontos importantes
        $content = preg_replace('/(!importante|atenção|cuidado|risco):/i', '⚠️ $1:', $content);
        $content = preg_replace('/(oportunidade|sugestão):/i', '💡 $1:', $content);
        
        // Destacar métricas e valores
        $content = preg_replace('/(\d+(?:,\d+)?%)/', '<strong class="metric-highlight">$1</strong>', $content);
        $content = preg_replace('/(R\$ \d+(?:,\d+)?)/', '<strong class="metric-value">$1</strong>', $content);
        
        // Formatar títulos de seções
        $content = preg_replace('/(^|\n)([\d\-\•]\. [^\n]+)/', '$1<h4 class="analysis-section-title">$2</h4>', $content);
        
        // Adicionar classes para pontos de atenção
        $content = preg_replace(
            '/\b(ATENÇÃO|RISCO|IMPORTANTE):/i',
            '<span class="attention-point">$1:</span>',
            $content
        );
        
        return $content;
    }

    private function calculatePortfolioMetrics(array $data): array
    {
        // Simulação de cálculos de métricas de portfólio
        return [
            'volatility' => $this->calculateVolatility($data['current_portfolio']),
            'expected_return' => $this->calculateExpectedReturn($data),
            'sharpe_ratio' => $this->calculateSharpeRatio($data),
            'beta' => $this->calculateBeta($data['current_portfolio']),
            'alpha' => $this->calculateAlpha($data)
        ];
    }

    private function calculateVolatility(array $portfolio): float
    {
        // Implementação simplificada - deve ser substituída por cálculo real
        $totalRisk = 0;
        foreach ($portfolio as $asset) {
            $totalRisk += isset($asset['volatility']) ? $asset['volatility'] : 5;
        }
        return $totalRisk / count($portfolio);
    }

    private function calculateExpectedReturn(array $data): float
    {
        // Base: perfil de risco + horizonte de investimento
        $baseReturn = match($data['investment_profile']) {
            'conservador' => 8,
            'moderado' => 12,
            'arrojado' => 16,
            default => 10
        };

        return $baseReturn * (1 + ($data['investment_horizon'] / 10));
    }

    private function calculateSharpeRatio(array $data): float
    {
        $riskFreeRate = 11.25; // Taxa Selic atual
        $expectedReturn = $this->calculateExpectedReturn($data);
        $volatility = $this->calculateVolatility($data['current_portfolio']);
        
        return ($expectedReturn - $riskFreeRate) / $volatility;
    }

    private function calculateDiversificationScore(array $portfolio): float
    {
        try {
            if (empty($portfolio)) {
                Log::warning('Portfolio vazio recebido em calculateDiversificationScore');
                return 0.0;
            }

            Log::info('Calculando score de diversificação para portfolio:', ['portfolio' => $portfolio]);

            $assetTypes = [];
            $totalValue = 0;

            // Primeiro loop: calcular o valor total e validar os dados
            foreach ($portfolio as $asset) {
                if (!isset($asset['type']) || !isset($asset['value'])) {
                    Log::warning('Asset com dados incompletos', ['asset' => $asset]);
                    continue;
                }
                $totalValue += $asset['value'];
                $assetTypes[$asset['type']] = ($assetTypes[$asset['type']] ?? 0) + $asset['value'];
            }

            if ($totalValue == 0) {
                Log::warning('Valor total do portfolio é zero');
                return 0.0;
            }

            // Converter valores absolutos em percentuais
            foreach ($assetTypes as $type => $value) {
                $assetTypes[$type] = ($value / $totalValue) * 100;
            }

            Log::info('Distribuição por tipo de ativo:', $assetTypes);

            // Pontuação base de diversificação
            $score = 50;

            // Bônus por diversificação entre classes de ativos
            $numAssetTypes = count($assetTypes);
            $score += min($numAssetTypes * 10, 30); // Máximo de 30 pontos por número de classes

            // Penalidade por concentração excessiva
            foreach ($assetTypes as $percentage) {
                if ($percentage > 50) {
                    $score -= min(($percentage - 50) / 2, 20); // Máximo de 20 pontos de penalidade
                }
            }

            // Garantir que o score esteja entre 0 e 100
            $score = max(0, min(100, $score));

            Log::info('Score de diversificação calculado:', ['score' => $score]);
            return (float) $score;

        } catch (\Exception $e) {
            Log::error('Erro no cálculo do score de diversificação:', [
                'error' => $e->getMessage(),
                'portfolio' => $portfolio
            ]);
            return 0.0;
        }
    }

    private function calculateRiskReturnRatio($data): float
    {
        try {
            if (empty($data['current_portfolio'])) {
                Log::warning('Portfolio vazio em calculateRiskReturnRatio');
                return 0.0;
            }

            if (!isset($data['investment_profile'])) {
                Log::warning('Perfil de investimento não definido em calculateRiskReturnRatio');
                return 0.0;
            }

            $expectedReturn = $this->calculateExpectedReturn($data);
            $volatility = $this->calculateVolatility($data['current_portfolio']);

            if ($volatility == 0) {
                Log::warning('Volatilidade zero em calculateRiskReturnRatio');
                return 0.0;
            }

            return $expectedReturn / $volatility;
        } catch (\Exception $e) {
            Log::error('Erro no cálculo de risco/retorno:', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return 0.0;
        }
    }

    private function calculateBeta(array $portfolio): float
    {
        // Implementação simplificada - deve ser substituída por cálculo real
        return 1.0;
    }

    private function calculateAlpha(array $data): float
    {
        // Implementação simplificada - deve ser substituída por cálculo real
        return 0.5;
    }

    private function assessMarketAlignment(array $portfolio): array
    {
        // Análise simplificada de alinhamento com o mercado
        return [
            'alignment_score' => 75,
            'market_conditions' => 'favorável',
            'suggested_adjustments' => [
                'Aumentar exposição em renda fixa pós-fixada',
                'Reduzir exposição em small caps',
                'Manter posição em ativos atrelados à inflação'
            ]
        ];
    }

    private function generateRecommendations(array $data, array $metrics): array
    {
        $recommendations = [];

        // Recomendações baseadas no perfil
        if ($data['investment_profile'] === 'conservador') {
            $recommendations[] = [
                'type' => 'allocation',
                'suggestion' => 'Aumentar alocação em títulos públicos pós-fixados',
                'reason' => 'Maior previsibilidade e segurança'
            ];
        }

        // Recomendações baseadas no horizonte
        if ($data['investment_horizon'] > 5) {
            $recommendations[] = [
                'type' => 'diversification',
                'suggestion' => 'Considerar exposição internacional',
                'reason' => 'Proteção contra riscos locais'
            ];
        }

        // Recomendações baseadas nas métricas
        if ($metrics['sharpe_ratio'] < 0.5) {
            $recommendations[] = [
                'type' => 'risk_adjustment',
                'suggestion' => 'Revisar alocação de ativos de maior risco',
                'reason' => 'Melhorar relação risco-retorno'
            ];
        }

        return $recommendations;
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    private function generateDetailedAnalysis($data)
    {
        $profile = $data['investment_profile'];
        $horizon = $data['investment_horizon'];
        $portfolio = $data['current_portfolio'];

        $analysis = [];

        // Análise do perfil e horizonte
        $analysis[] = "Seu perfil {$profile} com horizonte de {$horizon} anos sugere uma estratégia " . 
                     ($profile === 'conservador' ? 'mais focada em preservação de capital' : 
                      ($profile === 'arrojado' ? 'mais focada em crescimento' : 'balanceada')) . ".";

        // Análise da distribuição atual
        $typeDistribution = [];
        foreach ($portfolio as $asset) {
            $typeDistribution[$asset['type']] = ($typeDistribution[$asset['type']] ?? 0) + $asset['percentage'];
        }

        foreach ($typeDistribution as $type => $percentage) {
            $analysis[] = "• " . ucfirst(str_replace('_', ' ', $type)) . ": {$percentage}% do portfólio";
        }

        // Análise de concentração
        $highConcentration = array_filter($portfolio, function($asset) {
            return $asset['percentage'] > 30;
        });

        if ($highConcentration) {
            $analysis[] = "Atenção: Identificamos concentração elevada em alguns ativos:";
            foreach ($highConcentration as $asset) {
                $analysis[] = "- " . ucfirst(str_replace('_', ' ', $asset['type'])) . 
                             " representa {$asset['percentage']}% do portfólio";
            }
        }

        return implode("\n", $analysis);
    }

    private function evaluateMarketAlignment($portfolio)
    {
        if (empty($portfolio)) {
            Log::warning('Portfolio vazio recebido em evaluateMarketAlignment');
            return 0.0;
        }

        // Simples avaliação baseada em distribuição ideal de mercado
        $idealDistribution = [
            'renda_fixa' => 40,
            'renda_variavel' => 30,
            'fundos' => 20,
            'alternativos' => 10
        ];

        $totalDeviation = 0;
        $currentDistribution = [];
        
        foreach ($portfolio as $asset) {
            if (!isset($asset['type']) || !isset($asset['percentage'])) {
                continue;
            }
            $currentDistribution[$asset['type']] = ($currentDistribution[$asset['type']] ?? 0) + $asset['percentage'];
        }

        if (empty($currentDistribution)) {
            return 0.0;
        }

        foreach ($idealDistribution as $type => $ideal) {
            $current = $currentDistribution[$type] ?? 0;
            $totalDeviation += abs($ideal - $current);
        }

        $alignmentScore = max(0, 100 - ($totalDeviation / 2));
        return number_format($alignmentScore / 20, 1); // Convert to 0-5 scale
    }

    private function buildPrompt($data, $metrics)
    {
        $macroContext = [
            'selic' => 14.25,
            'tendencia_selic' => 'alta',
            'ipca_anual' => 4.82,
            'cambio' => 4.95,
            'cenario_global' => 'incerto',
            'crescimento_pib' => 2.9
        ];

        // Início do prompt com contexto do sistema
        $prompt = "Como especialista em análise de investimentos no mercado brasileiro, forneça uma análise profunda e personalizada da seguinte carteira, considerando o cenário atual e cada ativo individual:\n\n";
        
        // Contexto Macroeconômico
        $prompt .= "CENÁRIO MACROECONÔMICO ATUAL:\n";
        $prompt .= "- Taxa Selic: {$macroContext['selic']}% (tendência de {$macroContext['tendencia_selic']})\n";
        $prompt .= "- IPCA Anual: {$macroContext['ipca_anual']}%\n";
        $prompt .= "- Câmbio USD/BRL: R$ {$macroContext['cambio']}\n";
        $prompt .= "- Crescimento do PIB projetado: {$macroContext['crescimento_pib']}%\n";
        $prompt .= "- Cenário global: {$macroContext['cenario_global']}\n\n";

        // Perfil do Investidor
        $prompt .= "PERFIL DO INVESTIDOR:\n";
        $prompt .= "- Perfil: " . ucfirst($data['investment_profile']) . "\n";
        $prompt .= "- Horizonte: {$data['investment_horizon']} anos\n";
        $prompt .= "- Patrimônio Total: R$ " . number_format($data['total_equity'], 2, ',', '.') . "\n";
        if (isset($data['monthly_contribution'])) {
            $prompt .= "- Aporte Mensal: R$ " . number_format($data['monthly_contribution'], 2, ',', '.') . "\n";
        }
        $prompt .= "\n";

        // Organizar ativos por categoria
        $assetsByCategory = [];
        if (isset($data['detailed_assets'])) {
            foreach ($data['detailed_assets'] as $asset) {
                if (!isset($assetsByCategory[$asset['type']])) {
                    $assetsByCategory[$asset['type']] = [];
                }
                $assetsByCategory[$asset['type']][] = $asset;
            }
        }

        // Detalhamento dos Ativos por Categoria
        $prompt .= "COMPOSIÇÃO DETALHADA DA CARTEIRA:\n\n";
        
        foreach ($assetsByCategory as $type => $assets) {
            $typeTotal = array_sum(array_column($assets, 'value'));
            $typePercentage = ($typeTotal / $data['total_equity']) * 100;
            
            $prompt .= strtoupper(str_replace('_', ' ', $type)) . " - " . 
                      number_format($typePercentage, 2) . "% do total\n";
            
            foreach ($assets as $asset) {
                $assetPercentage = ($asset['value'] / $data['total_equity']) * 100;
                $prompt .= "- {$asset['name']} ({$asset['subtype']})\n";
                $prompt .= "  Valor: R$ " . number_format($asset['value'], 2, ',', '.') . 
                          " (" . number_format($assetPercentage, 2) . "% da carteira)\n";
            }
            $prompt .= "\n";
        }

        // Métricas da Carteira
        $prompt .= "MÉTRICAS DA CARTEIRA:\n";
        $prompt .= "- Volatilidade: " . number_format($metrics['volatility'], 2) . "%\n";
        $prompt .= "- Retorno Esperado: " . number_format($metrics['expected_return'], 2) . "%\n";
        $prompt .= "- Índice Sharpe: " . number_format($metrics['sharpe_ratio'], 2) . "\n";
        $prompt .= "- Beta: " . number_format($metrics['beta'], 2) . "\n\n";

        // Instruções para análise
        $prompt .= "SOLICITAÇÃO DE ANÁLISE:\n\n";
        $prompt .= "1. ANÁLISE INDIVIDUAL DOS ATIVOS:\n";
        $prompt .= "Para cada ativo listado acima, forneça:\n";
        $prompt .= "- Análise específica considerando o cenário atual\n";
        $prompt .= "- Riscos individuais e potencial de valorização\n";
        $prompt .= "- Adequação ao perfil do investidor\n";
        $prompt .= "- Recomendações específicas (manter, aumentar, reduzir ou vender)\n\n";

        $prompt .= "2. ANÁLISE POR CATEGORIA DE ATIVOS:\n";
        $prompt .= "Para cada categoria (Renda Fixa, Renda Variável, etc.):\n";
        $prompt .= "- Avaliação da concentração e diversificação\n";
        $prompt .= "- Adequação ao cenário de Selic a {$macroContext['selic']}%\n";
        $prompt .= "- Sugestões de realocação entre subcategorias\n\n";

        $prompt .= "3. RECOMENDAÇÕES ESTRATÉGICAS:\n";
        $prompt .= "- Sugestões específicas de novos ativos para investimento\n";
        $prompt .= "- Estratégia para os aportes mensais\n";
        $prompt .= "- Ajustes necessários considerando o perfil e horizonte\n";
        $prompt .= "- Proteções específicas para cada classe de ativo\n\n";

        $prompt .= "4. ALERTAS E OPORTUNIDADES:\n";
        $prompt .= "- Identificar concentrações excessivas\n";
        $prompt .= "- Apontar riscos específicos no cenário atual\n";
        $prompt .= "- Destacar oportunidades táticas no mercado\n\n";

        $prompt .= "Forneça a análise em linguagem clara e direta, com recomendações práticas e acionáveis. Sempre justifique suas recomendações com base nos dados apresentados e no cenário atual.";

        return $prompt;
    }
} 