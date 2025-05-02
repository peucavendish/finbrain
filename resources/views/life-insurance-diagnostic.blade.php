<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico de Seguro de Vida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .risk-score {
            font-size: 2rem;
            font-weight: bold;
        }
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
        .result-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-5">Diagnóstico de Seguro de Vida</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="diagnosticForm" class="card p-4">
                    <!-- Dados Pessoais -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">Dados Pessoais</h3>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="age" class="form-label">Idade</label>
                                <input type="number" class="form-control" id="age" min="18" max="100" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="occupation" class="form-label">Ocupação</label>
                                <input type="text" class="form-control" id="occupation" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="income" class="form-label">Renda Anual (R$)</label>
                                <input type="number" class="form-control" id="income" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Condições de Saúde -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">Condições de Saúde</h3>
                        <div id="healthConditions">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Digite uma condição de saúde">
                                <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('healthConditions')">
                            + Adicionar Condição
                        </button>
                    </div>

                    <!-- Fatores de Estilo de Vida -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">Fatores de Estilo de Vida</h3>
                        <div id="lifestyleFactors">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Digite um fator de estilo de vida">
                                <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('lifestyleFactors')">
                            + Adicionar Fator
                        </button>
                    </div>

                    <!-- Histórico Familiar -->
                    <div class="mb-4">
                        <h3 class="h5 mb-3">Histórico Familiar</h3>
                        <div id="familyHistory">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Digite uma condição do histórico familiar">
                                <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remover</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('familyHistory')">
                            + Adicionar Histórico
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        Realizar Diagnóstico
                    </button>
                </form>

                <!-- Resultados -->
                <div id="results" class="mt-4" style="display: none;">
                    <h2 class="h4 mb-4">Resultado da Análise</h2>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="result-card">
                                <h3 class="h6">Score de Risco</h3>
                                <p class="risk-score" id="riskScore">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card">
                                <h3 class="h6">Cobertura Sugerida</h3>
                                <p class="h4" id="suggestedCoverage">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card">
                                <h3 class="h6">Prêmio Mensal Estimado</h3>
                                <p class="h4" id="monthlyPremium">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="result-card mt-4">
                        <h3 class="h6">Análise Detalhada</h3>
                        <p id="detailedAnalysis" class="mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addField(containerId) {
            const container = document.getElementById(containerId);
            const newField = document.createElement('div');
            newField.className = 'input-group mb-2';
            newField.innerHTML = `
                <input type="text" class="form-control" placeholder="Digite uma condição">
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

        document.getElementById('diagnosticForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Analisando...';

            try {
                const data = {
                    age: document.getElementById('age').value,
                    occupation: document.getElementById('occupation').value,
                    income: document.getElementById('income').value,
                    health_conditions: getFieldValues('healthConditions'),
                    lifestyle_factors: getFieldValues('lifestyleFactors'),
                    family_history: getFieldValues('familyHistory')
                };

                const response = await fetch('http://127.0.0.1:8002/api/ai-analysis', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Atualizar resultados
                    document.getElementById('riskScore').textContent = result.data.risk_score + '%';
                    document.getElementById('riskScore').className = 'risk-score ' + 
                        (result.data.risk_score <= 30 ? 'risk-low' : 
                         result.data.risk_score <= 70 ? 'risk-medium' : 'risk-high');
                    
                    document.getElementById('suggestedCoverage').textContent = 
                        'R$ ' + formatCurrency(result.data.suggested_coverage);
                    
                    document.getElementById('monthlyPremium').textContent = 
                        'R$ ' + formatCurrency(result.data.monthly_premium_estimate);
                    
                    document.getElementById('detailedAnalysis').textContent = result.data.recommendation;
                    
                    document.getElementById('results').style.display = 'block';
                } else {
                    alert('Erro ao realizar análise: ' + result.message);
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao realizar a análise. Por favor, tente novamente.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Realizar Diagnóstico';
            }
        });

        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        }
    </script>
</body>
</html> 