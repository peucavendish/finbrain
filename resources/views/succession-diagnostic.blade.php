<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico Sucessório - FinBrain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0033a0 0%, #001f60 100%);
            color: #fff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .hero-succession {
            padding: 4rem 0 2rem 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-succession::before {
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
        .hero-succession .container {
            position: relative;
            z-index: 1;
        }
        .card-succession {
            background: #fff;
            color: #0033a0;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
            transition: all 0.3s ease;
        }
        .card-succession:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
        }
        .btn-succession {
            background: #43a047;
            color: #fff;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-succession:hover {
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
        .heir-item, .asset-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
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
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 51, 160, 0.1);
            transition: all 0.3s ease;
            color: #2c3e50;
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
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            color: #2c3e50;
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
        .legal-consideration, .next-step {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #0033a0;
            color: #2c3e50;
        }
        .resultado-header {
            color: #ffffff;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }
        .btn-succession {
            background: #43a047;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-succession:hover {
            background: #388e3c;
            color: #ffffff;
            transform: translateY(-2px);
        }
        .form-label {
            color: #0033a0;
            font-weight: 600;
        }
        .form-text {
            color: #666666;
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <span class="fw-bold text-primary">FinBrain</span>
            </a>
        </div>
    </nav>

    <section class="hero-succession">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Diagnóstico Sucessório</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Análise inteligente do seu planejamento sucessório com recomendações personalizadas baseadas na legislação brasileira.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Analisando seu planejamento sucessório...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-succession p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Análise Sucessória</h3>
                    <form id="successionForm">
                        @csrf
                        <!-- Informações Pessoais -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Informações Pessoais</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marital_status" class="form-label">Estado Civil</label>
                                    <select class="form-control" id="marital_status" name="marital_status" required>
                                        <option value="">Selecione...</option>
                                        <option value="solteiro">Solteiro(a)</option>
                                        <option value="casado">Casado(a)</option>
                                        <option value="divorciado">Divorciado(a)</option>
                                        <option value="viuvo">Viúvo(a)</option>
                                        <option value="uniao_estavel">União Estável</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="total_assets" class="form-label">Patrimônio Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="total_assets" name="total_assets" required oninput="maskCurrency(this)">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label d-block">Possui Testamento?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_will" id="has_will_yes" value="1">
                                        <label class="form-check-label" for="has_will_yes">Sim</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_will" id="has_will_no" value="0" checked>
                                        <label class="form-check-label" for="has_will_no">Não</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label d-block">Possui Empresa?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_company" id="has_company_yes" value="1">
                                        <label class="form-check-label" for="has_company_yes">Sim</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_company" id="has_company_no" value="0" checked>
                                        <label class="form-check-label" for="has_company_no">Não</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Herdeiros -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Herdeiros</h4>
                            <div id="heirsList"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addHeir()">
                                + Adicionar Herdeiro
                            </button>
                        </div>

                        <!-- Patrimônio -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Composição do Patrimônio</h4>
                            <div id="assetsList"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAsset()">
                                + Adicionar Bem
                            </button>
                        </div>

                        <!-- Condições Especiais -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Condições Especiais</h4>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="herdeiro_incapaz" id="condition_incapaz">
                                    <label class="form-check-label" for="condition_incapaz">
                                        Possui herdeiro incapaz
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="herdeiro_menor" id="condition_menor">
                                    <label class="form-check-label" for="condition_menor">
                                        Possui herdeiro menor de idade
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="bem_exterior" id="condition_exterior">
                                    <label class="form-check-label" for="condition_exterior">
                                        Possui bens no exterior
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="processo_judicial" id="condition_judicial">
                                    <label class="form-check-label" for="condition_judicial">
                                        Possui processos judiciais em andamento
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-succession w-100">Analisar Planejamento Sucessório</button>
                    </form>
                </div>

                <!-- Resultados -->
                <div id="results" class="animate__animated animate__fadeIn" style="display: none;">
                    <!-- O conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </section>

    <footer class="footer py-4 bg-white text-center">
        <div class="container">
            <span class="me-2 text-dark">Powered by</span>
            <span class="fw-bold text-primary">FinBrain</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function maskCurrency(input) {
            let v = input.value.replace(/\D/g, '');
            v = (v/100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            input.value = v;
        }

        function addHeir() {
            const container = document.getElementById('heirsList');
            const heirDiv = document.createElement('div');
            heirDiv.className = 'heir-item';
            heirDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control heir-name" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Idade</label>
                        <input type="number" class="form-control heir-age" required min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Parentesco</label>
                        <select class="form-control heir-relationship" required>
                            <option value="">Selecione...</option>
                            <option value="filho">Filho(a)</option>
                            <option value="conjuge">Cônjuge</option>
                            <option value="neto">Neto(a)</option>
                            <option value="pai">Pai</option>
                            <option value="mae">Mãe</option>
                            <option value="irmao">Irmão/Irmã</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeHeir(this)">
                    Remover Herdeiro
                </button>
            `;
            container.appendChild(heirDiv);
        }

        function removeHeir(button) {
            button.closest('.heir-item').remove();
        }

        function addAsset() {
            const container = document.getElementById('assetsList');
            const assetDiv = document.createElement('div');
            assetDiv.className = 'asset-item';
            assetDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Bem</label>
                        <select class="form-control asset-type" required>
                            <option value="">Selecione...</option>
                            <option value="imoveis">Imóveis</option>
                            <option value="investimentos">Investimentos Financeiros</option>
                            <option value="empresa">Participações Societárias</option>
                            <option value="veiculos">Veículos</option>
                            <option value="joias">Joias e Obras de Arte</option>
                            <option value="outros">Outros Bens</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control asset-value" required oninput="maskCurrency(this)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAsset(this)">
                    Remover Bem
                </button>
            `;
            container.appendChild(assetDiv);
        }

        function removeAsset(button) {
            button.closest('.asset-item').remove();
        }

        function parseCurrencyToNumber(value) {
            if (!value) return 0;
            return parseFloat(value.replace(/\./g, '').replace(',', '.'));
        }

        function getHeirs() {
            const heirs = [];
            document.querySelectorAll('.heir-item').forEach(item => {
                const age = parseInt(item.querySelector('.heir-age').value);
                if (!isNaN(age)) {
                    heirs.push({
                        name: item.querySelector('.heir-name').value.trim(),
                        age: age,
                        relationship: item.querySelector('.heir-relationship').value
                    });
                }
            });
            return heirs;
        }

        function getAssets() {
            const assets = [];
            document.querySelectorAll('.asset-item').forEach(item => {
                const value = parseCurrencyToNumber(item.querySelector('.asset-value').value);
                if (!isNaN(value) && value > 0) {
                    assets.push({
                        type: item.querySelector('.asset-type').value,
                        value: value
                    });
                }
            });
            return assets;
        }

        function getSpecialConditions() {
            return Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
        }

        document.getElementById('successionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

            try {
                const totalAssets = parseCurrencyToNumber(document.getElementById('total_assets').value);
                const heirs = getHeirs();
                const assets = getAssets();

                if (heirs.length === 0) {
                    throw new Error('É necessário adicionar pelo menos um herdeiro.');
                }

                if (assets.length === 0) {
                    throw new Error('É necessário adicionar pelo menos um bem.');
                }

                if (isNaN(totalAssets) || totalAssets <= 0) {
                    throw new Error('O valor total do patrimônio deve ser maior que zero.');
                }

                const data = {
                    marital_status: document.getElementById('marital_status').value,
                    total_assets: totalAssets,
                    has_will: document.querySelector('input[name="has_will"]:checked').value === "1",
                    has_company: document.querySelector('input[name="has_company"]:checked').value === "1",
                    heirs: heirs,
                    asset_types: assets,
                    special_conditions: getSpecialConditions()
                };

                console.log('Enviando dados:', data); // Debug log

                const response = await fetch('/api/diagnostico-sucessorio', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData?.message || `HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Resposta:', result); // Debug log
                
                if (result.success) {
                    // Armazenar o contexto do diagnóstico de forma estruturada
                    window.diagnosticContext = {
                        analysis: result.data.detailed_analysis || '',
                        recommendations: Array.isArray(result.data.recommendations) && result.data.recommendations.length > 0 
                            ? result.data.recommendations 
                            : ['Consultar um advogado especializado'],
                        legal_considerations: Array.isArray(result.data.legal_considerations) && result.data.legal_considerations.length > 0
                            ? result.data.legal_considerations
                            : ['Análise da legislação sucessória aplicável'],
                        next_steps: Array.isArray(result.data.next_steps) && result.data.next_steps.length > 0
                            ? result.data.next_steps
                            : ['Agendar consulta com especialista'],
                        data: {
                            total_assets: Number(data.total_assets) || 0,
                            heirs: Array.isArray(data.heirs) && data.heirs.length > 0 
                                ? data.heirs.map(heir => ({
                                    name: String(heir.name || ''),
                                    age: Number(heir.age || 0),
                                    relationship: String(heir.relationship || '')
                                }))
                                : [{
                                    name: 'Herdeiro não especificado',
                                    age: 0,
                                    relationship: 'Não especificado'
                                }],
                            asset_types: Array.isArray(data.asset_types) && data.asset_types.length > 0
                                ? data.asset_types.map(asset => ({
                                    type: String(asset.type || ''),
                                    value: Number(asset.value || 0)
                                }))
                                : [{
                                    type: 'Não especificado',
                                    value: Number(data.total_assets) || 0
                                }]
                        }
                    };

                    console.log('Contexto do diagnóstico salvo:', JSON.stringify(window.diagnosticContext, null, 2));

                    // Criar HTML para métricas
                    const metricsHtml = `
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Complexidade</div>
                                    <div class="metric-value">${result.data.complexity_score}%</div>
                                    <div class="metric-description">Nível de complexidade do seu planejamento</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Risco</div>
                                    <div class="metric-value">${result.data.risk_score}%</div>
                                    <div class="metric-description">Nível de risco identificado</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Exposição Tributária</div>
                                    <div class="metric-value">${result.data.tax_exposure.percentage}%</div>
                                    <div class="metric-description">R$ ${result.data.tax_exposure.total.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
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

                    // Criar HTML para considerações legais
                    const legalHtml = `
                        <div class="analysis-section">
                            <div class="analysis-title">Considerações Legais</div>
                            <div class="analysis-content">
                                ${result.data.legal_considerations.map(item => `
                                    <div class="legal-consideration">
                                        <i class="fas fa-balance-scale me-2"></i>${item}
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
                                        <i class="fas fa-check-circle me-2"></i>${item}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;

                    // Atualizar a seção de resultados
                    document.getElementById('results').innerHTML = `
                        <h4 class="resultado-header mb-4">Análise do Planejamento Sucessório</h4>
                        ${metricsHtml}
                        ${analysisHtml}
                        ${legalHtml}
                        ${nextStepsHtml}
                        
                        <!-- Chat Section -->
                        <div class="analysis-section mt-4" id="chatSection">
                            <div class="analysis-title">
                                <i class="fas fa-comments me-2"></i>Tire suas Dúvidas
                                <small class="text-muted ms-2">(mínimo 5 caracteres)</small>
                            </div>
                            <div class="chat-container" style="max-height: 400px; overflow-y: auto;">
                                <div id="chatMessages" class="mb-3">
                                    <div class="chat-message chat-answer">
                                        <strong>Assistente:</strong><br>
                                        Olá! Estou aqui para ajudar com suas dúvidas sobre o diagnóstico sucessório. 
                                        Você pode me perguntar sobre:
                                        <ul>
                                            <li>Detalhes da análise realizada</li>
                                            <li>Esclarecimentos sobre as recomendações</li>
                                            <li>Dúvidas sobre as considerações legais</li>
                                            <li>Como implementar os próximos passos</li>
                                            <li>Qualquer outro aspecto do seu planejamento sucessório</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <form id="chatForm" class="mt-3">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="chatInput" 
                                           placeholder="Digite sua pergunta sobre o diagnóstico..." 
                                           required 
                                           minlength="5"
                                           autocomplete="off">
                                    <button class="btn btn-succession" type="submit">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="chatError"></div>
                            </form>
                        </div>
                    `;

                    // Adicionar estilos para o chat
                    const chatStyles = `
                        .chat-message {
                            margin-bottom: 1rem;
                            padding: 1rem;
                            border-radius: 8px;
                            opacity: 0;
                            animation: fadeIn 0.3s ease forwards;
                        }
                        .chat-question {
                            background: #f8f9fa;
                            margin-left: 20%;
                            border-top-right-radius: 0;
                        }
                        .chat-answer {
                            background: #e3f2fd;
                            margin-right: 20%;
                            border-top-left-radius: 0;
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
                    `;

                    const styleSheet = document.createElement("style");
                    styleSheet.textContent = chatStyles;
                    document.head.appendChild(styleSheet);

                    // Configurar o formulário do chat
                    document.getElementById('chatForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const input = document.getElementById('chatInput');
                        const question = input.value.trim();
                        const errorDiv = document.getElementById('chatError');
                        
                        // Validação básica
                        if (question.length < 5) {
                            errorDiv.textContent = 'A pergunta deve ter pelo menos 5 caracteres';
                            errorDiv.style.display = 'block';
                            return;
                        }
                        
                        errorDiv.style.display = 'none';

                        // Verificar se temos o contexto do diagnóstico
                        if (!window.diagnosticContext) {
                            console.error('Contexto do diagnóstico não encontrado');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'chat-message chat-answer animate__animated animate__fadeIn';
                            errorDiv.style.background = '#ffebee';
                            errorDiv.innerHTML = `
                                <strong>Erro:</strong><br>
                                É necessário realizar o diagnóstico sucessório antes de utilizar o chat.
                            `;
                            document.getElementById('chatMessages').appendChild(errorDiv);
                            return;
                        }

                        // Validar o contexto antes do envio
                        const contextValidation = validateContext(window.diagnosticContext);
                        if (!contextValidation.isValid) {
                            console.error('Contexto inválido:', contextValidation.errors);
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'chat-message chat-answer animate__animated animate__fadeIn';
                            errorDiv.style.background = '#ffebee';
                            errorDiv.innerHTML = `
                                <strong>Erro:</strong><br>
                                Dados do diagnóstico incompletos. Por favor, realize o diagnóstico novamente.<br>
                                <small class="text-muted">Detalhes: ${contextValidation.errors.join(', ')}</small>
                            `;
                            document.getElementById('chatMessages').appendChild(errorDiv);
                            return;
                        }

                        // Preparar o contexto para envio
                        const contextForSubmission = {
                            analysis: String(window.diagnosticContext.analysis || ''),
                            recommendations: Array.isArray(window.diagnosticContext.recommendations) 
                                ? window.diagnosticContext.recommendations.map(r => String(r))
                                : [],
                            legal_considerations: Array.isArray(window.diagnosticContext.legal_considerations)
                                ? window.diagnosticContext.legal_considerations.map(lc => String(lc))
                                : [],
                            next_steps: Array.isArray(window.diagnosticContext.next_steps)
                                ? window.diagnosticContext.next_steps.map(ns => String(ns))
                                : [],
                            data: {
                                total_assets: Number(window.diagnosticContext.data?.total_assets || 0),
                                heirs: Array.isArray(window.diagnosticContext.data?.heirs)
                                    ? window.diagnosticContext.data.heirs.map(h => ({
                                        name: String(h.name || ''),
                                        age: Number(h.age || 0),
                                        relationship: String(h.relationship || '')
                                    }))
                                    : [],
                                asset_types: Array.isArray(window.diagnosticContext.data?.asset_types)
                                    ? window.diagnosticContext.data.asset_types.map(at => ({
                                        type: String(at.type || ''),
                                        value: Number(at.value || 0)
                                    }))
                                    : []
                            }
                        };

                        // Log detalhado do contexto antes do envio
                        console.log('Contexto completo para envio:', JSON.stringify(contextForSubmission, null, 2));

                        // Adicionar a pergunta ao chat
                        const chatMessages = document.getElementById('chatMessages');
                        const questionDiv = document.createElement('div');
                        questionDiv.className = 'chat-message chat-question animate__animated animate__fadeIn';
                        questionDiv.innerHTML = `
                            <strong>Você:</strong><br>
                            ${question}
                        `;
                        chatMessages.appendChild(questionDiv);
                        
                        // Limpar o input e desabilitar o formulário
                        input.value = '';
                        const btn = this.querySelector('button');
                        const originalBtnHtml = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Aguarde...';
                        
                        // Adicionar indicador de digitação
                        const typingDiv = document.createElement('div');
                        typingDiv.className = 'chat-message chat-answer animate__animated animate__fadeIn';
                        typingDiv.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
                        chatMessages.appendChild(typingDiv);

                        try {
                            const response = await fetch('/api/diagnostico-sucessorio/chat', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question: question,
                                    context: contextForSubmission
                                })
                            });

                            // Remover indicador de digitação
                            typingDiv.remove();

                            const responseData = await response.json();
                            
                            if (!response.ok) {
                                console.error('Erro na resposta:', responseData);
                                console.error('Detalhes da validação:', responseData.errors);
                                console.error('Dados enviados:', {
                                    question: question,
                                    context: contextForSubmission
                                });
                                throw new Error(responseData.message || `Erro ${response.status}: ${response.statusText}`);
                            }

                            if (responseData.success) {
                                const answerDiv = document.createElement('div');
                                answerDiv.className = 'chat-message chat-answer animate__animated animate__fadeIn';
                                answerDiv.innerHTML = `
                                    <strong>Assistente:</strong><br>
                                    ${responseData.data.answer.replace(/\n/g, '<br>')}
                                `;
                                chatMessages.appendChild(answerDiv);
                            } else {
                                throw new Error(responseData.message || 'Resposta inválida do servidor');
                            }
                        } catch (err) {
                            console.error('Erro no chat:', err);
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'chat-message chat-answer animate__animated animate__fadeIn';
                            errorDiv.style.background = '#ffebee';
                            errorDiv.innerHTML = `
                                <strong>Erro:</strong><br>
                                ${err.message || 'Não foi possível processar sua pergunta. Por favor, tente novamente.'}
                            `;
                            chatMessages.appendChild(errorDiv);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = originalBtnHtml;
                            
                            // Rolar para a última mensagem
                            const chatContainer = document.querySelector('.chat-container');
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }
                    });

                    document.getElementById('results').style.display = 'block';
                    document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
                } else {
                    throw new Error(result.message || 'Falha na análise');
                }
            } catch (err) {
                console.error('Erro:', err);
                document.getElementById('results').innerHTML = `
                    <div class="analysis-section">
                        <div class="alert alert-danger">
                            ${err.message || 'Erro inesperado. Por favor, tente novamente.'}
                        </div>
                    </div>
                `;
                document.getElementById('results').style.display = 'block';
            } finally {
                loadingOverlay.style.display = 'none';
                btn.disabled = false;
            }
        });

        // Função para validar o contexto
        function validateContext(context) {
            const errors = [];
            
            if (!context) {
                return { isValid: false, errors: ['Contexto não encontrado'] };
            }

            // Validar análise
            if (!context.analysis || typeof context.analysis !== 'string' || context.analysis.trim() === '') {
                errors.push('Análise não encontrada ou inválida');
            }

            // Validar arrays
            const validateArray = (arr, name) => {
                if (!Array.isArray(arr) || arr.length === 0) {
                    errors.push(`${name} deve ser uma lista não vazia`);
                    return false;
                }
                return arr.every(item => typeof item === 'string' && item.trim() !== '');
            };

            if (!validateArray(context.recommendations, 'Recomendações')) {
                errors.push('Todas as recomendações devem ser textos não vazios');
            }

            if (!validateArray(context.legal_considerations, 'Considerações legais')) {
                errors.push('Todas as considerações legais devem ser textos não vazios');
            }

            if (!validateArray(context.next_steps, 'Próximos passos')) {
                errors.push('Todos os próximos passos devem ser textos não vazios');
            }

            // Validar dados
            if (!context.data) {
                errors.push('Dados não encontrados');
            } else {
                if (typeof context.data.total_assets !== 'number' || context.data.total_assets < 0) {
                    errors.push('Valor total inválido');
                }

                if (!Array.isArray(context.data.heirs) || context.data.heirs.length === 0) {
                    errors.push('Lista de herdeiros inválida');
                } else {
                    context.data.heirs.forEach((heir, index) => {
                        if (!heir.name || typeof heir.name !== 'string') {
                            errors.push(`Nome do herdeiro ${index + 1} inválido`);
                        }
                        if (typeof heir.age !== 'number' || heir.age < 0) {
                            errors.push(`Idade do herdeiro ${index + 1} inválida`);
                        }
                        if (!heir.relationship || typeof heir.relationship !== 'string') {
                            errors.push(`Parentesco do herdeiro ${index + 1} inválido`);
                        }
                    });
                }

                if (!Array.isArray(context.data.asset_types) || context.data.asset_types.length === 0) {
                    errors.push('Lista de bens inválida');
                } else {
                    context.data.asset_types.forEach((asset, index) => {
                        if (!asset.type || typeof asset.type !== 'string') {
                            errors.push(`Tipo do bem ${index + 1} inválido`);
                        }
                        if (typeof asset.value !== 'number' || asset.value < 0) {
                            errors.push(`Valor do bem ${index + 1} inválido`);
                        }
                    });
                }
            }

            return {
                isValid: errors.length === 0,
                errors: errors
            };
        }

        // Adicionar um herdeiro e um bem inicialmente
        addHeir();
        addAsset();
    </script>
</body>
</html> 