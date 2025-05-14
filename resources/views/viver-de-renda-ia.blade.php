@extends('layouts.app')

@section('content')
    <section class="hero-viver">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Planejador de Independência Financeira</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Descubra como conquistar sua liberdade financeira com análise personalizada baseada em IA. Simule cenários, receba recomendações detalhadas e planeje seu futuro financeiro.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Analisando seu plano financeiro...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Formulário de Diagnóstico -->
                <div class="card card-viver p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Simulação Personalizada</h3>
                    <form id="viverDeRendaForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="capital" class="form-label">Capital disponível para investir</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="capital" required oninput="maskCurrency(this)">
                                </div>
                                <div class="form-text">Valor total que você tem disponível para investir hoje</div>
                            </div>
                            <div class="col-md-6">
                                <label for="metaRenda" class="form-label">Meta de renda mensal</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="metaRenda" required oninput="maskCurrency(this)">
                                </div>
                                <div class="form-text">Quanto você deseja receber por mês</div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="idade" class="form-label">Idade atual</label>
                                <input type="number" class="form-control" id="idade" min="18" max="100" required>
                                <div class="form-text">Sua idade hoje</div>
                            </div>
                            <div class="col-md-4">
                                <label for="idadeMeta" class="form-label">Idade meta</label>
                                <input type="number" class="form-control" id="idadeMeta" min="18" max="100" required>
                                <div class="form-text">Idade desejada para independência</div>
                            </div>
                            <div class="col-md-4">
                                <label for="perfil" class="form-label">Perfil de investimento</label>
                                <select class="form-select" id="perfil" required>
                                    <option value="">Selecione...</option>
                                    <option value="conservador">Conservador</option>
                                    <option value="moderado">Moderado</option>
                                    <option value="arrojado">Arrojado</option>
                                </select>
                                <div class="form-text">Seu perfil de tolerância a risco</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-viver w-100">
                            <i class="fas fa-calculator me-2"></i>Analisar Plano Financeiro
                        </button>
                    </form>
                </div>

                <!-- Resultados do Diagnóstico -->
                <div id="results" class="d-none">
                    <!-- O conteúdo será preenchido dinamicamente -->
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Estilos específicos da página */
    body { 
        background: linear-gradient(135deg, #0033a0 0%, #001f60 100%);
        color: #fff;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
    }

    .hero-viver {
        padding: 4rem 0 2rem 0;
        text-align: center;
        background: rgba(0, 0, 0, 0.1);
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .hero-viver::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('/images/pattern.png') repeat;
        opacity: 0.05;
        z-index: 0;
    }

    .hero-viver .container {
        position: relative;
        z-index: 1;
    }

    .card-viver {
        background: #fff;
        color: #0033a0;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
        transition: all 0.3s ease;
    }

    .card-viver:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
    }

    .btn-viver {
        background: #43a047;
        color: #fff;
        font-weight: bold;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-viver:hover {
        background: #388e3c;
        color: #fff;
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #0033a0;
    }

    .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #0033a0;
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 160, 0.25);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-right: none;
        color: #0033a0;
        font-weight: 600;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: #0033a0;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 51, 160, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #43a047;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .analysis-section {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 51, 160, 0.1);
        transition: all 0.3s ease;
    }

    .analysis-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 51, 160, 0.15);
    }

    .analysis-title {
        color: #0033a0;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .analysis-content {
        color: #2c3e50;
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .metric-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 51, 160, 0.1);
    }

    .metric-title {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .metric-value {
        color: #0033a0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .metric-description {
        color: #666;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .chat-message {
        margin-bottom: 1rem;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 85%;
        position: relative;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }

    .chat-question {
        background: #e3f2fd;
        color: #0d47a1;
        margin-left: auto;
        margin-right: 15px;
        border-top-right-radius: 4px;
    }

    .chat-answer {
        background: #ffffff;
        color: #1a237e;
        margin-right: auto;
        margin-left: 15px;
        border-top-left-radius: 4px;
        border: 1px solid #e0e0e0;
    }

    .chat-container {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 15px;
    }

    .chat-container::-webkit-scrollbar {
        width: 8px;
    }

    .chat-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .chat-container::-webkit-scrollbar-thumb {
        background: #0033a0;
        border-radius: 4px;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 4px;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #0033a0;
        border-radius: 50%;
        animation: bounce 1s infinite;
    }

    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
    <script>
let diagnosticContext = null;

        function maskCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    value = (parseInt(value) / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    input.value = value;
}

function parseCurrencyToNumber(value) {
    if (!value) return 0;
    return parseFloat(value.replace(/\./g, '').replace(',', '.'));
}

document.getElementById('viverDeRendaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

    try {
        const formData = {
            capital: parseCurrencyToNumber(document.getElementById('capital').value),
            metaRenda: parseCurrencyToNumber(document.getElementById('metaRenda').value),
                    idade: parseInt(document.getElementById('idade').value),
                    idadeMeta: parseInt(document.getElementById('idadeMeta').value),
                    perfil: document.getElementById('perfil').value
                };

        if (isNaN(formData.capital) || formData.capital < 1000) {
            throw new Error('O capital inicial deve ser de pelo menos R$ 1.000,00');
        }

        if (isNaN(formData.metaRenda) || formData.metaRenda < 100) {
            throw new Error('A meta de renda mensal deve ser de pelo menos R$ 100,00');
        }

        const response = await fetch('/api/viver-de-renda/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
            body: JSON.stringify(formData)
                });

                const result = await response.json();
                
        if (!response.ok) {
            throw new Error(result.message || 'Erro ao processar diagnóstico');
        }

        // Armazenar o contexto para o chat
        diagnosticContext = {
            analysis: result.data.detailed_analysis || 'Análise não disponível',
            recommendations: Array.isArray(result.data.recommendations) && result.data.recommendations.length > 0 
                ? result.data.recommendations 
                : ['Recomendação padrão baseada no seu perfil'],
            considerations: Array.isArray(result.data.considerations) && result.data.considerations.length > 0
                ? result.data.considerations
                : ['Consideração padrão baseada no seu perfil'],
            next_steps: Array.isArray(result.data.next_steps) && result.data.next_steps.length > 0
                ? result.data.next_steps
                : ['Próximo passo padrão baseado no seu perfil'],
            data: {
                capital: Number(formData.capital),
                metaRenda: Number(formData.metaRenda),
                idade: Number(formData.idade),
                idadeMeta: Number(formData.idadeMeta),
                perfil: formData.perfil
            }
        };

        // Log do contexto para debug
        console.log('Contexto do diagnóstico:', JSON.stringify(diagnosticContext, null, 2));

        // Criar HTML para métricas
        const metricsHtml = `
            <div class="analysis-section">
                <div class="analysis-title">Métricas Principais</div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Montante Necessário</div>
                            <div class="metric-value" id="montante-necessario">${formatCurrency(result.data.metrics.montante_necessario)}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Gap Patrimonial</div>
                            <div class="metric-value" id="gap-patrimonial">${formatCurrency(result.data.metrics.gap_patrimonial)}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Aporte Mensal Sugerido</div>
                            <div class="metric-value" id="aporte-mensal">${formatCurrency(result.data.metrics.aporte_mensal_sugerido)}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Rentabilidade Alvo</div>
                            <div class="metric-value" id="rentabilidade-alvo">${result.data.metrics.rentabilidade_alvo}%</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Tempo até Meta</div>
                            <div class="metric-value" id="tempo-meta">${result.data.metrics.tempo_meta} anos</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Score de Viabilidade</div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" id="viabilidade-score-bar" role="progressbar" 
                                     style="width: ${result.data.metrics.viabilidade_score}%" 
                                     aria-valuenow="${result.data.metrics.viabilidade_score}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="metric-value" id="viabilidade-score">${result.data.metrics.viabilidade_score}%</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Criar HTML para análise detalhada
        const analysisHtml = `
            <div class="analysis-section">
                <div class="analysis-title">Análise Detalhada</div>
                <div class="analysis-content">${result.data.detailed_analysis.replace(/\n/g, '<br>')}</div>
            </div>
        `;

        // Criar HTML para recomendações
        const recommendationsHtml = `
            <div class="analysis-section">
                <div class="analysis-title">Recomendações</div>
                <div class="analysis-content">
                    ${result.data.recommendations.map(item => `
                        <div class="next-step">
                            <i class="fas fa-check-circle me-2"></i>${item}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Criar HTML para considerações
        const considerationsHtml = `
            <div class="analysis-section">
                <div class="analysis-title">Considerações</div>
                <div class="analysis-content">
                    ${result.data.considerations.map(item => `
                        <div class="legal-consideration">
                            <i class="fas fa-info-circle me-2"></i>${item}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Criar HTML para próximos passos
        const nextStepsHtml = `
            <div class="analysis-section">
                <div class="analysis-title">Próximos Passos</div>
                <div class="analysis-content">
                    ${result.data.next_steps.map(item => `
                        <div class="next-step">
                            <i class="fas fa-arrow-right me-2"></i>${item}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Criar seção de chat
        const chatHtml = `
            <div class="analysis-section mt-4" id="chatSection">
                <div class="analysis-title">
                    <i class="fas fa-comments me-2"></i>Tire suas Dúvidas
                    <small class="text-muted ms-2">(mínimo 5 caracteres)</small>
                </div>
                <div class="chat-container" style="max-height: 400px; overflow-y: auto;">
                    <div id="chatMessages" class="mb-3">
                        <div class="chat-message chat-answer">
                            <strong>Assistente:</strong><br>
                            Olá! Estou aqui para ajudar com suas dúvidas sobre o planejamento financeiro. 
                            Você pode me perguntar sobre:
                            <ul>
                                <li>Detalhes da análise realizada</li>
                                <li>Esclarecimentos sobre as recomendações</li>
                                <li>Dúvidas sobre as considerações</li>
                                <li>Como implementar os próximos passos</li>
                                <li>Qualquer outro aspecto do seu planejamento</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form id="chatForm" class="mt-3">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="chatInput" 
                               placeholder="Digite sua pergunta sobre o planejamento..." 
                               required 
                               minlength="5"
                               autocomplete="off">
                        <button class="btn btn-viver" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Enviar
                        </button>
                    </div>
                    <div class="invalid-feedback" id="chatError"></div>
                </form>
            </div>
        `;

        // Atualizar a seção de resultados
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = `
            <h4 class="resultado-header mb-4">Análise do Planejamento Financeiro</h4>
            ${metricsHtml}
            ${analysisHtml}
            ${recommendationsHtml}
            ${considerationsHtml}
            ${nextStepsHtml}
            ${chatHtml}
        `;
        resultsDiv.classList.remove('d-none');
        resultsDiv.scrollIntoView({ behavior: 'smooth' });

        // Configurar o chat
        setupChat();

    } catch (error) {
        console.error('Erro:', error);
        alert(error.message || 'Ocorreu um erro ao processar o diagnóstico');
            } finally {
                loadingOverlay.style.display = 'none';
                btn.disabled = false;
            }
        });

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function setupChat() {
    document.getElementById('chatForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const chatInput = document.getElementById('chatInput');
        const question = chatInput.value.trim();

        if (question.length < 5) {
            showError('A pergunta deve ter pelo menos 5 caracteres.');
            return;
        }

        // Validar contexto antes de enviar
        if (!diagnosticContext || !diagnosticContext.analysis) {
            showError('É necessário realizar o diagnóstico antes de fazer perguntas.');
            return;
        }

        // Garantir que todos os arrays necessários existam e tenham pelo menos um item
        const contextToSend = {
            analysis: String(diagnosticContext.analysis),
            recommendations: Array.isArray(diagnosticContext.recommendations) && diagnosticContext.recommendations.length > 0
                ? diagnosticContext.recommendations.map(String)
                : ['Recomendação padrão baseada no seu perfil'],
            considerations: Array.isArray(diagnosticContext.considerations) && diagnosticContext.considerations.length > 0
                ? diagnosticContext.considerations.map(String)
                : ['Consideração padrão baseada no seu perfil'],
            next_steps: Array.isArray(diagnosticContext.next_steps) && diagnosticContext.next_steps.length > 0
                ? diagnosticContext.next_steps.map(String)
                : ['Próximo passo padrão baseado no seu perfil'],
            data: {
                capital: Math.max(1000, Number(diagnosticContext.data.capital) || 1000),
                metaRenda: Math.max(100, Number(diagnosticContext.data.metaRenda) || 100),
                idade: Math.min(100, Math.max(18, Number(diagnosticContext.data.idade) || 18)),
                idadeMeta: Math.min(100, Math.max(18, Number(diagnosticContext.data.idadeMeta) || 18)),
                perfil: ['conservador', 'moderado', 'arrojado'].includes(diagnosticContext.data.perfil)
                    ? diagnosticContext.data.perfil
                    : 'conservador'
            }
        };

        // Log do contexto para debug
        console.log('Enviando contexto:', JSON.stringify(contextToSend, null, 2));

        // Adicionar a pergunta ao chat
        addMessage(question, 'question');

        // Mostrar indicador de digitação
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message chat-answer';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        document.getElementById('chatMessages').appendChild(typingDiv);

        // Limpar o input
        chatInput.value = '';

        try {
            const response = await fetch('/api/viver-de-renda/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    question: question,
                    context: contextToSend
                })
            });

            // Remover indicador de digitação
            typingDiv.remove();

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Erro ao processar sua pergunta');
            }

            addMessage(result.data.answer, 'answer');

        } catch (error) {
            console.error('Erro no chat:', error);
            showError(error.message || 'Ocorreu um erro ao processar sua pergunta');
            typingDiv.remove();
        }
    });
}

function addMessage(content, type) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message chat-${type}`;
    messageDiv.innerHTML = type === 'question' ? 
        `<strong>Você:</strong><br>${content}` : 
        `<strong>Assistente:</strong><br>${content}`;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'chat-message chat-answer';
    errorDiv.style.background = '#ffebee';
    errorDiv.innerHTML = `<strong>Erro:</strong><br>${message}`;
    document.getElementById('chatMessages').appendChild(errorDiv);
}
    </script>
@endpush 