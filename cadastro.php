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
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (!isValidEmail($email)) {
        $error = 'Por favor, insira um email válido.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } elseif (!isStrongPassword($password)) {
        $error = 'A senha deve ter pelo menos 6 caracteres, incluindo uma letra maiúscula, um número e um caractere especial.';
    } else {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'Este email já está cadastrado.';
            } else {
                // Criar usuário
                $hashed_password = generateHash($password);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$nome, $email, $hashed_password, $tipo, $telefone]);

                $user_id = $pdo->lastInsertId();

                // Se for modelo, criar registro na tabela modelos
                if ($tipo === 'modelo') {
                    $stmt = $pdo->prepare("INSERT INTO modelos (usuario_id, status) VALUES (?, 'pendente')");
                    $stmt->execute([$user_id]);
                }

                $success = 'Cadastro realizado com sucesso! Você já pode fazer login.';

                // Limpar formulário
                $_POST = [];
            }
        } catch (PDOException $e) {
            $error = 'Erro ao processar cadastro. Tente novamente.';
        }
    }
}

// Função para validar senha forte
function isStrongPassword($password) {
    return strlen($password) >= 6 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[0-9]/', $password) &&
           preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro - Stars Models</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
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
    .password-strength {
        transition: all 0.3s ease;
    }
    .requirement {
        transition: all 0.3s ease;
    }
    .requirement.met {
        color: #10b981;
    }
    .requirement.unmet {
        color: #6b7280;
    }
</style>
<body class="bg-black font-sans antialiased text-white">
    <!-- Navegação Igual à Segunda Página -->
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
                    <a href="login.php" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                        Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

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

                <?php if ($error) { ?>
                <div class="bg-red-500/20 border border-red-500/50 text-red-300 p-4 rounded-lg mb-6">
                    <div class="flex items-center space-x-2">
                        <i data-feather="alert-circle" class="w-5 h-5"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                </div>
                <?php } ?>

                <?php if ($success) { ?>
                <div class="bg-green-500/20 border border-green-500/50 text-green-300 p-4 rounded-lg mb-6">
                    <div class="flex items-center space-x-2">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                </div>
                <?php } ?>

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
                            placeholder="(99) 99999-9999"
                            oninput="formatPhoneNumber(this)"
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

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-purple-300 mb-2">Senha *</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            required
                            class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 password-input"
                            placeholder="Digite uma senha forte"
                            oninput="validatePassword(this.value)"
                        >
                        <button type="button" class="absolute right-3 top-10 transform -translate-y-1/2 text-purple-400 hover:text-purple-300 transition duration-300 toggle-password">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Indicador de Força da Senha -->
                    <div id="passwordStrength" class="hidden">
                        <div class="text-sm text-purple-300 mb-2">Força da senha:</div>
                        <div class="w-full bg-gray-700 rounded-full h-2 mb-3">
                            <div id="passwordStrengthBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        
                        <!-- Requisitos da Senha -->
                        <div class="space-y-1 text-xs">
                            <div id="reqLength" class="requirement unmet flex items-center space-x-2">
                                <i data-feather="x" class="w-3 h-3"></i>
                                <span>Mínimo 6 caracteres</span>
                            </div>
                            <div id="reqUppercase" class="requirement unmet flex items-center space-x-2">
                                <i data-feather="x" class="w-3 h-3"></i>
                                <span>Pelo menos 1 letra maiúscula</span>
                            </div>
                            <div id="reqNumber" class="requirement unmet flex items-center space-x-2">
                                <i data-feather="x" class="w-3 h-3"></i>
                                <span>Pelo menos 1 número</span>
                            </div>
                            <div id="reqSpecial" class="requirement unmet flex items-center space-x-2">
                                <i data-feather="x" class="w-3 h-3"></i>
                                <span>Pelo menos 1 caractere especial</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="confirm_password" class="block text-sm font-medium text-purple-300 mb-2">Confirmar Senha *</label>
                        <input 
                            type="password" 
                            id="confirm_password"
                            name="confirm_password" 
                            required
                            class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 password-input"
                            placeholder="Digite a senha novamente"
                            oninput="validatePasswordMatch()"
                        >
                        <button type="button" class="absolute right-3 top-10 transform -translate-y-1/2 text-purple-400 hover:text-purple-300 transition duration-300 toggle-password">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Indicador de Confirmação de Senha -->
                    <div id="passwordMatch" class="hidden">
                        <div id="passwordMatchText" class="text-xs"></div>
                    </div>

                    <div class="flex items-center space-x-2 text-sm text-purple-300">
                        <input 
                            type="checkbox" 
                            id="terms"
                            required
                            class="rounded bg-black/50 border-purple-500/30 text-purple-600 focus:ring-purple-500"
                        >
                        <span>
                            Concordo com os 
                            <a href="termos.php" class="text-purple-400 hover:text-purple-300 underline">Termos de Uso</a> 
                            e 
                            <a href="politica-privacidade.php" class="text-purple-400 hover:text-purple-300 underline">Política de Privacidade</a>
                        </span>
                    </div>

                    <button 
                        type="submit"
                        id="submitButton"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-4 rounded-lg transition duration-300 font-bold shadow-lg hover:shadow-purple-500/25 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
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
            // Inicializar AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });

            // Mostrar/ocultar senha
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.password-input');
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
        });

        function formatPhoneNumber(input) {
            // Remove tudo que não é número
            let numbers = input.value.replace(/\D/g, '');
            
            // Aplica a máscara (99) 99999-9999
            if (numbers.length <= 2) {
                input.value = numbers;
            } else if (numbers.length <= 6) {
                input.value = '(' + numbers.substring(0, 2) + ') ' + numbers.substring(2);
            } else if (numbers.length <= 10) {
                input.value = '(' + numbers.substring(0, 2) + ') ' + numbers.substring(2, 6) + '-' + numbers.substring(6);
            } else {
                input.value = '(' + numbers.substring(0, 2) + ') ' + numbers.substring(2, 7) + '-' + numbers.substring(7, 11);
            }
        }

        function validatePassword(password) {
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const submitButton = document.getElementById('submitButton');
            const termsCheckbox = document.getElementById('terms');
            
            // Mostrar o indicador de força quando começar a digitar
            if (password.length > 0) {
                strengthDiv.classList.remove('hidden');
            } else {
                strengthDiv.classList.add('hidden');
                updateSubmitButton();
                return;
            }

            // Verificar requisitos
            const hasMinLength = password.length >= 6;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

            // Atualizar ícones dos requisitos
            updateRequirement('reqLength', hasMinLength);
            updateRequirement('reqUppercase', hasUppercase);
            updateRequirement('reqNumber', hasNumber);
            updateRequirement('reqSpecial', hasSpecial);

            // Calcular força da senha
            let strength = 0;
            if (hasMinLength) strength += 25;
            if (hasUppercase) strength += 25;
            if (hasNumber) strength += 25;
            if (hasSpecial) strength += 25;

            // Atualizar barra de força
            strengthBar.style.width = strength + '%';
            
            // Atualizar cor da barra
            if (strength <= 25) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            } else if (strength <= 50) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
            } else if (strength <= 75) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
            } else {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            }

            updateSubmitButton();
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            const matchText = document.getElementById('passwordMatchText');
            const submitButton = document.getElementById('submitButton');

            if (confirmPassword.length > 0) {
                matchDiv.classList.remove('hidden');
                
                if (password === confirmPassword && password.length > 0) {
                    matchText.innerHTML = '<div class="text-green-400 flex items-center space-x-2"><i data-feather="check" class="w-3 h-3"></i><span>Senhas coincidem</span></div>';
                } else if (password.length > 0) {
                    matchText.innerHTML = '<div class="text-red-400 flex items-center space-x-2"><i data-feather="x" class="w-3 h-3"></i><span>Senhas não coincidem</span></div>';
                } else {
                    matchText.innerHTML = '<div class="text-yellow-400 flex items-center space-x-2"><i data-feather="alert-circle" class="w-3 h-3"></i><span>Digite a senha primeiro</span></div>';
                }
                feather.replace();
            } else {
                matchDiv.classList.add('hidden');
            }

            updateSubmitButton();
        }

        function updateRequirement(elementId, isMet) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('i');
            
            if (isMet) {
                element.classList.remove('unmet');
                element.classList.add('met');
                icon.setAttribute('data-feather', 'check');
            } else {
                element.classList.remove('met');
                element.classList.add('unmet');
                icon.setAttribute('data-feather', 'x');
            }
            feather.replace();
        }

        function updateSubmitButton() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const termsCheckbox = document.getElementById('terms');
            const submitButton = document.getElementById('submitButton');

            // Verificar se a senha atende todos os requisitos
            const hasMinLength = password.length >= 6;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            const isStrongPassword = hasMinLength && hasUppercase && hasNumber && hasSpecial;
            
            const passwordsMatch = password === confirmPassword && password.length > 0;
            const termsAccepted = termsCheckbox.checked;

            // Habilitar/desabilitar botão baseado nas condições
            if (isStrongPassword && passwordsMatch && termsAccepted) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Adicionar event listeners para atualizar o botão
        document.getElementById('terms').addEventListener('change', updateSubmitButton);
        document.getElementById('password').addEventListener('input', updateSubmitButton);
        document.getElementById('confirm_password').addEventListener('input', updateSubmitButton);

        // Validar ao carregar a página (caso tenha valores preenchidos)
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.value) {
                validatePassword(passwordInput.value);
            }
            validatePasswordMatch();
            updateSubmitButton();
        });
    </script>
</body>
</html>