<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico Holding - FinBrain</title>
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
        .hero-section {
            padding: 4rem 0 2rem 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
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
        .hero-section .container {
            position: relative;
            z-index: 1;
        }
        .card-main {
            background: #fff;
            color: #2c3e50;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
            transition: all 0.3s ease;
        }
        .card-main:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
        }
        .btn-main {
            background: #0033a0;
            color: #fff;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-main:hover {
            background: #001f60;
            color: #fff;
            transform: translateY(-2px);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
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
        .asset-item {
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
            border-top: 5px solid #0033a0;
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
        .consideration-item {
            background: #fff3e0;
            border-left: 4px solid #0033a0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }
        .consideration-item:hover {
            transform: translateX(5px);
            background: #fff7e6;
        }
        .next-step {
            background: #e8f5e9;
            border-left: 4px solid #0033a0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }
        .next-step:hover {
            transform: translateX(5px);
            background: #f1f8f1;
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

    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Diagnóstico Holding Patrimonial</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Análise personalizada da necessidade de estruturação patrimonial via holding, considerando aspectos fiscais, sucessórios e de proteção patrimonial.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="mt-3 text-white">Analisando sua estrutura patrimonial...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-main p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Análise de Necessidade de Holding</h3>
                    <form id="holdingForm">
                        @csrf
                        <!-- Informações Patrimoniais -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Informações Patrimoniais</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="total_assets" class="form-label">Patrimônio Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="total_assets" name="total_assets" required oninput="maskCurrency(this)">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="current_tax_exposure" class="form-label">Exposição Fiscal Atual (Anual)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="current_tax_exposure" name="current_tax_exposure" required oninput="maskCurrency(this)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estrutura Atual -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Estrutura Atual</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="business_count" class="form-label">Quantidade de Empresas</label>
                                    <input type="number" class="form-control" id="business_count" name="business_count" min="0" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="real_estate_count" class="form-label">Quantidade de Imóveis</label>
                                    <input type="number" class="form-control" id="real_estate_count" name="real_estate_count" min="0" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="family_members" class="form-label">Membros da Família</label>
                                    <input type="number" class="form-control" id="family_members" name="family_members" min="1" required>
                                </div>
                            </div>
                        </div>

                        <!-- Ativos -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Composição Patrimonial</h4>
                            <div id="assetsList"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAsset()">
                                + Adicionar Ativo
                            </button>
                        </div>

                        <!-- Fatores Adicionais -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Fatores Adicionais</h4>
                            <div class="mb-3">
                                <label class="form-label d-block">Possui Plano Sucessório?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="has_succession_plan" id="has_plan_yes" value="1">
                                    <label class="form-check-label" for="has_plan_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="has_succession_plan" id="has_plan_no" value="0" checked>
                                    <label class="form-check-label" for="has_plan_no">Não</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Possui Ativos Internacionais?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="has_international_assets" id="has_international_yes" value="1">
                                    <label class="form-check-label" for="has_international_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="has_international_assets" id="has_international_no" value="0" checked>
                                    <label class="form-check-label" for="has_international_no">Não</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-main w-100">Analisar Necessidade de Holding</button>
                    </form>
                </div>

                <!-- Resultados -->
                <div id="results" class="animate__animated animate__fadeIn" style="display: none;">
                    <!-- O conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function maskCurrency(input) {
            let v = input.value.replace(/\D/g, '');
            v = (v/100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            input.value = v;
        }

        function addAsset() {
            const container = document.getElementById('assetsList');
            const assetDiv = document.createElement('div');
            assetDiv.className = 'asset-item';
            assetDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Ativo</label>
                        <select class="form-control asset-type" required>
                            <option value="">Selecione...</option>
                            <option value="imoveis">Imóveis</option>
                            <option value="empresas">Participações Societárias</option>
                            <option value="investimentos">Investimentos Financeiros</option>
                            <option value="veiculos">Veículos</option>
                            <option value="outros">Outros Ativos</option>
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
                    Remover Ativo
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

        document.getElementById('holdingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

            try {
                const totalAssets = parseCurrencyToNumber(document.getElementById('total_assets').value);
                const currentTaxExposure = parseCurrencyToNumber(document.getElementById('current_tax_exposure').value);
                const assets = getAssets();

                if (assets.length === 0) {
                    throw new Error('É necessário adicionar pelo menos um ativo.');
                }

                const data = {
                    total_assets: totalAssets,
                    current_tax_exposure: currentTaxExposure,
                    assets: assets,
                    business_count: parseInt(document.getElementById('business_count').value),
                    real_estate_count: parseInt(document.getElementById('real_estate_count').value),
                    family_members: parseInt(document.getElementById('family_members').value),
                    has_succession_plan: document.querySelector('input[name="has_succession_plan"]:checked').value === "1",
                    has_international_assets: document.querySelector('input[name="has_international_assets"]:checked').value === "1"
                };

                console.log('Enviando dados:', data);

                const response = await fetch('/api/diagnostico-holding', {
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
                console.log('Resposta:', result);
                
                if (result.success) {
                    // Criar HTML para métricas
                    const metricsHtml = `
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Score de Complexidade</div>
                                    <div class="metric-value">${result.data.complexity_score}%</div>
                                    <div class="metric-description">${result.data.holding_recommendation.recommendation}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Economia Fiscal Potencial</div>
                                    <div class="metric-value">R$ ${result.data.potential_benefits.tax_savings.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                                    <div class="metric-description">Economia anual estimada</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Nível de Proteção</div>
                                    <div class="metric-value">${result.data.potential_benefits.protection_level}%</div>
                                    <div class="metric-description">Proteção patrimonial estimada</div>
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

                    // Criar HTML para considerações
                    const considerationsHtml = `
                        <div class="analysis-section">
                            <div class="analysis-title">Considerações Importantes</div>
                            <div class="analysis-content">
                                ${result.data.considerations.map(item => `
                                    <div class="consideration-item">
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
                        <h4 class="mb-4">Resultado da Análise</h4>
                        ${metricsHtml}
                        ${analysisHtml}
                        ${considerationsHtml}
                        ${nextStepsHtml}
                    `;

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

        // Adicionar um ativo inicialmente
        addAsset();
    </script>
</body>
</html> 