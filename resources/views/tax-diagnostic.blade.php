<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico Tributário - FinBrain</title>
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
        .hero-tax {
            padding: 4rem 0 2rem 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-tax::before {
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
        .hero-tax .container {
            position: relative;
            z-index: 1;
        }
        .card-tax {
            background: #fff;
            color: #2c3e50;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
            transition: all 0.3s ease;
        }
        .card-tax:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
        }
        .btn-tax {
            background: #0033a0;
            color: #fff;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-tax:hover {
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
        .income-item, .asset-item {
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
            background: rgba(44, 62, 80, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #27ae60;
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
        .tax-consideration {
            background: #fff3e0;
            border-left: 4px solid #0033a0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }
        .tax-consideration:hover {
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

    <section class="hero-tax">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Diagnóstico Tributário</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Análise inteligente da sua situação tributária com recomendações personalizadas para otimização fiscal.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Analisando sua situação tributária...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-tax p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Análise Tributária</h3>
                    <form id="taxForm">
                        @csrf
                        <!-- Informações de Renda -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Informações de Renda</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="total_income" class="form-label">Renda Anual Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="total_income" name="total_income" required oninput="maskCurrency(this)">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tax_regime" class="form-label">Regime Tributário Atual</label>
                                    <select class="form-control" id="tax_regime" name="tax_regime" required>
                                        <option value="">Selecione...</option>
                                        <option value="pf">Pessoa Física</option>
                                        <option value="mei">MEI</option>
                                        <option value="simples">Simples Nacional</option>
                                        <option value="presumido">Lucro Presumido</option>
                                        <option value="real">Lucro Real</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
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

                        <!-- Fontes de Renda -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Fontes de Renda</h4>
                            <div id="incomeList"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addIncome()">
                                + Adicionar Fonte de Renda
                            </button>
                        </div>

                        <!-- Patrimônio -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Patrimônio</h4>
                            <div id="assetsList"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addAsset()">
                                + Adicionar Bem
                            </button>
                        </div>

                        <!-- Deduções -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Deduções</h4>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="previdencia" id="deduction_previdencia">
                                    <label class="form-check-label" for="deduction_previdencia">
                                        Contribuição para Previdência
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="saude" id="deduction_saude">
                                    <label class="form-check-label" for="deduction_saude">
                                        Despesas Médicas
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="educacao" id="deduction_educacao">
                                    <label class="form-check-label" for="deduction_educacao">
                                        Despesas com Educação
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="dependentes" id="deduction_dependentes">
                                    <label class="form-check-label" for="deduction_dependentes">
                                        Dependentes
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-tax w-100">Analisar Situação Tributária</button>
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

        function addIncome() {
            const container = document.getElementById('incomeList');
            const incomeDiv = document.createElement('div');
            incomeDiv.className = 'income-item';
            incomeDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Renda</label>
                        <select class="form-control income-type" required>
                            <option value="">Selecione...</option>
                            <option value="salario">Salário/Pró-labore</option>
                            <option value="aluguel">Aluguel</option>
                            <option value="dividendos">Dividendos</option>
                            <option value="investimentos">Rendimentos de Investimentos</option>
                            <option value="servicos">Prestação de Serviços</option>
                            <option value="outros">Outras Rendas</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Anual</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control income-value" required oninput="maskCurrency(this)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIncome(this)">
                    Remover Fonte de Renda
                </button>
            `;
            container.appendChild(incomeDiv);
        }

        function removeIncome(button) {
            button.closest('.income-item').remove();
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
                            <option value="veiculos">Veículos</option>
                            <option value="investimentos">Investimentos Financeiros</option>
                            <option value="participacoes">Participações Societárias</option>
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

        function getIncomeSources() {
            const sources = [];
            document.querySelectorAll('.income-item').forEach(item => {
                const value = parseCurrencyToNumber(item.querySelector('.income-value').value);
                if (!isNaN(value) && value > 0) {
                    sources.push({
                        type: item.querySelector('.income-type').value,
                        value: value
                    });
                }
            });
            return sources;
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

        function getDeductions() {
            return Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
        }

        document.getElementById('taxForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

            try {
                const totalIncome = parseCurrencyToNumber(document.getElementById('total_income').value);
                const incomeSources = getIncomeSources();
                const assets = getAssets();

                if (incomeSources.length === 0) {
                    throw new Error('É necessário adicionar pelo menos uma fonte de renda.');
                }

                if (isNaN(totalIncome) || totalIncome <= 0) {
                    throw new Error('A renda total anual deve ser maior que zero.');
                }

                const data = {
                    total_income: totalIncome,
                    income_sources: incomeSources,
                    assets: assets,
                    tax_regime: document.getElementById('tax_regime').value,
                    has_company: document.querySelector('input[name="has_company"]:checked').value === "1",
                    deductions: getDeductions()
                };

                console.log('Enviando dados:', data); // Debug log

                const response = await fetch('/api/diagnostico-tributario', {
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
                    // Criar HTML para métricas
                    const metricsHtml = `
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Carga Tributária</div>
                                    <div class="metric-value">${result.data.tax_burden.percentage}%</div>
                                    <div class="metric-description">R$ ${result.data.tax_burden.monthly_burden.toLocaleString('pt-BR', {minimumFractionDigits: 2})} / mês</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Score de Otimização</div>
                                    <div class="metric-value">${result.data.optimization_score}%</div>
                                    <div class="metric-description">Nível de eficiência tributária</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-title">Economia Potencial</div>
                                    <div class="metric-value">${result.data.potential_savings.percentage}%</div>
                                    <div class="metric-description">R$ ${result.data.potential_savings.monthly.toLocaleString('pt-BR', {minimumFractionDigits: 2})} / mês</div>
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

                    // Criar HTML para considerações tributárias
                    const taxConsiderationsHtml = `
                        <div class="analysis-section">
                            <div class="analysis-title">Considerações Tributárias</div>
                            <div class="analysis-content">
                                ${result.data.tax_considerations.map(item => `
                                    <div class="tax-consideration">
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
                        <h4 class="resultado-header mb-4">Análise da Situação Tributária</h4>
                        ${metricsHtml}
                        ${analysisHtml}
                        ${taxConsiderationsHtml}
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

        // Adicionar uma fonte de renda e um bem inicialmente
        addIncome();
        addAsset();
    </script>
</body>
</html> 