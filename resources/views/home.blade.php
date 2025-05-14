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
        <a class="navbar-brand" href="#">
            <img src="" alt="" style="height:32px;vertical-align:middle;margin-right:8px;"> FinBrain
        </a>
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

<section class="hero pt-5 mt-5" style="background: #0033a0; color: #fff;">
    <div class="container text-center">
        <h1 class="hero-title mb-3" style="color:#fff;">Simule seu futuro financeiro com inteligência artificial</h1>
        <p class="hero-sub mb-4" style="color:#cce0ff;">Tome decisões mais inteligentes sobre seus investimentos com a ajuda da IA. Visualize cenários, compare estratégias e planeje sua liberdade financeira.</p>
        <a href="#simuladores" class="btn btn-warning btn-lg px-4 shadow">Começar agora</a>
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
            <div class="card sim-card h-100 text-center p-4" style="background: #0033a0; color: #fff; border: 2px solid #1976d2;">
                <div class="sim-icon sim-seguro mb-3" style="background: #1976d2;"><i class="bi bi-shield-check"></i></div>
                <h5 class="fw-bold mb-2">Diagnóstico de Seguro de Vida <span class="badge bg-warning text-dark">AI</span></h5>
                <p class="text-light">Descubra em segundos se você e sua família estão protegidos na medida certa.</p>
                <a href="/diagnostico-seguro-vida" class="btn btn-warning text-white fw-bold">Diagnosticar agora</a>
            </div>
        </div>
        <!-- Card Viver de Renda com IA -->
        <div class="col-md-3">
            <div class="card sim-card h-100 text-center p-4" style="background: #0a2540; color: #fff; border: 2px solid #43a047;">
                <div class="sim-icon sim-renda mb-3" style="background: #43a047;"><i class="bi bi-graph-up-arrow"></i></div>
                <h5 class="fw-bold mb-2">Viver de Renda <span class="badge bg-info text-dark">AI</span></h5>
                <p class="text-light">Simule como conquistar renda passiva vitalícia com inteligência artificial.</p>
                <a href="/viver-de-renda-ia" class="btn btn-info text-white fw-bold">Simular agora</a>
            </div>
        </div>
    </div>
</section>

<footer class="footer py-4 mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                &copy; {{ date('Y') }} FinBrain. Todos os direitos reservados.
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span class="me-2">Powered by</span>
                <img src=" alt=" style="height:28px;vertical-align:middle;">
            </div>
        </div>
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 