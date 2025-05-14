<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinBrain - Inteligência Financeira</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0033a0;
            --secondary-color: #00a3e0;
            --dark-blue: #001f60;
            --light-blue: #cce0ff;
            --success-color: #43a047;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #333;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-blue) 100%);
            min-height: 80vh;
            position: relative;
            overflow: hidden;
            padding: 6rem 0;
            color: white;
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

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--light-blue);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 51, 160, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 48px rgba(0, 51, 160, 0.15);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--light-blue);
        }

        .feature-icon i {
            font-size: 1.75rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
            flex-grow: 1;
            margin-bottom: 1.5rem;
        }

        .feature-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .feature-link:hover {
            color: var(--dark-blue);
            transform: translateX(5px);
        }

        .feature-link i {
            margin-left: 0.5rem;
            font-size: 1.1em;
        }

        .curved-section-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }

        .curved-section-bottom svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 69px;
        }

        .curved-section-bottom .shape-fill {
            fill: #FFFFFF;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">FinBrain</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="/register">Cadastre-se</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">
                        Inteligência artificial a favor do seu 
                        <span class="highlight-text">patrimônio</span>
                    </h1>
                    <p class="hero-subtitle">
                        Transformamos incertezas em oportunidades com análises personalizadas 
                        e recomendações inteligentes para suas decisões financeiras.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-light btn-lg">Comece Agora</a>
                        <a href="#saiba-mais" class="btn btn-outline-light btn-lg">Saiba Mais</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="/images/hero-image.svg" alt="FinBrain Analytics" class="img-fluid" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
        <div class="curved-section-bottom">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <section class="features py-5" id="saiba-mais">
        <div class="container">
            <div class="row g-4">
                <!-- Análise de Carteira -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                        <h3 class="feature-title">Análise de Carteira</h3>
                        <p class="feature-description">
                            Análise inteligente da sua carteira de investimentos com recomendações 
                            personalizadas baseadas no cenário atual do mercado brasileiro.
                        </p>
                        <a href="{{ route('diagnostico-carteira') }}" class="feature-link">
                            Fazer Diagnóstico <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Viver de Renda -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye text-success"></i>
                        </div>
                        <h3 class="feature-title">Viver de Renda</h3>
                        <p class="feature-description">
                            Planejamento personalizado para alcançar a independência financeira 
                            e viver de renda passiva.
                        </p>
                        <a href="{{ route('viver-de-renda-ia') }}" class="feature-link">
                            Fazer Simulação <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Proteção Patrimonial -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt text-danger"></i>
                        </div>
                        <h3 class="feature-title">Proteção Patrimonial</h3>
                        <p class="feature-description">
                            Análise inteligente de seguros de vida para proteger seu patrimônio 
                            e garantir o futuro da sua família.
                        </p>
                        <a href="{{ route('diagnostico-seguro-vida') }}" class="feature-link">
                            Fazer Diagnóstico <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Diagnóstico Sucessório -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-balance-scale text-success"></i>
                        </div>
                        <h3 class="feature-title">Diagnóstico Sucessório</h3>
                        <p class="feature-description">
                            Análise inteligente do seu planejamento sucessório com recomendações 
                            personalizadas baseadas na legislação brasileira.
                        </p>
                        <a href="{{ route('diagnostico-sucessorio') }}" class="feature-link">
                            Fazer Diagnóstico <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Diagnóstico Tributário -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calculator text-primary"></i>
                        </div>
                        <h3 class="feature-title">Diagnóstico Tributário</h3>
                        <p class="feature-description">
                            Análise completa da sua situação fiscal, identificando oportunidades 
                            de otimização tributária e recomendações personalizadas.
                        </p>
                        <a href="{{ route('diagnostico-tributario') }}" class="feature-link">
                            Fazer Diagnóstico <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Diagnóstico Holding -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building text-success"></i>
                        </div>
                        <h3 class="feature-title">Diagnóstico Holding</h3>
                        <p class="feature-description">
                            Análise personalizada da necessidade de estruturação patrimonial via holding, 
                            considerando aspectos fiscais, sucessórios e de proteção patrimonial.
                        </p>
                        <a href="{{ route('diagnostico-holding') }}" class="feature-link">
                            Fazer Diagnóstico <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 