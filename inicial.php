<?php
// Iniciar sessão para verificar se usuário está logado
session_start();
$usuario_logado = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Dados simulados para demonstração
$estatisticas = [
    'usuarios_ativos' => 1250,
    'projetos_concluidos' => 89,
    'satisfacao_clientes' => 98,
    'tempo_online' => '24/7'
];

$noticias_recentes = [
    [
        'titulo' => 'Nova atualização do sistema disponível',
        'resumo' => 'Melhorias de performance e novas funcionalidades',
        'data' => '2024-01-15',
        'categoria' => 'Sistema'
    ],
    [
        'titulo' => 'Empresa atinge marca histórica',
        'resumo' => 'Celebramos 10.000 usuários ativos em nossa plataforma',
        'data' => '2024-01-10',
        'categoria' => 'Empresa'
    ],
    [
        'titulo' => 'Novos tutoriais disponíveis',
        'resumo' => 'Aprenda a usar todas as funcionalidades do sistema',
        'data' => '2024-01-05',
        'categoria' => 'Suporte'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Nossa Plataforma</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.1"><polygon points="1000,100 1000,0 0,100"/></svg>');
        }

        .header-content h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .header-content p {
            font-size: 1.3rem;
            margin-bottom: 25px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .user-welcome {
            background: rgba(255,255,255,0.2);
            padding: 15px 25px;
            border-radius: 50px;
            display: inline-block;
            backdrop-filter: blur(10px);
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.3);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }

        /* Stats Section */
        .stats-section {
            margin-bottom: 50px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
        }

        /* Features Section */
        .features-section {
            margin-bottom: 50px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2.2rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            padding: 35px 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            color: #333;
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .feature-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .feature-link:hover {
            gap: 10px;
        }

        /* News Section */
        .news-section {
            margin-bottom: 50px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .news-category {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .news-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .news-date {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .news-content {
            padding: 20px;
        }

        .news-summary {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .news-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions-section {
            margin-bottom: 50px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .action-item {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .action-text {
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Footer */
        .main-footer {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 50px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #764ba2;
        }

        .copyright {
            color: #666;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content h1 {
                font-size: 2.2rem;
            }
            
            .header-content p {
                font-size: 1.1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 200px;
                justify-content: center;
            }
            
            .stats-grid,
            .features-grid,
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* User Menu */
        .user-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="main-header">
            <?php if ($usuario_logado): ?>
            <div class="user-menu">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($usuario_logado, 0, 1)); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="header-content">
                <h1>Bem-vindo à Nossa Plataforma</h1>
                <p>Soluções inovadoras para transformar seu negócio e impulsionar seus resultados</p>
                
                <?php if ($usuario_logado): ?>
                <div class="user-welcome">
                    👋 Olá, <strong><?php echo htmlspecialchars($usuario_logado); ?></strong>! Boas-vindas de volta.
                </div>
                <?php endif; ?>

                <div class="cta-buttons">
                    <?php if ($usuario_logado): ?>
                        <a href="dashboard.php" class="btn btn-primary">📊 Acessar Dashboard</a>
                        <a href="suporte.php" class="btn btn-secondary">💬 Precisa de Ajuda?</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">🚀 Começar Agora</a>
                        <a href="sobre.php" class="btn btn-secondary">📖 Conheça Mais</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Statistics -->
        <section class="stats-section animate-fade-in">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?php echo number_format($estatisticas['usuarios_ativos']); ?></div>
                    <div class="stat-label">Usuários Ativos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-number"><?php echo $estatisticas['projetos_concluidos']; ?>+</div>
                    <div class="stat-label">Projetos Concluídos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number"><?php echo $estatisticas['satisfacao_clientes']; ?>%</div>
                    <div class="stat-label">Satisfação dos Clientes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🕒</div>
                    <div class="stat-number"><?php echo $estatisticas['tempo_online']; ?></div>
                    <div class="stat-label">Disponibilidade</div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="features-section">
            <h2 class="section-title">Porque Escolher Nossa Plataforma</h2>
            <div class="features-grid">
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">🚀</div>
                    <h3 class="feature-title">Performance Excepcional</h3>
                    <p class="feature-description">Sistema otimizado para oferecer a melhor experiência com velocidade e estabilidade incomparáveis.</p>
                    <a href="#" class="feature-link">Saiba mais →</a>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Segurança de Ponta</h3>
                    <p class="feature-description">Proteção avançada para seus dados com criptografia e protocolos de segurança enterprise.</p>
                    <a href="#" class="feature-link">Saiba mais →</a>
                </div>
                <div class="feature-card animate-fade-in">
                    <div class="feature-icon">💡</div>
                    <h3 class="feature-title">Inovação Constante</h3>
                    <p class="feature-description">Estamos sempre evoluindo com novas funcionalidades baseadas no feedback dos usuários.</p>
                    <a href="#" class="feature-link">Saiba mais →</a>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <?php if ($usuario_logado): ?>
        <section class="quick-actions-section">
            <h2 class="section-title">Acesso Rápido</h2>
            <div class="actions-grid">
                <a href="dashboard.php" class="action-item">
                    <span class="action-icon">📊</span>
                    <div class="action-text">Dashboard</div>
                </a>
                <a href="perfil.php" class="action-item">
                    <span class="action-icon">👤</span>
                    <div class="action-text">Meu Perfil</div>
                </a>
                <a href="configuracoes.php" class="action-item">
                    <span class="action-icon">⚙️</span>
                    <div class="action-text">Configurações</div>
                </a>
                <a href="suporte.php" class="action-item">
                    <span class="action-icon">💬</span>
                    <div class="action-text">Suporte</div>
                </a>
                <a href="noticias.php" class="action-item">
                    <span class="action-icon">📰</span>
                    <div class="action-text">Notícias</div>
                </a>
                <a href="logout.php" class="action-item">
                    <span class="action-icon">🚪</span>
                    <div class="action-text">Sair</div>
                </a>
            </div>
        </section>
        <?php endif; ?>

        <!-- News -->
        <section class="news-section">
            <h2 class="section-title">Últimas Notícias</h2>
            <div class="news-grid">
                <?php foreach ($noticias_recentes as $noticia): ?>
                <div class="news-card animate-fade-in">
                    <div class="news-header">
                        <div class="news-category"><?php echo $noticia['categoria']; ?></div>
                        <h3 class="news-title"><?php echo $noticia['titulo']; ?></h3>
                        <div class="news-date"><?php echo date('d/m/Y', strtotime($noticia['data'])); ?></div>
                    </div>
                    <div class="news-content">
                        <p class="news-summary"><?php echo $noticia['resumo']; ?></p>
                        <a href="noticias.php" class="news-link">Ler mais →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="footer-links">
                <a href="sobre.php" class="footer-link">Sobre Nós</a>
                <a href="contato.php" class="footer-link">Contato</a>
                <a href="suporte.php" class="footer-link">Suporte</a>
                <a href="termos.php" class="footer-link">Termos de Uso</a>
                <a href="privacidade.php" class="footer-link">Privacidade</a>
            </div>
            <div class="copyright">
                © 2024 Nossa Plataforma. Todos os direitos reservados.
            </div>
        </footer>
    </div>

    <script>
        // Animações de scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observar elementos para animação
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // Efeito de digitação no título
            const title = document.querySelector('.header-content h1');
            const originalTitle = title.textContent;
            title.textContent = '';
            
            let titleIndex = 0;
            function typeTitle() {
                if (titleIndex < originalTitle.length) {
                    title.textContent += originalTitle.charAt(titleIndex);
                    titleIndex++;
                    setTimeout(typeTitle, 100);
                }
            }
            
            // Iniciar efeito após carregamento
            setTimeout(typeTitle, 500);
        });

        // Contador animado para estatísticas
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 40);
        }

        // Iniciar contadores quando visíveis
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const numberElement = entry.target.querySelector('.stat-number');
                    const target = parseInt(numberElement.textContent.replace(/,/g, ''));
                    if (!isNaN(target)) {
                        animateCounter(numberElement, target);
                    }
                    statObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-card').forEach(card => {
            statObserver.observe(card);
        });
    </script>
</body>
</html>