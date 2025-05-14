<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico de Seguro de Vida - FinBrain</title>
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
        .hero-seguro {
            padding: 4rem 0 2rem 0;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .hero-seguro::before {
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
        .hero-seguro .container {
            position: relative;
            z-index: 1;
        }
        .card-seguro {
            background: #fff;
            color: #0033a0;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.15);
            transition: all 0.3s ease;
        }
        .card-seguro:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 51, 160, 0.25);
        }
        .btn-seguro {
            background: #43a047;
            color: #fff;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-seguro::after {
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
        .btn-seguro:hover {
            background: #388e3c;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-seguro:hover::after {
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

    <section class="hero-seguro">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeIn">Diagnóstico de Seguro de Vida</h1>
            <p class="lead mb-4" style="color:#cce0ff;">Descubra a cobertura ideal para proteger você e sua família. Análise personalizada baseada em IA para recomendações precisas de seguro de vida.</p>
        </div>
    </section>

    <div class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Analisando seus dados...<br>Aguarde um momento</div>
        </div>
    </div>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-seguro p-4 mb-4 animate__animated animate__fadeInUp">
                    <h3 class="h5 mb-4">Análise Personalizada</h3>
                    <form id="diagnosticForm">
                        <!-- Dados Pessoais -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Dados Pessoais</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="age" class="form-label">Idade</label>
                                    <input type="number" class="form-control" id="age" min="18" required>
                                    <div class="form-text">Sua idade atual</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="occupation" class="form-label">Ocupação</label>
                                    <input type="text" class="form-control" id="occupation" required>
                                    <div class="form-text">Sua profissão atual</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="income" class="form-label">Renda Anual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="income" required oninput="maskCurrency(this)">
                                    </div>
                                    <div class="form-text">Renda bruta anual</div>
                                </div>
                            </div>
                        </div>

                        <!-- Condições de Saúde -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Condições de Saúde</h4>
                            <div id="healthConditions">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" placeholder="Digite uma condição de saúde">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addField('healthConditions', 'condição de saúde')">
                                + Adicionar Condição
                            </button>
                            <div class="form-text">Ex: diabetes, hipertensão, etc.</div>
                        </div>

                        <!-- Fatores de Estilo de Vida -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Fatores de Estilo de Vida</h4>
                            <div id="lifestyleFactors">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" placeholder="Digite um fator de estilo de vida">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addField('lifestyleFactors', 'fator de estilo de vida')">
                                + Adicionar Fator
                            </button>
                            <div class="form-text">Ex: fumante, pratica esportes, etc.</div>
                        </div>

                        <!-- Histórico Familiar -->
                        <div class="mb-4">
                            <h4 class="h6 mb-3 text-primary">Histórico Familiar</h4>
                            <div id="familyHistory">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" placeholder="Digite uma condição do histórico familiar">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addField('familyHistory', 'condição do histórico familiar')">
                                + Adicionar Histórico
                            </button>
                            <div class="form-text">Ex: câncer na família, doenças cardíacas, etc.</div>
                        </div>

                        <button type="submit" class="btn btn-seguro w-100">Analisar Perfil de Risco</button>
                    </form>
                </div>

                <!-- Resultados -->
                <div id="results" class="resultado-section animate__animated animate__fadeIn" style="display: none;">
                    <h4 class="resultado-header">Análise de Risco</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Score de Risco</h5>
                                <div class="progress mb-2">
                                    <div id="riskBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                                <small id="riskComparison" class="text-muted d-block mt-2"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Cobertura Sugerida</h5>
                                <p class="h4 mb-0 text-primary" id="suggestedCoverage">-</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="result-card text-center">
                                <h5 class="h6 mb-3">Prêmio Mensal</h5>
                                <p class="h4 mb-0 text-success" id="monthlyPremium">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="result-card mt-4">
                        <h5 class="h6 mb-3">Análise Detalhada</h5>
                        <div id="detailedAnalysis" class="resultado-content"></div>
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
    <script>
        function addField(containerId, placeholder) {
            const container = document.getElementById(containerId);
            const newField = document.createElement('div');
            newField.className = 'input-group mb-2';
            newField.innerHTML = `
                <input type="text" class="form-control" placeholder="Digite uma ${placeholder}">
                <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
            `;
            container.appendChild(newField);
        }

        function removeField(button) {
            button.parentElement.remove();
        }

        function getFieldValues(containerId) {
            const container = document.getElementById(containerId);
            const inputs = container.getElementsByTagName('input');
            return Array.from(inputs).map(input => input.value).filter(value => value.trim() !== '');
        }

        function maskCurrency(input) {
            let v = input.value.replace(/\D/g, '');
            v = (v/100).toFixed(2) + '';
            v = v.replace('.', ',');
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            input.value = v;
        }

        document.getElementById('diagnosticForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.querySelector('.loading-overlay');
            loadingOverlay.style.display = 'flex';
            
            const btn = this.querySelector('button[type=submit]');
            btn.disabled = true;

            try {
                const data = {
                    age: document.getElementById('age').value,
                    occupation: document.getElementById('occupation').value,
                    income: parseFloat(document.getElementById('income').value.replace(/\./g, '').replace(',', '.')),
                    health_conditions: getFieldValues('healthConditions'),
                    lifestyle_factors: getFieldValues('lifestyleFactors'),
                    family_history: getFieldValues('familyHistory')
                };

                console.log('Enviando dados:', data);
                const response = await fetch('/api/diagnostico-seguro', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                console.log('Status da resposta:', response.status);
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro completo:', errorText);
                    const errorData = JSON.parse(errorText);
                    throw new Error(errorData?.message || `HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Resultado:', result);
                
                if(result.success) {
                    // Atualizar resultados
                    const riskScore = result.data.risk_score;
                    const riskBar = document.getElementById('riskBar');
                    riskBar.style.width = riskScore + '%';
                    riskBar.setAttribute('aria-valuenow', riskScore);
                    riskBar.textContent = riskScore + '%';
                    
                    // Definir cor baseada no score
                    if (riskScore < 30) {
                        riskBar.className = 'progress-bar bg-success';
                    } else if (riskScore < 70) {
                        riskBar.className = 'progress-bar bg-warning';
                    } else {
                        riskBar.className = 'progress-bar bg-danger';
                    }

                    // Atualizar outros elementos
                    document.getElementById('riskComparison').textContent = result.data.risk_comparison || '';
                    document.getElementById('suggestedCoverage').textContent = result.data.suggested_coverage || '-';
                    document.getElementById('monthlyPremium').textContent = result.data.monthly_premium || '-';
                    document.getElementById('detailedAnalysis').innerHTML = result.data.detailed_analysis.replace(/\n/g, '<br>');
                    
                    // Mostrar seção de resultados
                    document.getElementById('results').style.display = 'block';
                    document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
                } else {
                    document.getElementById('detailedAnalysis').innerHTML = '<div class="alert alert-danger">Erro: ' + (result.message || 'Falha na análise') + '</div>';
                    document.getElementById('results').style.display = 'block';
                }
            } catch (err) {
                console.error('Erro:', err);
                document.getElementById('detailedAnalysis').innerHTML = '<div class="alert alert-danger">Erro inesperado. Por favor, tente novamente.</div>';
                document.getElementById('results').style.display = 'block';
            } finally {
                loadingOverlay.style.display = 'none';
                btn.disabled = false;
            }
        });

        // Validação da idade
        document.getElementById('age').addEventListener('input', function() {
            if (this.value < 18) this.value = 18;
        });
    </script>
</body>
</html> 