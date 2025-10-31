<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notícias - Stars Models</title>
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
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.3), 0 10px 10px -5px rgba(139, 92, 246, 0.2);
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
                    <a href="sobrenos.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Sobre Nós</a>
                    <a href="casting.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Casting</a>
                    <a href="jobs.php" class="border-transparent text-purple-300 hover:border-purple-500 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Jobs</a>
                    <a href="noticias.php" class="border-purple-600 text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Notícias</a>
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
        <!-- Hero Section -->
        <section class="gradient-bg rounded-2xl p-12 text-center mb-16" data-aos="fade-down">
            <h1 class="text-5xl font-bold mb-6 text-white">Notícias da Agência</h1>
            <p class="text-xl text-purple-100 max-w-3xl mx-auto">
                Confira as últimas notícias, parcerias e conquistas da Stars Models no mundo da moda e entretenimento
            </p>
        </section>

        <!-- Grid de Notícias -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Notícia 1 Capa de revista -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2025/05/18/09/15/ai-generated-9606987_960_720.jpg" alt="Modelo em capa de revista" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Capa da Vogue Brasil</h3>
                <p class="text-purple-200 mb-4">Nossa modelo estrela foi capa da edição de dezembro da Vogue Brasil, marcando nossa 5ª capa consecutiva na publicação.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>15/11/2024</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Notícia 2 Parceria com a Dior -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2025/03/01/16/23/woman-9440298_960_720.jpg" alt="Parceria com marca" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Parceria com Dior</h3>
                <p class="text-purple-200 mb-4">Fechamos exclusividade com a Dior para campanhas e desfiles internacionais com 3 de nossos modelos na próxima temporada.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>10/10/2025</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Notícia 3 Prêmio -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2015/06/27/21/21/prize-823854_1280.jpg" alt="Prêmio recebido" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Prêmio de Agência do Ano!</h3>
                <p class="text-purple-200 mb-4">Pela 11ª vez consecutiva, fomos eleitos a Melhor Agência de Modelos da América Latina pelo Fashion Awards.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>30/10/2023</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Notícia 4 Desfile Paris -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2023/07/07/03/44/woman-8111580_640.jpg" alt="Desfile de moda" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Desfile em Paris</h3>
                <p class="text-purple-200 mb-4">15 de nossos modelos desfilaram na Paris Fashion Week, com destaque para a coleção inverno 2024 da Chanel.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>22/10/2023</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Notícia 5 Expansão -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2014/08/10/22/45/computer-workstations-415138_640.jpg" alt="Novo escritório" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Expansão Internacional</h3>
                <p class="text-purple-200 mb-4">Inauguramos nosso novo escritório em Milão, consolidando nossa presença no mercado europeu de moda.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>14/10/2023</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Notícia 6 Diferenciais -->
            <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20 transition duration-300 news-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mb-4 overflow-hidden rounded-lg">
                    <img src="https://cdn.pixabay.com/photo/2017/08/06/00/27/yoga-2587066_640.jpg" alt="Curiosidades" class="w-full h-48 object-cover hover:scale-105 transition duration-500">
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Diferenciais Exclusivos</h3>
                <p class="text-purple-200 mb-4">Nossa agência oferece treinamento exclusivo em desenvolvimento pessoal e preparação psicológica para modelos.</p>
                <div class="flex justify-between items-center text-sm text-purple-300">
                    <span>05/10/2023</span>
                    <a href="#" class="text-purple-400 hover:text-white transition duration-300 flex items-center space-x-1">
                        <span>Ler mais</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Parcerias -->
        <section class="mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-12 text-center gradient-text">Nossas Marcas Parceiras</h2>
            <div class="flex flex-wrap justify-center gap-8">
                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Valentino_logo.svg/1200px-Valentino_logo.svg.png" alt="Valentino" class="h-16 w-auto mx-auto">
                </div>
                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Dior_Logo.svg/2560px-Dior_Logo.svg.png" alt="Dior" class="h-16 w-auto mx-auto">
                </div>
                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20">
                    <img src="https://logosmarcas.net/wp-content/uploads/2020/05/Prada-Simbolo.jpg" alt="Prada" class="h-16 w-auto mx-auto">
                </div>
                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/76/Louis_Vuitton_logo_and_wordmark.svg" alt="Louis Vuitton" class="h-16 w-auto mx-auto">
                </div>
                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/30 p-6 rounded-xl border border-purple-500/20">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Smart_Fit_logo.svg/2560px-Smart_Fit_logo.svg.png" alt="Smart Fit" class="h-16 w-auto mx-auto">
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="gradient-bg rounded-2xl p-12 text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-6 text-white">Fique por Dentro das Novidades</h2>
            <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                Não perca nenhuma notícia da Stars Models. Siga nossas redes sociais e acompanhe tudo em primeira mão.
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
    </script>
</body>
</html>