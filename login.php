<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isLoggedIn()) {
    // Redirecionar baseado no tipo de usuário
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: admin/admin.php');
    } else {
        header('Location: home.php');
    }
    exit;
}

$error = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (!isValidEmail($email)) {
        $error = 'Por favor, insira um email válido.';
    } else {
        try {
            // Buscar usuário
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['senha'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['tipo'];

                // Redirecionar baseado no tipo de usuário
                if ($user['tipo'] === 'admin') {
                    header('Location: admin/admin.php');
                } else {
                    header('Location: home.php');
                }
                exit;
            } else {
                $error = 'Email ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $error = 'Erro ao processar login. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Stars Models</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<style>
    .hero-bg {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://cdn.pixabay.com/photo/2016/11/19/20/17/catwalk-1840941_1280.jpg');
        background-size: cover;
        background-position: center;
    }
    .gradient-text {
        background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .star-glow {
        filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.6)) drop-shadow(0 0 12px rgba(236, 72, 153, 0.4));
    }
</style>
<body class="bg-black font-sans antialiased text-white">
    <!-- Navegação Simplificada (Igual ao primeiro código) -->
    <nav class="bg-black shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <!-- ESTRELA DO LADO ESQUERDO DO TEXTO -->
                        <div class="flex items-center space-x-3">
                            <!-- Estrela brilhante -->
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
                            <!-- Texto -->
                            <span class="text-2xl font-bold gradient-text">STARS MODELS</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="home_public.html" class="text-purple-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        Início
                    </a>
                    <a href="cadastro.php" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                        Cadastre-se
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="min-h-screen bg-gradient-to-br from-black via-purple-900 to-pink-900 flex items-center justify-center py-12">
        <div class="max-w-md w-full mx-4">
            <!-- Card de Login -->
            <div class="bg-gradient-to-br from-gray-900 to-purple-900/50 rounded-2xl p-8 border border-purple-500/30 shadow-2xl">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-feather="star" class="w-8 h-8 text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white">Fazer Login</h2>
                    <p class="text-purple-300 mt-2">Acesse sua conta Stars Models</p>
                </div>

                <?php if ($error) { ?>
                <div class="bg-red-500/20 border border-red-500/50 text-red-300 p-4 rounded-lg mb-6">
                    <div class="flex items-center space-x-2">
                        <i data-feather="alert-circle" class="w-5 h-5"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                </div>
                <?php } ?>

                <!-- Formulário -->
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-purple-300 mb-2">Email</label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            placeholder="seu@email.com"
                        >
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-purple-300 mb-2">Senha</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            required
                            class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            placeholder="Sua senha"
                        >
                        <button type="button" id="togglePassword" class="absolute right-3 top-10 transform -translate-y-1/2 text-purple-400 hover:text-purple-300 transition duration-300">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center space-x-2 text-purple-300">
                            <input type="checkbox" class="rounded bg-black/50 border-purple-500/30 text-purple-600 focus:ring-purple-500">
                            <span>Lembrar-me</span>
                        </label>
                        <a href="esqueceusenha.html" class="text-purple-400 hover:text-purple-300 transition duration-300">
                            Esqueceu a senha?
                        </a>
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-4 rounded-lg transition duration-300 font-bold shadow-lg hover:shadow-purple-500/25 flex items-center justify-center space-x-2"
                    >
                        <i data-feather="log-in" class="w-5 h-5"></i>
                        <span>Entrar na Conta</span>
                    </button>
                </form>

                <!-- Divisor -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-purple-500/30"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-900 text-purple-400">Novo por aqui?</span>
                    </div>
                </div>

                <!-- Link para Cadastro -->
                <div class="text-center">
                    <a href="cadastro.php" class="inline-block w-full bg-transparent hover:bg-white/5 text-white py-3 rounded-lg transition duration-300 font-medium border-2 border-purple-500/30 hover:border-purple-500/50">
                        Criar Nova Conta
                    </a>
                </div>

                <!-- Tipos de Conta -->
                <div class="mt-8 pt-6 border-t border-purple-500/30">
                    <h3 class="text-sm font-medium text-purple-300 mb-3 text-center">Tipos de Conta</h3>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="text-center p-2 bg-purple-600/20 rounded border border-purple-500/30">
                            <i data-feather="user" class="w-4 h-4 mx-auto mb-1 text-purple-300"></i>
                            <span class="text-purple-300">Modelo</span>
                        </div>
                        <div class="text-center p-2 bg-purple-600/20 rounded border border-purple-500/30">
                            <i data-feather="briefcase" class="w-4 h-4 mx-auto mb-1 text-purple-300"></i>
                            <span class="text-purple-300">Cliente</span>
                        </div>
                        <div class="text-center p-2 bg-purple-600/20 rounded border border-purple-500/30">
                            <i data-feather="shield" class="w-4 h-4 mx-auto mb-1 text-purple-300"></i>
                            <span class="text-purple-300">Admin</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Adicionais -->
            <div class="text-center mt-6">
                <a href="home.php" class="text-purple-400 hover:text-purple-300 transition duration-300 text-sm flex items-center justify-center space-x-2">
                    <i data-feather="arrow-left" class="w-4 h-4"></i>
                    <span>Voltar para o site</span>
                </a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Mostrar/ocultar senha
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.setAttribute('data-feather', 'eye-off');
                } else {
                    icon.setAttribute('data-feather', 'eye');
                }
                feather.replace();
            });
            
            feather.replace();
        });
    </script>
</body>
</html>