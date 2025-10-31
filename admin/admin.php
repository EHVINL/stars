<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// === PARTE MODIFICADA: Buscar estatísticas REAIS do banco ===
// Buscar estatísticas para o dashboard
$stats = getDashboardStats($pdo);

// Buscar atividades recentes (AJUSTADO para seu banco)
try {
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
} catch (PDOException $e) {
    // Em caso de erro, define arrays vazios
    $atividades_recentes = [];
    $distribuicao_usuarios = [];
    $status_modelos = [];
    $status_jobs = [];
}
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
        
        body {
            background-color: #000000;
            color: #ffffff;
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
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid #374151;
            transition: all 0.3s ease;
            color: white;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.3), 0 10px 10px -5px rgba(139, 92, 246, 0.2);
            border-color: #8b5cf6;
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
        
        /* Cores para texto no tema escuro */
        .text-gray-800 {
            color: #f3f4f6 !important;
        }
        
        .text-gray-600 {
            color: #d1d5db !important;
        }
        
        .bg-white {
            background-color: #1f2937 !important;
            border-color: #374151;
        }
        
        .bg-gray-50 {
            background-color: #111827 !important;
        }
        
        .bg-gray-200 {
            background-color: #374151 !important;
        }
        
        .border-gray-300 {
            border-color: #4b5563 !important;
        }
        
        .hover\:bg-gray-100:hover {
            background-color: #374151 !important;
        }
        
        .border {
            border-color: #374151 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(139, 92, 246, 0.1) !important;
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
<body class="bg-black text-white font-sans antialiased">
    <!-- Container Principal -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar text-white w-64 fixed inset-y-0 left-0 z-50">
            <div class="p-6">
                <!-- Logo MODIFICADA -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                        <i data-feather="star" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold">STARS MODELS</span>
                </div>
                
                <!-- Menu de Navegação - CORRIGIDO para seus arquivos -->
                <nav class="space-y-2">
                    <a href="dashboard.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="modelos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Modelos</span>
                        <?php if($stats['modelos_pendentes'] > 0): ?>
                            <span class="notification-badge bg-red-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['modelos_pendentes']; ?></span>
                        <?php else: ?>
                            <span class="bg-green-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['total_modelos']; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="jobs.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Vagas</span>
                        <span class="bg-purple-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $stats['total_jobs']; ?></span>
                    </a>
                    
                    <!-- REMOVIDO: Item de contatos -->
                    
                    <a href="../logout.php" class="sidebar-item flex items-center space-x-3 p-3 text-red-300 hover:text-red-200">
                        <i data-feather="log-out" class="w-5 h-5"></i>
                        <span>Sair</span>
                    </a>
                </nav>
                
                <!-- Divisor -->
                <div class="pt-4 mt-4 border-t border-purple-700">
                    <a href="../home.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="globe" class="w-5 h-5"></i>
                        <span>Ver Site</span>
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
            <header class="bg-gray-900 shadow-sm border-b border-gray-800">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-800 transition duration-300">
                            <i data-feather="menu" class="w-6 h-6 text-gray-400"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-white ml-4">Dashboard</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-400">Olá,</p>
                            <p class="font-medium text-white"><?php echo $_SESSION['user_name']; ?></p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <main class="flex-1 overflow-y-auto p-6 bg-black">
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
                            <div class="p-3 bg-blue-500/20 rounded-lg mr-4">
                                <i data-feather="users" class="w-6 h-6 text-blue-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-white"><?php echo $stats['total_usuarios']; ?></p>
                                <p class="text-gray-300">Total de Usuários</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">Crescimento</span>
                                <span class="text-green-400 font-medium">+12%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modelos Ativos -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500/20 rounded-lg mr-4">
                                <i data-feather="user-check" class="w-6 h-6 text-green-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-white"><?php echo $stats['total_modelos']; ?></p>
                                <p class="text-gray-300">Modelos Ativos</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">Aprovação</span>
                                <span class="text-green-400 font-medium">+8%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Jobs Abertos -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500/20 rounded-lg mr-4">
                                <i data-feather="briefcase" class="w-6 h-6 text-purple-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-white"><?php echo $stats['total_jobs']; ?></p>
                                <p class="text-gray-300">Vagas Abertas</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">Preenchimento</span>
                                <span class="text-green-400 font-medium">+15%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modelos Pendentes -->
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500/20 rounded-lg mr-4">
                                <i data-feather="clock" class="w-6 h-6 text-yellow-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-white"><?php echo $stats['modelos_pendentes']; ?></p>
                                <p class="text-gray-300">Modelos Pendentes</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-400">Revisão</span>
                                <span class="text-yellow-400 font-medium">Pendente</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full" style="width: <?php echo $stats['modelos_pendentes'] > 0 ? '85' : '0'; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações Rápidas e Atividade Recente -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Ações Rápidas -->
                    <div class="bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-800">
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-400"></i>
                            Ações Rápidas
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="modelos.php" class="p-4 border-2 border-dashed border-gray-700 rounded-lg text-center hover:border-purple-500 hover:bg-purple-500/10 transition duration-300 group">
                                <i data-feather="user-plus" class="w-8 h-8 text-gray-400 mx-auto mb-2 group-hover:text-purple-400"></i>
                                <p class="font-medium text-white">Gerenciar Modelos</p>
                                <p class="text-sm text-gray-400">
                                    <?php echo $stats['modelos_pendentes']; ?> 
                                    <?php echo $stats['modelos_pendentes'] == 1 ? 'pendente' : 'pendentes'; ?>
                                </p>
                            </a>
                            
                            <a href="jobs.php" class="p-4 border-2 border-dashed border-gray-700 rounded-lg text-center hover:border-purple-500 hover:bg-purple-500/10 transition duration-300 group">
                                <i data-feather="briefcase" class="w-8 h-8 text-gray-400 mx-auto mb-2 group-hover:text-purple-400"></i>
                                <p class="font-medium text-white">Gerenciar Vagas</p>
                                <p class="text-sm text-gray-400">
                                    <?php echo $stats['total_jobs']; ?> 
                                    <?php echo $stats['total_jobs'] == 1 ? 'vaga' : 'vagas'; ?>
                                </p>
                            </a>
                            
                            <a href="jobs.php?action=add" class="p-4 border-2 border-dashed border-gray-700 rounded-lg text-center hover:border-purple-500 hover:bg-purple-500/10 transition duration-300 group">
                                <i data-feather="plus" class="w-8 h-8 text-gray-400 mx-auto mb-2 group-hover:text-purple-400"></i>
                                <p class="font-medium text-white">Nova Vaga</p>
                                <p class="text-sm text-gray-400">Criar oportunidade</p>
                            </a>
                            
                            <a href="modelo_form.php?action=add" class="p-4 border-2 border-dashed border-gray-700 rounded-lg text-center hover:border-purple-500 hover:bg-purple-500/10 transition duration-300 group">
                                <i data-feather="user-plus" class="w-8 h-8 text-gray-400 mx-auto mb-2 group-hover:text-purple-400"></i>
                                <p class="font-medium text-white">Novo Modelo</p>
                                <p class="text-sm text-gray-400">Adicionar talento</p>
                            </a>
                        </div>
                    </div>

                    <!-- Atividade Recente -->
                    <div class="bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-800">
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                            <i data-feather="activity" class="w-5 h-5 mr-2 text-green-400"></i>
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
                                        'usuário' => 'text-blue-400',
                                        'modelo' => 'text-green-400',
                                        'job' => 'text-purple-400'
                                    ][$atividade['tipo']] ?? 'text-gray-400';
                                ?>
                                <div class="flex items-center space-x-3 p-3 bg-gray-800 rounded-lg">
                                    <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center border border-gray-600">
                                        <i data-feather="<?php echo $icone; ?>" class="w-4 h-4 <?php echo $cor; ?>"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-white truncate">
                                            Novo <?php echo $atividade['tipo']; ?> cadastrado
                                        </p>
                                        <p class="text-sm text-gray-400 truncate"><?php echo htmlspecialchars($atividade['nome']); ?></p>
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
                    <div class="bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-800">
                        <h2 class="text-xl font-bold text-white mb-4">Distribuição de Usuários</h2>
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
                                    <span class="font-medium text-white flex items-center">
                                        <span class="w-3 h-3 <?php echo $cores[$tipo['tipo']] ?? 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                        <?php echo ucfirst($tipo['tipo']); ?>s
                                    </span>
                                    <span class="text-gray-400">
                                        <?php echo $tipo['total']; ?> 
                                        (<?php echo round($percent, 1); ?>%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-3">
                                    <div class="h-3 rounded-full <?php echo $cores[$tipo['tipo']] ?? 'bg-gray-500'; ?>" 
                                         style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Status dos Modelos -->
                    <div class="bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-800">
                        <h2 class="text-xl font-bold text-white mb-4">Status dos Modelos</h2>
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
                                    <span class="font-medium text-white flex items-center">
                                        <span class="w-3 h-3 <?php echo $cores[$status['status']] ?? 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                        <?php echo ucfirst($status['status']); ?>
                                    </span>
                                    <span class="text-gray-400">
                                        <?php echo $status['total']; ?> 
                                        (<?php echo round($percent, 1); ?>%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-3">
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
    </script>
</body>
</html>