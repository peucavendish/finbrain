<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico de Carteira - FinBrain</title>
    @csrf
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0033a0 0%, #001f60 100%);
            color: #fff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .hero-carteira {
            padding: 4rem 0 2rem 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-carteira::before {
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
        .hero-carteira .container {
            position: relative;
            z-index: 1;
        }
        .card-carteira {
            background: #fff;
            color: #0033a0;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
            transition: all 0.3s ease;
        }
        .card-carteira:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
        }
        .btn-carteira {
            background: #43a047;
            color: #fff;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-carteira::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, .5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        .btn-carteira:hover {
            background: #388e3c;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-carteira:hover::after {
            animation: ripple 1s ease-out;
        }
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
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
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #0033a0;
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 160, 0.25);
        }
        .resultado-section {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
        }
        .resultado-header {
            color: #0033a0;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .resultado-content {
            color: #333;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .resultado-content strong {
            color: #0033a0;
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
        .loading-text {
            color: #fff;
            margin-top: 1rem;
            font-size: 1.1rem;
            text-align: center;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .form-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
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
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-outline-primary {
            color: #0033a0;
            border-color: #0033a0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background-color: #0033a0;
            color: #fff;
            transform: translateY(-2px);
        }
        .risk-score {
            font-size: 2rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
        .result-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 16px rgba(0, 51, 160, 0.1);
            transition: all 0.3s ease;
        }
        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0, 51, 160, 0.15);
        }
        .progress {
            height: 1.5rem;
            border-radius: 0.75rem;
            background-color: #e9ecef;
            overflow: hidden;
        }
        .progress-bar {
            transition: width 1s ease;
            background-color: #0033a0;
            color: #fff;
            font-weight: 600;
        }
        .asset-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
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
        .analysis-highlight {
            background: #f1f8ff;
            border-left: 4px solid #0033a0;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight {
            color: #e74c3c;
            font-weight: 600;
        }
        .analysis-section strong {
            color: #0033a0;
            font-weight: 600;
        }
        .macro-context {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .macro-context-title {
            color: #0033a0;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .macro-metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .macro-metric:last-child {
            border-bottom: none;
        }
        .macro-metric-label {
            color: #495057;
            font-weight: 500;
        }
        .macro-metric-value {
            color: #0033a0;
            font-weight: 600;
        }
        .trend-up {
            color: #28a745;
        }
        .trend-down {
            color: #dc3545;
        }
        .recommendation-item {
            background: #f1f8ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #43a047;
            transition: all 0.3s ease;
        }
        .recommendation-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 51, 160, 0.1);
        }
        .recommendation-title {
            color: #0033a0;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .recommendation-content {
            color: #2c3e50;
            line-height: 1.6;
        }
        .metric-highlight {
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            background: #e8f5e9;
            color: #2e7d32;
        }
        .metric-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        .metric-danger {
            background: #ffebee;
            color: #c62828;
        }
        .metric-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
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

    <section class="hero-carteira">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Diagnóstico de Carteira</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Análise inteligente da sua carteira de investimentos com recomendações personalizadas baseadas no cenário atual do mercado brasileiro.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Analisando sua carteira...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-carteira p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Análise de Carteira</h3>
                    <form id="portfolioForm">
                        <!-- Upload de Extrato -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Upload de Extrato</h4>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card p-3 bg-light">
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <label for="pdfUpload" class="form-label">
                                                    <div class="btn btn-outline-primary">
                                                        <i class="fas fa-upload me-2"></i>Fazer upload do extrato em PDF
                                                    </div>
                                                </label>
                                                <input type="file" class="d-none" id="pdfUpload" accept=".pdf" onchange="handlePdfUpload(this)">
                                            </div>
                                            <div id="pdfInfo" class="small text-muted" style="display: none;">
                                                <span id="pdfName"></span>
                                                <button type="button" class="btn btn-link text-danger p-0 ms-2" onclick="removePdf()">Remover</button>
                                            </div>
                                            <div class="text-muted small">ou preencha manualmente abaixo</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Perfil do Investidor -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Perfil do Investidor</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="profile" class="form-label">Perfil de Investimento</label>
                                    <select class="form-control" id="profile" required>
                                        <option value="">Selecione...</option>
                                        <option value="conservador">Conservador</option>
                                        <option value="moderado">Moderado</option>
                                        <option value="arrojado">Arrojado</option>
                                    </select>
                                    <div class="form-text">Seu perfil de risco</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="horizon" class="form-label">Horizonte de Investimento</label>
                                    <select class="form-control" id="horizon" required>
                                        <option value="">Selecione...</option>
                                        <option value="2">Curto (até 2 anos)</option>
                                        <option value="5">Médio (2-5 anos)</option>
                                        <option value="10">Longo (mais de 5 anos)</option>
                                    </select>
                                    <div class="form-text">Tempo planejado para investir (em anos)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Financeiros -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Dados Financeiros</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="monthlyContribution" class="form-label">Aporte Mensal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="monthlyContribution" required oninput="maskCurrency(this)">
                                    </div>
                                    <div class="form-text">Valor que pode investir mensalmente</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="totalEquity" class="form-label">Patrimônio Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="totalEquity" required oninput="maskCurrency(this)">
                                    </div>
                                    <div class="form-text">Valor total investido atualmente</div>
                                </div>
                            </div>
                        </div>

                        <!-- Carteira Atual -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Carteira Atual</h4>
                            <div id="portfolioAssets">
                                <div class="asset-item">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tipo de Ativo</label>
                                            <select class="form-control asset-type" required>
                                                <option value="">Selecione...</option>
                                                <option value="renda_fixa">Renda Fixa</option>
                                                <option value="renda_variavel">Renda Variável</option>
                                                <option value="fundos">Fundos de Investimento</option>
                                                <option value="alternativos">Investimentos Alternativos</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Valor Investido</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" class="form-control asset-value" required oninput="maskCurrency(this)">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAsset(this)">Remover Ativo</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="addAsset()">
                                + Adicionar Ativo
                            </button>
                        </div>

                        <button type="submit" class="btn btn-carteira w-100">Analisar Carteira</button>
                    </form>
                </div>

                <!-- Resultados -->
                <div id="results" class="resultado-section animate__animated animate__fadeIn" style="display: none;">
                    <h4 class="resultado-header">Análise da Carteira</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Score de Diversificação</h5>
                                <div class="progress mb-2">
                                    <div id="diversificationBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Relação Risco/Retorno</h5>
                                <p class="h4 mb-0 text-primary" id="riskReturnRatio">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Alinhamento com Mercado</h5>
                                <p class="h4 mb-0 text-success" id="marketAlignment">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="result-card mt-4">
                        <h5 class="h6 mb-3">Análise Detalhada</h5>
                        <div id="detailedAnalysis" class="resultado-content"></div>
                    </div>
                    <div class="result-card mt-4">
                        <h5 class="h6 mb-3">Recomendações</h5>
                        <div id="recommendations" class="resultado-content"></div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
    <script>
        function addAsset() {
            const container = document.getElementById('portfolioAssets');
            const newAsset = document.createElement('div');
            newAsset.className = 'asset-item';
            newAsset.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Ativo</label>
                        <select class="form-control asset-type" required>
                            <option value="">Selecione...</option>
                            <option value="renda_fixa">Renda Fixa</option>
                            <option value="renda_variavel">Renda Variável</option>
                            <option value="fundos">Fundos de Investimento</option>
                            <option value="alternativos">Investimentos Alternativos</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Investido</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control asset-value" required oninput="maskCurrency(this)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAsset(this)">Remover Ativo</button>
            `;
            container.appendChild(newAsset);
        }

        function removeAsset(button) {
            button.closest('.asset-item').remove();
        }

        function maskCurrency(input) {
            let v = input.value.replace(/\D/g, '');
            v = (v/100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            input.value = v;
        }

        function getAssets() {
            const assets = [];
            let totalValue = 0;
            
            // Primeiro, calcular o valor total
            document.querySelectorAll('.asset-item').forEach(item => {
                const value = item.querySelector('.asset-value').value;
                if (value) {
                    totalValue += parseFloat(value.replace(/\./g, '').replace(',', '.'));
                }
            });

            // Depois, calcular a porcentagem de cada ativo
            document.querySelectorAll('.asset-item').forEach(item => {
                const type = item.querySelector('.asset-type').value;
                const value = item.querySelector('.asset-value').value;
                if (type && value) {
                    const numericValue = parseFloat(value.replace(/\./g, '').replace(',', '.'));
                    const percentage = parseFloat(((numericValue / totalValue) * 100).toFixed(2));
                    assets.push({
                        type: type,
                        value: numericValue,
                        percentage: percentage
                    });
                }
            });
            return assets;
        }

        function parseCurrencyToNumber(value) {
            if (!value) return 0;
            return parseFloat(value.replace(/\./g, '').replace(',', '.'));
        }

        function handlePdfUpload(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('pdfName').textContent = file.name;
                document.getElementById('pdfInfo').style.display = 'block';
                
                // Disable manual input fields when PDF is uploaded
                toggleManualInputs(true);
            }
        }

        function removePdf() {
            const input = document.getElementById('pdfUpload');
            input.value = '';
            document.getElementById('pdfInfo').style.display = 'none';
            document.getElementById('pdfName').textContent = '';
            
            // Enable manual input fields when PDF is removed
            toggleManualInputs(false);
        }

        function toggleManualInputs(disabled) {
            const inputs = document.querySelectorAll('#monthlyContribution, #totalEquity, .asset-type, .asset-value');
            inputs.forEach(input => input.disabled = disabled);
            
            const addAssetBtn = document.querySelector('button[onclick="addAsset()"]');
            const removeAssetBtns = document.querySelectorAll('button[onclick="removeAsset(this)"]');
            
            if (addAssetBtn) addAssetBtn.disabled = disabled;
            removeAssetBtns.forEach(btn => btn.disabled = disabled);
        }

        document.getElementById('portfolioForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

            try {
                const investmentProfile = document.getElementById('profile').value;
                const investmentHorizon = document.getElementById('horizon').value;

                // Validar campos obrigatórios
                if (!investmentProfile) {
                    throw new Error('Por favor, selecione o perfil de investimento.');
                }
                if (!investmentHorizon) {
                    throw new Error('Por favor, selecione o horizonte de investimento.');
                }

                const pdfInput = document.getElementById('pdfUpload');
                let endpoint = '/api/diagnostico-carteira';
                let formData;
                let headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                };

                if (pdfInput.files.length > 0) {
                    console.log('Preparando upload de PDF...');
                    endpoint += '/pdf';
                    
                    // Criar FormData
                    formData = new FormData();
                    
                    // Adicionar arquivo
                    formData.append('pdf', pdfInput.files[0]);
                    
                    // Adicionar outros campos
                    formData.append('investment_profile', investmentProfile);
                    formData.append('investment_horizon', investmentHorizon);

                    // Log dos dados
                    console.log('Dados do formulário:', {
                        pdf_name: pdfInput.files[0].name,
                        pdf_size: pdfInput.files[0].size,
                        investment_profile: investmentProfile,
                        investment_horizon: investmentHorizon
                    });

                } else {
                    console.log('Enviando dados manuais...');
                    headers['Content-Type'] = 'application/json';
                    const portfolioItems = getAssets();
                    const data = {
                        investment_profile: investmentProfile,
                        investment_horizon: parseInt(investmentHorizon),
                        monthly_contribution: parseCurrencyToNumber(document.getElementById('monthlyContribution').value),
                        total_equity: parseCurrencyToNumber(document.getElementById('totalEquity').value),
                        current_portfolio: portfolioItems,
                        risk_tolerance: 5,
                        financial_goals: ['crescimento_patrimonio']
                    };
                    formData = JSON.stringify(data);
                    console.log('Dados JSON sendo enviados:', data);
                }

                console.log('Enviando requisição para:', endpoint);
                console.log('Headers:', headers);

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: headers,
                    body: formData
                });

                console.log('Status da resposta:', response.status);
                const responseText = await response.text();
                console.log('Resposta completa:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    console.error('Erro ao fazer parse da resposta:', e);
                    throw new Error('Erro ao processar resposta do servidor: ' + responseText);
                }

                if (!response.ok) {
                    throw new Error(result?.message || `Erro HTTP: ${response.status}`);
                }

                if(result.success) {
                    updateResults(result.data);
                } else {
                    throw new Error(result.message || 'Erro desconhecido na análise');
                }
            } catch (err) {
                console.error('Erro:', err);
                document.getElementById('results').innerHTML = `
                    <div class="analysis-section">
                        <div class="alert alert-danger">
                            ${err.message || 'Erro inesperado. Por favor, tente novamente mais tarde.'}
                        </div>
                    </div>
                `;
                document.getElementById('results').style.display = 'block';
            } finally {
                loadingOverlay.style.display = 'none';
                btn.disabled = false;
            }
        });

        function updateResults(data) {
            // Atualizar score de diversificação
            const diversificationScore = data.diversification_score;
            const diversificationBar = document.getElementById('diversificationBar');
            diversificationBar.style.width = diversificationScore + '%';
            diversificationBar.setAttribute('aria-valuenow', diversificationScore);
            diversificationBar.textContent = diversificationScore + '%';
            
            // Definir cor baseada no score
            if (diversificationScore < 30) {
                diversificationBar.className = 'progress-bar bg-danger';
            } else if (diversificationScore < 70) {
                diversificationBar.className = 'progress-bar bg-warning';
            } else {
                diversificationBar.className = 'progress-bar bg-success';
            }

            // Contexto Macroeconômico
            const macroContextHtml = `
                <div class="macro-context mb-4">
                    <div class="macro-context-title">Cenário Macroeconômico Atual</div>
                    <div class="macro-metric">
                        <span class="macro-metric-label">Taxa Selic</span>
                        <span class="macro-metric-value">14,25% <span class="trend-up">(↑)</span></span>
                    </div>
                    <div class="macro-metric">
                        <span class="macro-metric-label">IPCA Anual</span>
                        <span class="macro-metric-value">4,82% <span class="trend-down">(↓)</span></span>
                    </div>
                    <div class="macro-metric">
                        <span class="macro-metric-label">Câmbio USD/BRL</span>
                        <span class="macro-metric-value">R$ 4,95</span>
                    </div>
                    <div class="macro-metric">
                        <span class="macro-metric-label">Crescimento PIB (proj.)</span>
                        <span class="macro-metric-value">2,9%</span>
                    </div>
                </div>
            `;

            // Formatar e exibir métricas principais
            const metricsHtml = `
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Score de Diversificação</div>
                            <div class="metric-value ${diversificationScore < 50 ? 'metric-danger' : diversificationScore < 70 ? 'metric-warning' : 'metric-highlight'}">${diversificationScore}%</div>
                            <div class="metric-description">Nível de distribuição dos seus investimentos</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Relação Risco/Retorno</div>
                            <div class="metric-value ${data.risk_return_ratio < 1 ? 'metric-warning' : 'metric-highlight'}">${data.risk_return_ratio}</div>
                            <div class="metric-description">Equilíbrio entre risco e retorno esperado</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-title">Alinhamento com Mercado</div>
                            <div class="metric-value ${data.market_alignment < 3 ? 'metric-warning' : 'metric-highlight'}">${data.market_alignment}</div>
                            <div class="metric-description">Adequação ao cenário atual</div>
                        </div>
                    </div>
                </div>
            `;

            // Processar análise detalhada
            let detailedAnalysis = '';
            if (typeof data.detailed_analysis === 'string') {
                // Dividir a análise em parágrafos
                const paragraphs = data.detailed_analysis.split('\n').filter(p => p.trim());
                detailedAnalysis = paragraphs.map(p => {
                    // Destacar pontos importantes (começando com "•" ou "-" ou números)
                    if (p.trim().match(/^[•\-\d]/) || p.toLowerCase().includes('atenção') || p.toLowerCase().includes('importante')) {
                        return `<div class="analysis-highlight">${p}</div>`;
                    }
                    return `<p>${p}</p>`;
                }).join('');
            } else if (Array.isArray(data.detailed_analysis)) {
                detailedAnalysis = data.detailed_analysis.map(item => {
                    return `<div class="analysis-highlight">${item}</div>`;
                }).join('');
            }

            // Processar recomendações
            let recommendations = '';
            if (typeof data.recommendations === 'string') {
                const recs = data.recommendations.split('\n').filter(r => r.trim());
                recommendations = recs.map((rec, index) => `
                    <div class="recommendation-item">
                        <div class="recommendation-title">Recomendação ${index + 1}</div>
                        <div class="recommendation-content">${rec}</div>
                    </div>
                `).join('');
            } else if (Array.isArray(data.recommendations)) {
                recommendations = data.recommendations.map((rec, index) => `
                    <div class="recommendation-item">
                        <div class="recommendation-title">Recomendação ${index + 1}</div>
                        <div class="recommendation-content">${rec}</div>
                    </div>
                `).join('');
            }

            // Atualizar o conteúdo na página
            document.getElementById('results').innerHTML = `
                <h4 class="resultado-header mb-4">Análise da Carteira</h4>
                ${macroContextHtml}
                ${metricsHtml}
                <div class="analysis-section">
                    <div class="analysis-title">Análise Detalhada</div>
                    <div class="analysis-content">${detailedAnalysis || 'Análise detalhada não disponível'}</div>
                </div>
                <div class="analysis-section">
                    <div class="analysis-title">Recomendações</div>
                    <div class="analysis-content">${recommendations || 'Recomendações não disponíveis'}</div>
                </div>
            `;

            // Mostrar seção de resultados
            document.getElementById('results').style.display = 'block';
            document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html> 