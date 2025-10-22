<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif (!isValidEmail($email)) {
        $error = "Por favor, insira um email válido.";
    } else {
        try {
            // Buscar usuário
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['senha'])) {
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
                $error = "Email ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $error = "Erro ao processar login. Tente novamente.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

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

            <?php if($error): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 p-4 rounded-lg mb-6">
                <div class="flex items-center space-x-2">
                    <i data-feather="alert-circle" class="w-5 h-5"></i>
                    <span><?php echo $error; ?></span>
                </div>
            </div>
            <?php endif; ?>

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

                <div>
                    <label for="password" class="block text-sm font-medium text-purple-300 mb-2">Senha</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Sua senha"
                    >
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center space-x-2 text-purple-300">
                        <input type="checkbox" class="rounded bg-black/50 border-purple-500/30 text-purple-600 focus:ring-purple-500">
                        <span>Lembrar-me</span>
                    </label>
                    <a href="#" class="text-purple-400 hover:text-purple-300 transition duration-300">
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
        // Mostrar/ocultar senha
        const passwordInput = document.getElementById('password');
        const togglePassword = document.createElement('button');
        togglePassword.type = 'button';
        togglePassword.innerHTML = '<i data-feather="eye" class="w-4 h-4"></i>';
        togglePassword.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-400 hover:text-purple-300 transition duration-300';
        
        const passwordContainer = passwordInput.parentElement;
        passwordContainer.classList.add('relative');
        passwordContainer.appendChild(togglePassword);
        
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

<?php include 'includes/footer.php'; ?>