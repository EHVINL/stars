<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Buscar estatísticas para o dashboard
$stats = getDashboardStats($pdo);

// Buscar atividades recentes
$stmt = $pdo->query("
    (SELECT 'usuário' as tipo, nome, created_at as data, id FROM usuarios ORDER BY created_at DESC LIMIT 3)
    UNION ALL
    (SELECT 'modelo' as tipo, 
            (SELECT nome FROM usuarios WHERE id = modelos.usuario_id) as nome, 
            created_at as data, 
            id 
     FROM modelos 
     ORDER BY created_at DESC LIMIT 3)
    UNION ALL  
    (SELECT 'job' as tipo, titulo as nome, data_publicacao as data, id FROM jobs ORDER BY data_publicacao DESC LIMIT 3)
    ORDER BY data DESC 
    LIMIT 6
");
$atividades_recentes = $stmt->fetchAll();

// Buscar distribuição de usuários
$stmt = $pdo->query("SELECT tipo, COUNT(*) as total FROM usuarios GROUP BY tipo");
$distribuicao_usuarios = $stmt->fetchAll();

// Buscar status dos modelos
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM modelos GROUP BY status");
$status_modelos = $stmt->fetchAll();

// Buscar jobs por status
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM jobs GROUP BY status");
$status_jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel Administrativo - Stars Models</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 50%, #7e22ce 100%);
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-item {
            transition: all 0.3s ease;
            border-radius: 0.75rem;
        }
        
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #8b5cf6;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #8b5cf6, #a855f7, #ec4899);
            animation: progress 2s ease-in-out;
        }
        
        @keyframes progress {
            from { width: 0%; }
        }
        
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Container Principal -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar text-white w-64 fixed inset-y-0 left-0 z-50">
            <div class="p-6">
                <!-- Logo -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i data-feather="star" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">STARS MODELS</h1>
                        <span class="text-xs bg-purple-500 px-2 py-1 rounded-full">ADMIN</span>
                    </div>
                </div>
                
                <!-- Menu de Navegação -->
                <nav class="space-y-2">
                    <a href="admin.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="admin_usuarios.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Usuários</span>
                        <span class="bg-blue-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['total_usuarios']; ?></span>
                    </a>
                    
                    <a href="admin_modelos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="user-check" class="w-5 h-5"></i>
                        <span>Modelos</span>
                        <?php if($stats['modelos_pendentes'] > 0): ?>
                            <span class="notification-badge bg-red-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['modelos_pendentes']; ?></span>
                        <?php else: ?>
                            <span class="bg-green-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['total_modelos']; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="admin_jobs.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Jobs</span>
                        <span class="bg-purple-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['total_jobs']; ?></span>
                    </a>
                    
                    <a href="admin_noticias.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                        <span>Notícias</span>
                    </a>
                    
                    <a href="admin_contatos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="mail" class="w-5 h-5"></i>
                        <span>Mensagens</span>
                        <?php if($stats['novos_contatos'] > 0): ?>
                            <span class="notification-badge bg-red-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['novos_contatos']; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="admin_config.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="settings" class="w-5 h-5"></i>
                        <span>Configurações</span>
                    </a>
                </nav>
                
                <!-- Divisor -->
                <div class="pt-4 mt-4 border-t border-purple-700">
                    <a href="../home.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="globe" class="w-5 h-5"></i>
                        <span>Ver Site</span>
                    </a>
                    
                    <a href="../logout.php" class="sidebar-item flex items-center space-x-3 p-3 text-red-300 hover:text-red-200">
                        <i data-feather="log-out" class="w-5 h-5"></i>
                        <span>Sair</span>
                    </a>
                </div>
                
                <!-- Status do Sistema -->
                <div class="mt-8 p-4 bg-purple-800/30 rounded-lg">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-purple-200">Status</span>
                        <span class="text-green-400 flex items-center">
                            <i data-feather="check-circle" class="w-3 h-3 mr-1"></i>
                            Online
                        </span>
                    </div>
                    <div class="text-xs text-purple-300">
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="ml-64 flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition duration-300">
                            <i data-feather="menu" class="w-6 h-6 text-gray-600"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Dashboard</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Olá,</p>
                            <p class="font-medium text-gray-800"><?php echo $_SESSION['user_name']; ?></p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Mensagem de Boas-Vindas -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-6 text-white shadow-lg mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Bem-vindo de volta, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>! 👋</h2>
                            <p class="text-purple-100">Aqui está o resumo do que está acontecendo na sua agência.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-purple-200">Status do Sistema</p>
                            <p class="text-lg font-bold">Tudo Operacional</p>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total de Usuários -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_usuarios']; ?></p>
                                <p class="text-gray-600">Total de Usuários</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Crescimento</span>
                                <span class="text-green-500 font-medium">+12%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modelos Ativos -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i data-feather="user-check" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_modelos']; ?></p>
                                <p class="text-gray-600">Modelos Ativos</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Aprovação</span>
                                <span class="text-green-500 font-medium">+8%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Jobs Abertos -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_jobs']; ?></p>
                                <p class="text-gray-600">Jobs Abertos</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Preenchimento</span>
                                <span class="text-green-500 font-medium">+15%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Novas Mensagens -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-lg mr-4">
                                <i data-feather="mail" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800"><?php echo $stats['novos_contatos']; ?></p>
                                <p class="text-gray-600">Novas Mensagens</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Resposta</span>
                                <span class="text-green-500 font-medium">+20%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações Rápidas e Atividade Recente -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Ações Rápidas -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-500"></i>
                            Ações Rápidas
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="admin_modelos.php?filter=pending" class="p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-purple-500 hover:bg-purple-50 transition duration-300 group">
                                <i data-feather="user-plus" class="w-8 h-8 text-gray-600 mx-auto mb-2 group-hover:text-purple-600"></i>
                                <p class="font-medium text-gray-800">Aprovar Modelos</p>
                                <p class="text-sm text-gray-600">
                                    <?php echo $stats['modelos_pendentes']; ?> 
                                    <?php echo $stats['modelos_pendentes'] == 1 ? 'pendente' : 'pendentes'; ?>
                                </p>
                            </a>
                            
                            <a href="admin_contatos.php" class="p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-purple-500 hover:bg-purple-50 transition duration-300 group">
                                <i data-feather="message-square" class="w-8 h-8 text-gray-600 mx-auto mb-2 group-hover:text-purple-600"></i>
                                <p class="font-medium text-gray-800">Ver Mensagens</p>
                                <p class="text-sm text-gray-600">
                                    <?php echo $stats['novos_contatos']; ?> 
                                    <?php echo $stats['novos_contatos'] == 1 ? 'nova' : 'novas'; ?>
                                </p>
                            </a>
                            
                            <a href="admin_jobs.php?action=new" class="p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-purple-500 hover:bg-purple-50 transition duration-300 group">
                                <i data-feather="plus" class="w-8 h-8 text-gray-600 mx-auto mb-2 group-hover:text-purple-600"></i>
                                <p class="font-medium text-gray-800">Novo Job</p>
                                <p class="text-sm text-gray-600">Criar oportunidade</p>
                            </a>
                            
                            <a href="admin_noticias.php?action=new" class="p-4 border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-purple-500 hover:bg-purple-50 transition duration-300 group">
                                <i data-feather="edit" class="w-8 h-8 text-gray-600 mx-auto mb-2 group-hover:text-purple-600"></i>
                                <p class="font-medium text-gray-800">Nova Notícia</p>
                                <p class="text-sm text-gray-600">Publicar conteúdo</p>
                            </a>
                        </div>
                    </div>

                    <!-- Atividade Recente -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i data-feather="activity" class="w-5 h-5 mr-2 text-green-500"></i>
                            Atividade Recente
                        </h2>
                        <div class="space-y-4">
                            <?php if(!empty($atividades_recentes)): ?>
                                <?php foreach($atividades_recentes as $atividade): 
                                    $icone = [
                                        'usuário' => 'user',
                                        'modelo' => 'user-check', 
                                        'job' => 'briefcase'
                                    ][$atividade['tipo']] ?? 'circle';
                                    
                                    $cor = [
                                        'usuário' => 'text-blue-500',
                                        'modelo' => 'text-green-500',
                                        'job' => 'text-purple-500'
                                    ][$atividade['tipo']] ?? 'text-gray-500';
                                ?>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center border">
                                        <i data-feather="<?php echo $icone; ?>" class="w-4 h-4 <?php echo $cor; ?>"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800 truncate">
                                            Novo <?php echo $atividade['tipo']; ?> cadastrado
                                        </p>
                                        <p class="text-sm text-gray-600 truncate"><?php echo htmlspecialchars($atividade['nome']); ?></p>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                        <?php echo date('d/m H:i', strtotime($atividade['data'])); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i data-feather="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                    <p>Nenhuma atividade recente</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Gráficos/Estatísticas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Distribuição de Usuários -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Distribuição de Usuários</h2>
                        <div class="space-y-4">
                            <?php 
                            $total_usuarios = array_sum(array_column($distribuicao_usuarios, 'total'));
                            foreach($distribuicao_usuarios as $tipo): 
                                $percent = $total_usuarios > 0 ? ($tipo['total'] / $total_usuarios) * 100 : 0;
                                $cores = [
                                    'admin' => 'bg-red-500',
                                    'modelo' => 'bg-blue-500',
                                    'cliente' => 'bg-green-500'
                                ];
                            ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-800 flex items-center">
                                        <span class="w-3 h-3 <?php echo $cores[$tipo['tipo']] ?? 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                        <?php echo ucfirst($tipo['tipo']); ?>s
                                    </span>
                                    <span class="text-gray-600">
                                        <?php echo $tipo['total']; ?> 
                                        (<?php echo round($percent, 1); ?>%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full <?php echo $cores[$tipo['tipo']] ?? 'bg-gray-500'; ?>" 
                                         style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Status dos Modelos -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Status dos Modelos</h2>
                        <div class="space-y-4">
                            <?php 
                            $total_modelos_stats = array_sum(array_column($status_modelos, 'total'));
                            foreach($status_modelos as $status): 
                                $percent = $total_modelos_stats > 0 ? ($status['total'] / $total_modelos_stats) * 100 : 0;
                                $cores = [
                                    'ativo' => 'bg-green-500',
                                    'inativo' => 'bg-gray-500', 
                                    'pendente' => 'bg-yellow-500'
                                ];
                            ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-800 flex items-center">
                                        <span class="w-3 h-3 <?php echo $cores[$status['status']] ?? 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                        <?php echo ucfirst($status['status']); ?>
                                    </span>
                                    <span class="text-gray-600">
                                        <?php echo $status['total']; ?> 
                                        (<?php echo round($percent, 1); ?>%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full <?php echo $cores[$status['status']] ?? 'bg-gray-500'; ?>" 
                                         style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        
        // Fechar menu ao clicar fora (mobile)
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.getElementById('menuToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
        
        // Atualizar dados em tempo real (simulação)
        function updateStats() {
            // Em produção, faria uma requisição AJAX
            console.log('Atualizando estatísticas...');
        }
        
        // Atualizar a cada 30 segundos
        setInterval(updateStats, 30000);
        
        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
</body>
</html>