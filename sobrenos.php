<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - Stars Models</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .star-glow {
            filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.6)) drop-shadow(0 0 12px rgba(236, 72, 153, 0.4));
        }
        .gradient-bg {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        }
        .timeline::before {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        }
        .timeline-dot {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen font-sans antialiased">
    <!-- Topbar como usuário LOGADO -->
    <nav class="bg-black shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <!-- Logo com estrela -->
                        <div class="flex items-center space-x-3">
                            <div class="star-glow">
                                <svg width="28" height="28" viewBox="0 0 24 24">
                                    <defs>
                                        <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#8b5cf6" />
                                            <stop offset="100%" stop-color="#ec4899" />
                                        </linearGradient>
                                        <filter id="glow">
                                            <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                                            <feMerge>
                                                <feMergeNode in="coloredBlur"/>
                                                <feMergeNode in="SourceGraphic"/>
                                            </feMerge>
                                        </filter>
                                    </defs>
                                    <path fill="url(#starGradient)" filter="url(#glow)" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold gradient-text">STARS MODELS</span>
                        </div>
                    </div>
                    <!-- Menu para usuário logado -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="home.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Home</a>
                        <a href="sobrenos.php" class="border-purple-600 text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Sobre Nós</a>
                        <a href="casting.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Casting</a>
                        <a href="jobs.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Jobs</a>
                        <a href="noticias.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Notícias</a>
                        <a href="contato.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Contato</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Perfil do usuário logado -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            U
                        </div>
                        <span class="text-purple-300 text-sm">Usuário</span>
                    </div>
                    <a href="logout.php" class="text-purple-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Hero Section Inspirada na Versão Antiga -->
        <section class="gradient-bg rounded-2xl p-12 text-center mb-16" data-aos="fade-down">
            <h1 class="text-5xl font-bold mb-6 text-white">Sobre a Stars Models</h1>
            <p class="text-xl text-purple-100 max-w-3xl mx-auto">
                Conheça a história, missão e valores que nos movem todos os dias para revelar os maiores talentos do Brasil
            </p>
        </section>

        <!-- Carousel -->
        <div class="relative overflow-hidden rounded-xl shadow-2xl mb-16 h-96 bg-black" data-aos="fade-up">
            <div class="carousel-slides flex h-full transition-transform duration-500">
                <div class="min-w-full h-full bg-cover bg-center" style="background-image: url('https://cdn.pixabay.com/photo/2023/07/07/03/44/woman-8111581_960_720.jpg')"></div>
                <div class="min-w-full h-full bg-cover bg-center" style="background-image: url('https://cdn.pixabay.com/photo/2022/09/20/01/54/models-7466805_960_720.jpg')"></div>
                <div class="min-w-full h-full bg-cover bg-center" style="background-image: url('https://cdn.pixabay.com/photo/2016/02/19/11/49/model-1209985_640.jpg')"></div>
            </div>
            <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition" onclick="prevSlide()">
                <i data-feather="chevron-left"></i>
            </button>
            <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition" onclick="nextSlide()">
                <i data-feather="chevron-right"></i>
            </button>
        </div>

        <!-- História e Missão -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-8 rounded-xl border border-purple-500/20" data-aos="fade-right">
                <h2 class="text-3xl font-bold mb-6 text-white gradient-text">Nossa História</h2>
                <p class="text-purple-200 leading-relaxed mb-4">
                    Fundada em 2010, a Stars Models Agency nasceu com o propósito de transformar sonhos em carreiras e talentos em referência. Desde o início, acreditamos que a beleza vai além da aparência, ela está na atitude, na confiança e na autenticidade de cada modelo que representamos.
                </p>
                <p class="text-purple-200 leading-relaxed mb-4">
                    Ao longo dos anos, crescemos lado a lado com nossos talentos, construindo uma trajetória marcada por profissionalismo, transparência e resultados reais. Participamos de campanhas publicitárias, desfiles e projetos nacionais e internacionais, consolidando nossa presença no mercado da moda e da publicidade.
                </p>
                <p class="text-purple-200 leading-relaxed">
                    Hoje, com mais de uma década de experiência, continuamos firmes em nosso compromisso de descobrir novas promessas, impulsionar carreiras e conectar pessoas ao mundo da moda de forma ética e inspiradora.
                </p>
            </div>
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-8 rounded-xl border border-purple-500/20" data-aos="fade-left">
                <h2 class="text-3xl font-bold mb-6 text-white gradient-text">Missão e Visão</h2>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-purple-300 mb-2">🎯 Missão</h3>
                        <p class="text-purple-200">
                            Revelar, desenvolver e valorizar talentos, conectando modelos e profissionais da moda às melhores oportunidades do mercado.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-purple-300 mb-2">🔭 Visão</h3>
                        <p class="text-purple-200">
                            Ser a agência de modelos mais inovadora e respeitada do Brasil, reconhecida pela qualidade de nossos talentos e pelo impacto positivo que geramos.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-purple-300 mb-2">💫 Compromisso</h3>
                        <p class="text-purple-200">
                            Trabalhamos com ética, profissionalismo e paixão, oferecendo suporte completo para o crescimento pessoal e profissional de nossos modelos.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Números/Estatísticas -->
        <section class="gradient-bg rounded-2xl p-12 mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-center mb-12 text-white">Nossos Números</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">+500</div>
                    <div class="text-purple-200">Modelos Representados</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">+1000</div>
                    <div class="text-purple-200">Projetos Realizados</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">14</div>
                    <div class="text-purple-200">Anos de Experiência</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="400">
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">98%</div>
                    <div class="text-purple-200">Clientes Satisfeitos</div>
                </div>
            </div>
        </section>

        <!-- Valores -->
        <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-8 rounded-xl border border-purple-500/20 mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-12 text-white text-center gradient-text">Nossos Valores</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="star-glow mb-4 mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center">
                        <i data-feather="star" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Excelência</h3>
                    <p class="text-purple-200 text-sm">Buscamos a perfeição em cada detalhe e projeto que realizamos</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="star-glow mb-4 mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center">
                        <i data-feather="heart" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Paixão</h3>
                    <p class="text-purple-200 text-sm">Amamos o que fazemos e isso reflete em nosso trabalho diário</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
                    <div class="star-glow mb-4 mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center">
                        <i data-feather="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Diversidade</h3>
                    <p class="text-purple-200 text-sm">Celebramos a beleza em todas as suas formas e expressões</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="400">
                    <div class="star-glow mb-4 mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center">
                        <i data-feather="award" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Qualidade</h3>
                    <p class="text-purple-200 text-sm">Excelência em cada detalhe, do primeiro contato à entrega final</p>
                </div>
            </div>
        </div>

        <!-- Linha do Tempo -->
        <section class="mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-12 text-center gradient-text">Nossa Trajetória</h2>
            <div class="timeline relative max-w-4xl mx-auto">
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full timeline"></div>
                
                <div class="timeline-item mb-12 relative" data-aos="fade-right">
                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20 ml-0 md:ml-12 w-full md:w-5/12">
                        <div class="text-purple-300 font-bold text-lg mb-2">2010</div>
                        <p class="text-purple-200">Fundação da Stars Models Agency com foco em descobrir novos talentos</p>
                    </div>
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full border-4 border-black top-6"></div>
                </div>

                <div class="timeline-item mb-12 relative" data-aos="fade-left">
                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20 ml-auto mr-0 md:mr-12 w-full md:w-5/12">
                        <div class="text-purple-300 font-bold text-lg mb-2">2014</div>
                        <p class="text-purple-200">Primeira expansão e participação em eventos internacionais de moda</p>
                    </div>
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full border-4 border-black top-6"></div>
                </div>

                <div class="timeline-item mb-12 relative" data-aos="fade-right">
                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20 ml-0 md:ml-12 w-full md:w-5/12">
                        <div class="text-purple-300 font-bold text-lg mb-2">2018</div>
                        <p class="text-purple-200">Lançamento da plataforma digital e expansão para todo território nacional</p>
                    </div>
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full border-4 border-black top-6"></div>
                </div>

                <div class="timeline-item mb-12 relative" data-aos="fade-left">
                    <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20 ml-auto mr-0 md:mr-12 w-full md:w-5/12">
                        <div class="text-purple-300 font-bold text-lg mb-2">2024</div>
                        <p class="text-purple-200">Consolidação como referência no mercado e preparação para expansão internacional</p>
                    </div>
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-4 h-4 rounded-full border-4 border-black top-6"></div>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="gradient-bg rounded-2xl p-12 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-6 text-white">Pronto para Fazer Parte da Nossa História?</h2>
            <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                Junte-se à família Stars Models e descubra um mundo de oportunidades para sua carreira
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="casting.php" class="bg-white text-purple-700 hover:bg-purple-100 px-8 py-4 rounded-xl transition duration-300 text-lg font-bold inline-flex items-center space-x-3">
                    <i data-feather="users" class="w-5 h-5"></i>
                    <span>Ver Casting</span>
                </a>
                <a href="contato.php" class="bg-transparent hover:bg-white/10 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold border-2 border-white inline-flex items-center space-x-3">
                    <i data-feather="mail" class="w-5 h-5"></i>
                    <span>Falar Conosco</span>
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-purple-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase">Sobre</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="sobrenos.php" class="hover:text-purple-300">Nossa História</a></li>
                        <li><a href="sobrenos.php" class="hover:text-purple-300">Missões</a></li>
                        <li><a href="noticias.php" class="hover:text-purple-300">Notícias</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase">Serviços</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="casting.php" class="hover:text-purple-300">Casting</a></li>
                        <li><a href="jobs.php" class="hover:text-purple-300">Jobs</a></li>
                        <li><a href="contato.php" class="hover:text-purple-300">Contato</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase">Suporte</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="hover:text-purple-300">FAQ</a></li>
                        <li><a href="#" class="hover:text-purple-300">Ajuda</a></li>
                        <li><a href="#" class="hover:text-purple-300">Termos</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase">Contato</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="mailto:contato@starsmodels.com" class="hover:text-purple-300">starsmodels@gmail.com</a></li>
                        <li><a href="tel:+5511999999999" class="hover:text-purple-300">61 4184847</a></li>
                        <li>Brasília, Brasil</li>
                        <li>Asa Norte</li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-purple-800 pt-8 text-center text-purple-400 text-sm">
                © 2025 Stars Models Agency. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        feather.replace();
        
        let currentSlide = 0;
        const slides = document.querySelector('.carousel-slides');
        const totalSlides = 3;

        function updateSlide() {
            slides.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlide();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlide();
        }

        setInterval(nextSlide, 5000);
    </script>
</body>
</html>