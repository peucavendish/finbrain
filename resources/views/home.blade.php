<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinBrain - Simule seu futuro financeiro</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .navbar-brand { font-weight: 700; color: #1a237e !important; }
        .hero { background: linear-gradient(90deg, #e3e9f7 60%, #fff 100%); padding: 4rem 0 2rem 0; }
        .hero-title { font-size: 2.5rem; font-weight: 700; color: #1a237e; }
        .hero-sub { font-size: 1.25rem; color: #374151; }
        .sim-card { border: none; border-radius: 1rem; box-shadow: 0 2px 16px 0 #e3e9f7; transition: box-shadow .2s; }
        .sim-card:hover { box-shadow: 0 4px 32px 0 #c7d0e6; }
        .sim-icon { font-size: 2.5rem; border-radius: 0.75rem; padding: 0.5rem 0.8rem; color: #fff; display: inline-block; }
        .sim-apos { background: #3949ab; }
        .sim-renda { background: #43a047; }
        .sim-comp { background: #8e24aa; }
        .sim-seguro { background: #ff9800; }
        .footer { background: #fff; border-top: 1px solid #e3e9f7; color: #6b7280; font-size: 0.95rem; }
        .nav-link { color: #374151 !important; font-weight: 500; }
        .nav-link.active, .nav-link:hover { color: #1a237e !important; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">FinBrain</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="#simuladores">Simuladores</a></li>
                <li class="nav-item"><a class="nav-link" href="#planning">Planejamento</a></li>
                <li class="nav-item"><a class="nav-link" href="#ferramentas">Ferramentas</a></li>
                <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero pt-5 mt-5">
    <div class="container text-center">
        <h1 class="hero-title mb-3">Simule seu futuro financeiro com inteligência artificial</h1>
        <p class="hero-sub mb-4">Tome decisões mais inteligentes sobre seus investimentos com a ajuda da IA. Visualize cenários, compare estratégias e planeje sua liberdade financeira.</p>
        <a href="#simuladores" class="btn btn-primary btn-lg px-4 shadow">Começar agora</a>
    </div>
</section>

<section id="simuladores" class="container py-5">
    <div class="row mb-4">
        <div class="col text-center">
            <h2 class="fw-bold" style="color:#1a237e">Simuladores Inteligentes</h2>
            <p class="text-muted">Escolha um simulador e veja projeções personalizadas para o seu perfil.</p>
        </div>
    </div>
    <div class="row g-4 justify-content-center">
        <div class="col-md-3">
            <div class="card sim-card h-100 text-center p-4">
                <div class="sim-icon sim-apos mb-3"><i class="bi bi-piggy-bank"></i></div>
                <h5 class="fw-bold mb-2">Simulador de Aposentadoria</h5>
                <p class="text-muted">Calcule quanto você precisa investir para se aposentar com conforto e segurança.</p>
                <a href="#" class="btn btn-outline-primary">Simular agora</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sim-card h-100 text-center p-4">
                <div class="sim-icon sim-renda mb-3"><i class="bi bi-cash-coin"></i></div>
                <h5 class="fw-bold mb-2">Simulador de Renda Passiva</h5>
                <p class="text-muted">Descubra quanto precisa investir para viver de renda e conquistar sua liberdade financeira.</p>
                <a href="#" class="btn btn-outline-success">Simular agora</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sim-card h-100 text-center p-4">
                <div class="sim-icon sim-comp mb-3"><i class="bi bi-bar-chart-line"></i></div>
                <h5 class="fw-bold mb-2">Comparador de Investimentos</h5>
                <p class="text-muted">Compare CDI, Ibovespa, S&P500 e veja qual estratégia faz mais sentido para você.</p>
                <a href="#" class="btn btn-outline-secondary">Comparar agora</a>
            </div>
        </div>
        <!-- Novo Card Diagnóstico Seguro de Vida -->
        <div class="col-md-3">
            <div class="card sim-card h-100 text-center p-4">
                <div class="sim-icon sim-seguro mb-3"><i class="bi bi-shield-check"></i></div>
                <h5 class="fw-bold mb-2">Diagnóstico de Seguro de Vida <span class="badge bg-warning text-dark">AI</span></h5>
                <p class="text-muted">Descubra em segundos se você e sua família estão protegidos na medida certa.</p>
                <button class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalSeguroVida">Diagnosticar agora</button>
            </div>
        </div>
    </div>
</section>

<!-- Modal Diagnóstico Seguro de Vida -->
<div class="modal fade" id="modalSeguroVida" tabindex="-1" aria-labelledby="modalSeguroVidaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSeguroVidaLabel">Diagnóstico de Seguro de Vida com IA</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formSeguroVida">
          <div class="mb-3">
            <label for="idade" class="form-label">Sua idade</label>
            <input type="number" class="form-control" id="idade" name="idade" min="18" max="80" required>
          </div>
          <div class="mb-3">
            <label for="dependentes" class="form-label">Quantos dependentes?</label>
            <input type="number" class="form-control" id="dependentes" name="dependentes" min="0" max="10" required>
          </div>
          <div class="mb-3">
            <label for="renda" class="form-label">Renda mensal (R$)</label>
            <input type="number" class="form-control" id="renda" name="renda" min="500" step="100" required>
          </div>
          <div class="mb-3">
            <label for="profissao" class="form-label">Profissão</label>
            <input type="text" class="form-control" id="profissao" name="profissao" required>
          </div>
          <button type="submit" class="btn btn-warning w-100">Analisar com IA</button>
        </form>
        <div id="resultadoSeguroVida" class="mt-4 d-none">
          <div class="alert alert-success">
            <h6 class="mb-2">Diagnóstico IA:</h6>
            <div id="textoResultado"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="footer py-4 mt-5">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-12">
                &copy; {{ date('Y') }} FinBrain. Todos os direitos reservados.
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Diagnóstico simulado de seguro de vida
const form = document.getElementById('formSeguroVida');
const resultado = document.getElementById('resultadoSeguroVida');
const textoResultado = document.getElementById('textoResultado');
if(form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const idade = +form.idade.value;
    const dependentes = +form.dependentes.value;
    const renda = +form.renda.value;
    const profissao = form.profissao.value;
    // Lógica simulada de IA
    let valorSeguro = renda * 36 + dependentes * 20000;
    if(idade > 50) valorSeguro *= 0.8;
    let texto = `Com base nos dados fornecidos, recomendamos um seguro de vida de <b>R$ ${valorSeguro.toLocaleString('pt-BR')}</b> para garantir a segurança financeira da sua família.`;
    if(dependentes === 0) texto += '<br><span class="text-warning">Você não informou dependentes. Considere se há pessoas que dependem de você financeiramente.</span>';
    texto += `<br><small class='text-muted'>* Diagnóstico gerado por IA. Consulte um especialista para análise personalizada.</small>`;
    textoResultado.innerHTML = texto;
    resultado.classList.remove('d-none');
  });
}
</script>
</body>
</html> 