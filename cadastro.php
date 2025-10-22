<?php

require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error = '';
$success = '';

// Processar cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tipo = sanitize($_POST['tipo']);
    $telefone = sanitize($_POST['telefone'] ?? '');
    
    // Validações
    if (empty($nome) || empty($email) || empty($password) || empty($confirm_password) || empty($tipo)) {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!isValidEmail($email)) {
        $error = "Por favor, insira um email válido.";
    } elseif ($password !== $confirm_password) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Este email já está cadastrado.";
            } else {
                // Criar usuário
                $hashed_password = generateHash($password);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $hashed_password, $tipo, $telefone]);
                
                $user_id = $pdo->lastInsertId();
                
                // Se for modelo, criar registro na tabela modelos
                if ($tipo === 'modelo') {
                    $stmt = $pdo->prepare("INSERT INTO modelos (usuario_id, status) VALUES (?, 'pendente')");
                    $stmt->execute([$user_id]);
                }
                
                $success = "Cadastro realizado com sucesso! Você já pode fazer login.";
                
                // Limpar formulário
                $_POST = array();
            }
        } catch (PDOException $e) {
            $error = "Erro ao processar cadastro. Tente novamente.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<section class="min-h-screen bg-gradient-to-br from-black via-purple-900 to-pink-900 flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-4">
        <!-- Card de Cadastro -->
        <div class="bg-gradient-to-br from-gray-900 to-purple-900/50 rounded-2xl p-8 border border-purple-500/30 shadow-2xl">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-feather="user-plus" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">Criar Conta</h2>
                <p class="text-purple-300 mt-2">Junte-se à família Stars Models</p>
            </div>

            <?php if($error): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 p-4 rounded-lg mb-6">
                <div class="flex items-center space-x-2">
                    <i data-feather="alert-circle" class="w-5 h-5"></i>
                    <span><?php echo $error; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if($success): ?>
            <div class="bg-green-500/20 border border-green-500/50 text-green-300 p-4 rounded-lg mb-6">
                <div class="flex items-center space-x-2">
                    <i data-feather="check-circle" class="w-5 h-5"></i>
                    <span><?php echo $success; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="nome" class="block text-sm font-medium text-purple-300 mb-2">Nome Completo *</label>
                    <input 
                        type="text" 
                        id="nome"
                        name="nome" 
                        required
                        value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>"
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Seu nome completo"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-purple-300 mb-2">Email *</label>
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
                    <label for="telefone" class="block text-sm font-medium text-purple-300 mb-2">Telefone</label>
                    <input 
                        type="tel" 
                        id="telefone"
                        name="telefone" 
                        value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>"
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="(61) 99999-9999"
                    >
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-medium text-purple-300 mb-2">Tipo de Conta *</label>
                    <select 
                        id="tipo"
                        name="tipo" 
                        required
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                    >
                        <option value="">Selecione o tipo de conta</option>
                        <option value="modelo" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] === 'modelo') ? 'selected' : ''; ?>>Sou Modelo/Talento</option>
                        <option value="cliente" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] === 'cliente') ? 'selected' : ''; ?>>Sou Cliente/Agência</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-purple-300 mb-2">Senha *</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Mínimo 6 caracteres"
                        minlength="6"
                    >
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-purple-300 mb-2">Confirmar Senha *</label>
                    <input 
                        type="password" 
                        id="confirm_password"
                        name="confirm_password" 
                        required
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                        placeholder="Digite a senha novamente"
                        minlength="6"
                    >
                </div>

                <div class="flex items-center space-x-2 text-sm text-purple-300">
                    <input 
                        type="checkbox" 
                        required
                        class="rounded bg-black/50 border-purple-500/30 text-purple-600 focus:ring-purple-500"
                    >
                    <span>
                        Concordo com os 
                        <a href="termos.php" class="text-purple-400 hover:text-purple-300 underline">Termos de Uso</a> 
                        e 
                        <a href="termos.php#privacidade" class="text-purple-400 hover:text-purple-300 underline">Política de Privacidade</a>
                    </span>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-4 rounded-lg transition duration-300 font-bold shadow-lg hover:shadow-purple-500/25 flex items-center justify-center space-x-2"
                >
                    <i data-feather="user-plus" class="w-5 h-5"></i>
                    <span>Criar Minha Conta</span>
                </button>
            </form>

            <!-- Divisor -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-purple-500/30"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-900 text-purple-400">Já tem uma conta?</span>
                </div>
            </div>

            <!-- Link para Login -->
            <div class="text-center">
                <a href="login.php" class="inline-block w-full bg-transparent hover:bg-white/5 text-white py-3 rounded-lg transition duration-300 font-medium border-2 border-purple-500/30 hover:border-purple-500/50">
                    Fazer Login
                </a>
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
        const passwordInputs = ['password', 'confirm_password'];
        
        passwordInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            const togglePassword = document.createElement('button');
            togglePassword.type = 'button';
            togglePassword.innerHTML = '<i data-feather="eye" class="w-4 h-4"></i>';
            togglePassword.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-400 hover:text-purple-300 transition duration-300';
            
            const container = input.parentElement;
            container.classList.add('relative');
            container.appendChild(togglePassword);
            
            togglePassword.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.setAttribute('data-feather', 'eye-off');
                } else {
                    icon.setAttribute('data-feather', 'eye');
                }
                feather.replace();
            });
        });
        
        feather.replace();
        
        // Validação de senha em tempo real
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePasswords() {
            if (password.value && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#ef4444';
                } else {
                    confirmPassword.style.borderColor = '#8b5cf6';
                }
            }
        }
        
        password.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    });
</script>

<?php include 'includes/footer.php'; ?>