<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
$usuario_logado = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Processar logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_logout'])) {
    // Registrar ação de logout
    $acao = $usuario_logado ? "Logout realizado por: " . $usuario_logado : "Logout realizado";
    error_log($acao);
    
    // Destruir todas as variáveis de sessão
    $_SESSION = array();
    
    // Se deseja destruir a sessão completamente, apague também o cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Finalmente, destruir a sessão
    session_destroy();
    
    // Redirecionar para página de login com mensagem
    header("Location: login.php?message=logout_success");
    exit();
}

// Se tentou acessar sem estar logado
if (!$usuario_logado) {
    header("Location: login.php?message=already_logged_out");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sair do Sistema - Confirmação de Logout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .logout-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .logout-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .logout-title {
            color: #333;
            font-size: 2.2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .logout-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .user-avatar {
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
            font-weight: bold;
        }

        .user-name {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .user-email {
            color: #666;
            font-size: 0.95rem;
        }

        .logout-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 140px;
        }

        .btn-logout {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-cancel {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e9ecef;
        }

        .btn-cancel:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-top: 25px;
            text-align: left;
        }

        .security-notice h4 {
            color: #856404;
            margin-bottom: 8px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .security-notice p {
            color: #856404;
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 0;
        }

        .logout-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }

        .feature-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-3px);
            background: #e9ecef;
        }

        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
        }

        .feature-text {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }

        .countdown {
            color: #e74c3c;
            font-weight: bold;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .logout-container {
                padding: 30px 20px;
            }
            
            .logout-title {
                font-size: 1.8rem;
            }
            
            .logout-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .logout-features {
                grid-template-columns: 1fr 1fr;
            }
        }

        .auto-redirect {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
            color: #155724;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">🚪</div>
        
        <h1 class="logout-title">Sair do Sistema</h1>
        <p class="logout-subtitle">Tem certeza que deseja encerrar sua sessão?</p>
        
        <!-- Informações do Usuário -->
        <div class="user-info">
            <div class="user-avatar">
                <?php 
                // Pegar primeira letra do nome para avatar
                echo strtoupper(substr($usuario_logado, 0, 1)); 
                ?>
            </div>
            <div class="user-name"><?php echo htmlspecialchars($usuario_logado); ?></div>
            <div class="user-email">Usuário logado</div>
        </div>

        <!-- Recursos ativos -->
        <div class="logout-features">
            <div class="feature-item">
                <span class="feature-icon">💼</span>
                <div class="feature-text">Sessão Ativa</div>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🔒</span>
                <div class="feature-text">Dados Protegidos</div>
            </div>
            <div class="feature-item">
                <span class="feature-icon">⏱️</span>
                <div class="feature-text">Tempo Online</div>
            </div>
        </div>

        <!-- Formulário de Logout -->
        <form method="POST" action="" id="logoutForm">
            <div class="logout-actions">
                <button type="submit" name="confirmar_logout" class="btn btn-logout">
                    <span>✅</span>
                    Sim, Sair
                </button>
                <a href="dashboard.php" class="btn btn-cancel">
                    <span>❌</span>
                    Cancelar
                </a>
            </div>
        </form>

        <!-- Aviso de Segurança -->
        <div class="security-notice">
            <h4>🔒 Aviso de Segurança</h4>
            <p>Ao sair, todos os dados da sua sessão serão removidos com segurança. Você precisará fazer login novamente para acessar o sistema.</p>
        </div>

        <!-- Redirecionamento Automático (Opcional) -->
        <div class="auto-redirect" id="countdownMessage" style="display: none;">
            Redirecionando para login em <span id="countdown">5</span> segundos...
        </div>
    </div>

    <script>
        // Prevenir reenvio do formulário
        let formSubmitted = false;
        
        document.getElementById('logoutForm').addEventListener('submit', function(e) {
            if (formSubmitted) {
                e.preventDefault();
                return;
            }
            
            // Mostrar animação de carregamento
            const logoutBtn = this.querySelector('.btn-logout');
            const originalText = logoutBtn.innerHTML;
            logoutBtn.innerHTML = '<span>⏳</span> Processando...';
            logoutBtn.disabled = true;
            
            // Mostrar contagem regressiva
            document.getElementById('countdownMessage').style.display = 'block';
            let countdown = 5;
            const countdownElement = document.getElementById('countdown');
            
            const countdownInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
            
            formSubmitted = true;
        });

        // Prevenir que o usuário saia sem querer
        let showWarning = true;
        
        window.addEventListener('beforeunload', function(e) {
            if (showWarning) {
                e.preventDefault();
                e.returnValue = 'Tem certeza que deseja sair? Suas alterações podem não ser salvas.';
            }
        });

        // Remover aviso quando o usuário confirmar o logout
        document.querySelector('.btn-logout').addEventListener('click', function() {
            showWarning = false;
        });

        // Tecla Escape para cancelar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = 'dashboard.php';
            }
        });

        // Efeito de digitação no subtítulo (opcional)
        const subtitle = document.querySelector('.logout-subtitle');
        const originalText = subtitle.textContent;
        subtitle.textContent = '';
        
        let charIndex = 0;
        function typeWriter() {
            if (charIndex < originalText.length) {
                subtitle.textContent += originalText.charAt(charIndex);
                charIndex++;
                setTimeout(typeWriter, 30);
            }
        }
        
        // Iniciar efeito de digitação após um breve delay
        setTimeout(typeWriter, 500);

        // Adicionar data e hora atual
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const dateTime = now.toLocaleDateString('pt-BR', options);
            
            // Adicionar data/hora ao user-info
            const userInfo = document.querySelector('.user-info');
            const timeElement = document.createElement('div');
            timeElement.className = 'user-time';
            timeElement.style.cssText = 'color: #999; font-size: 0.8rem; margin-top: 8px;';
            timeElement.textContent = 'Sessão iniciada em: ' + dateTime;
            userInfo.appendChild(timeElement);
        });
    </script>
</body>
</html>