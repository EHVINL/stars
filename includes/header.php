<?php
// Inicia a sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclui o config se precisar
if (!isset($pdo)) {
    require_once 'includes/config.php';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stars Models Agency - Conectando Talentos e Oportunidades</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }
        
        .hero-bg {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://cdn.pixabay.com/photo/2016/11/19/20/17/catwalk-1840941_1280.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .model-card {
            transition: all 0.3s ease;
        }
        
        .model-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(168, 85, 247, 0.04);
        }
        
        .client-logo {
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }
        
        .client-logo:hover {
            filter: grayscale(0%);
            transform: scale(1.05);
        }
        
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-link.active::after {
            width: 100%;
        }
        
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen">
    <!-- Loading Spinner -->
    <div id="loading" class="fixed inset-0 bg-black z-50 flex items-center justify-center transition-opacity duration-300">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-purple-400 font-medium">Stars Models</p>
        </div>
    </div>

    <!-- Navegação -->
    <nav class="bg-black/90 backdrop-blur-md shadow-xl sticky top-0 z-40 border-b border-purple-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                            <i data-feather="star" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="text-2xl font-bold gradient-text">STARS MODELS</span>
                    </div>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="home.php" class="nav-link text-white font-medium text-sm transition duration-300 hover:text-purple-300 <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active text-purple-400' : ''; ?>">
                        Home
                    </a>
                    <a href="sobrenos.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'sobrenos.php' ? 'active text-white' : ''; ?>">
                        Sobre Nós
                    </a>
                    <a href="casting.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'casting.php' ? 'active text-white' : ''; ?>">
                        Casting
                    </a>
                    <a href="jobs.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active text-white' : ''; ?>">
                        Jobs
                    </a>
                    <a href="noticias.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'noticias.php' ? 'active text-white' : ''; ?>">
                        Notícias
                    </a>
                    <a href="contato.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo basename($_SERVER['PHP_SELF']) == 'contato.php' ? 'active text-white' : ''; ?>">
                        Contato
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_type'] === 'admin'): ?>
                            <a href="admin/admin.php" class="nav-link text-purple-300 font-medium text-sm transition duration-300 hover:text-white <?php echo strpos($_SERVER['PHP_SELF'], 'admin/') !== false ? 'active text-white' : ''; ?>">
                                Painel Admin
                            </a>
                        <?php elseif($_SESSION['user_type'] === 'modelo'): ?>
                        <?php endif; ?>
                        
                        <div class="flex items-center space-x-4 pl-4 border-l border-purple-800">
                            <span class="text-sm text-purple-300">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="logout.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-300 text-sm font-medium flex items-center space-x-2">
                                <i data-feather="log-out" class="w-4 h-4"></i>
                                <span>Sair</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-4">
                            <a href="login.php" class="text-purple-300 hover:text-white font-medium text-sm transition duration-300">
                                Login
                            </a>
                            <a href="cadastro.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-2 rounded-lg transition duration-300 text-sm font-medium shadow-lg hover:shadow-purple-500/25">
                                Cadastrar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Botão Mobile Menu -->
                <div class="md:hidden flex items-center space-x-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="text-sm text-purple-300 hidden sm:inline"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                    <?php endif; ?>
                    <button id="mobileMenuButton" class="p-2 rounded-lg text-purple-300 hover:text-white hover:bg-purple-900/50 transition duration-300">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu md:hidden absolute top-20 left-0 w-full bg-black/95 backdrop-blur-lg border-b border-purple-900/30 shadow-xl">
            <div class="px-4 py-6 space-y-4">
                <a href="home.php" class="block py-3 px-4 text-white font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'bg-purple-900/50 text-purple-300' : ''; ?>">
                    Home
                </a>
                <a href="sobrenos.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'sobrenos.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                    Sobre Nós
                </a>
                <a href="casting.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'casting.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                    Casting
                </a>
                <a href="jobs.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                    Jobs
                </a>
                <a href="noticias.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'noticias.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                    Notícias
                </a>
                <a href="contato.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'contato.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                    Contato
                </a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="border-t border-purple-800 pt-4 mt-4">
                        <?php if($_SESSION['user_type'] === 'admin'): ?>
                            <a href="admin/admin.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo strpos($_SERVER['PHP_SELF'], 'admin/') !== false ? 'bg-purple-900/50 text-white' : ''; ?>">
                                Painel Admin
                            </a>
                        <?php elseif($_SESSION['user_type'] === 'modelo'): ?>
                            <a href="modelo.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'modelo.php' ? 'bg-purple-900/50 text-white' : ''; ?>">
                                Meu Perfil
                            </a>
                        <?php endif; ?>
                        
                        <div class="px-4 py-3 bg-purple-900/20 rounded-lg mt-2">
                            <p class="text-sm text-purple-300">Logado como</p>
                            <p class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                            <p class="text-xs text-purple-400 mt-1"><?php echo ucfirst($_SESSION['user_type']); ?></p>
                        </div>
                        
                        <a href="logout.php" class="block py-3 px-4 text-red-400 font-medium rounded-lg hover:bg-red-900/20 transition duration-300 mt-2 border border-red-800/50 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <i data-feather="log-out" class="w-4 h-4"></i>
                                <span>Sair da Conta</span>
                            </div>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="border-t border-purple-800 pt-4 mt-4 space-y-3">
                        <a href="login.php" class="block py-3 px-4 text-purple-300 font-medium rounded-lg hover:bg-purple-900/30 transition duration-300 text-center border border-purple-800">
                            Fazer Login
                        </a>
                        <a href="cadastro.php" class="block py-3 px-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-pink-700 transition duration-300 text-center shadow-lg">
                            Criar Conta
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if(isset($_SESSION['flash_message'])): ?>
        <div class="fixed top-24 right-4 z-50 max-w-sm">
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg border border-green-400">
                <div class="flex items-center space-x-3">
                    <i data-feather="check-circle" class="w-5 h-5 text-white"></i>
                    <span class="font-medium"><?php echo $_SESSION['flash_message']; ?></span>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <main>