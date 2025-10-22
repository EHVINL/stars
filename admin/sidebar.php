<?php
require_once '../includes/config.php';

// Verificar se está no contexto admin
if (!isset($pdo)) {
    require_once '../includes/config.php';
}

// Estatísticas para a sidebar
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contatos WHERE status = 'novo'");
    $novos_contatos = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'pendente'");
    $modelos_pendentes = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM candidaturas WHERE status = 'pendente'");
    $candidaturas_pendentes = $stmt->fetch()['total'];
} catch (PDOException $e) {
    $novos_contatos = 0;
    $modelos_pendentes = 0;
    $candidaturas_pendentes = 0;
}

// Determinar página atual para highlight do menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="sidebar bg-purple-900 text-white w-64 fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo e Header -->
        <div class="p-6 border-b border-purple-700">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-600 p-2 rounded-lg">
                    <i data-feather="star" class="w-6 h-6 text-purple-200"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">STARS MODELS</h1>
                    <p class="text-purple-300 text-sm">Painel Admin</p>
                </div>
            </div>
        </div>

        <!-- Menu de Navegação -->
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="space-y-2 px-4">
                <!-- Dashboard -->
                <a href="admin.php" class="<?php echo $current_page == 'admin.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="home" class="w-5 h-5 <?php echo $current_page == 'admin.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Usuários -->
                <a href="admin_usuarios.php" class="<?php echo $current_page == 'admin_usuarios.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="users" class="w-5 h-5 <?php echo $current_page == 'admin_usuarios.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Usuários</span>
                </a>

                <!-- Modelos -->
                <a href="admin_modelos.php" class="<?php echo $current_page == 'admin_modelos.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="user-check" class="w-5 h-5 <?php echo $current_page == 'admin_modelos.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Modelos</span>
                    <?php if($modelos_pendentes > 0): ?>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                        <?php echo $modelos_pendentes; ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Jobs -->
                <a href="admin_jobs.php" class="<?php echo $current_page == 'admin_jobs.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="briefcase" class="w-5 h-5 <?php echo $current_page == 'admin_jobs.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Jobs</span>
                </a>

                <!-- Candidaturas -->
                <a href="admin_candidaturas.php" class="<?php echo $current_page == 'admin_candidaturas.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="file-text" class="w-5 h-5 <?php echo $current_page == 'admin_candidaturas.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Candidaturas</span>
                    <?php if($candidaturas_pendentes > 0): ?>
                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                        <?php echo $candidaturas_pendentes; ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Notícias -->
                <a href="admin_noticias.php" class="<?php echo $current_page == 'admin_noticias.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="edit" class="w-5 h-5 <?php echo $current_page == 'admin_noticias.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Notícias</span>
                </a>

                <!-- Mensagens -->
                <a href="admin_contatos.php" class="<?php echo $current_page == 'admin_contatos.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="mail" class="w-5 h-5 <?php echo $current_page == 'admin_contatos.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Mensagens</span>
                    <?php if($novos_contatos > 0): ?>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                        <?php echo $novos_contatos; ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Configurações -->
                <a href="admin_config.php" class="<?php echo $current_page == 'admin_config.php' ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white'; ?> flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                    <i data-feather="settings" class="w-5 h-5 <?php echo $current_page == 'admin_config.php' ? 'text-white' : 'text-purple-300 group-hover:text-white'; ?>"></i>
                    <span>Configurações</span>
                </a>
            </div>

            <!-- Seção de Relatórios -->
            <div class="mt-8 px-4">
                <h3 class="text-xs uppercase tracking-wider text-purple-400 font-semibold mb-3">Relatórios</h3>
                <div class="space-y-2">
                    <a href="admin_relatorios.php?tipo=usuarios" class="text-purple-200 hover:bg-purple-800 hover:text-white flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                        <i data-feather="bar-chart-2" class="w-4 h-4 text-purple-300 group-hover:text-white"></i>
                        <span class="text-sm">Relatório de Usuários</span>
                    </a>
                    <a href="admin_relatorios.php?tipo=jobs" class="text-purple-200 hover:bg-purple-800 hover:text-white flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                        <i data-feather="trending-up" class="w-4 h-4 text-purple-300 group-hover:text-white"></i>
                        <span class="text-sm">Relatório de Jobs</span>
                    </a>
                    <a href="admin_relatorios.php?tipo=financeiro" class="text-purple-200 hover:bg-purple-800 hover:text-white flex items-center space-x-3 p-3 rounded-lg transition duration-300 group">
                        <i data-feather="dollar-sign" class="w-4 h-4 text-purple-300 group-hover:text-white"></i>
                        <span class="text-sm">Relatório Financeiro</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Footer da Sidebar -->
        <div class="border-t border-purple-700 p-4">
            <!-- Usuário Logado -->
            <div class="flex items-center space-x-3 mb-4 p-3 bg-purple-800/50 rounded-lg">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate"><?php echo $_SESSION['user_name']; ?></p>
                    <p class="text-purple-300 text-xs truncate">Administrador</p>
                </div>
            </div>

            <!-- Links de Ação -->
            <div class="space-y-2">
                <a href="../home.php" target="_blank" class="flex items-center space-x-3 p-3 text-purple-200 hover:bg-purple-800 hover:text-white rounded-lg transition duration-300 group">
                    <i data-feather="globe" class="w-4 h-4 text-purple-300 group-hover:text-white"></i>
                    <span class="text-sm">Ver Site</span>
                </a>
                
                <a href="../perfil.php" class="flex items-center space-x-3 p-3 text-purple-200 hover:bg-purple-800 hover:text-white rounded-lg transition duration-300 group">
                    <i data-feather="user" class="w-4 h-4 text-purple-300 group-hover:text-white"></i>
                    <span class="text-sm">Meu Perfil</span>
                </a>
                
                <a href="../logout.php" class="flex items-center space-x-3 p-3 text-red-400 hover:bg-red-500 hover:text-white rounded-lg transition duration-300 group">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span class="text-sm">Sair</span>
                </a>
            </div>

            <!-- Status do Sistema -->
            <div class="mt-4 pt-4 border-t border-purple-700">
                <div class="flex items-center justify-between text-xs text-purple-400">
                    <span>Status do Sistema</span>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para Mobile -->
<div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden"></div>

<script>
    // Inicializar Feather Icons
    feather.replace();
    
    // Controle do Sidebar Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        
        // Abrir sidebar no mobile
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
            });
        }
        
        // Fechar sidebar no mobile
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
        
        // Fechar ao clicar no overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
        
        // Fechar ao redimensionar para desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
        
        // Adicionar classe active baseado na página atual
        const currentPage = '<?php echo $current_page; ?>';
        const menuItems = document.querySelectorAll('nav a');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPage) {
                item.classList.add('bg-purple-800', 'text-white');
                item.classList.remove('text-purple-200', 'hover:bg-purple-800');
                
                // Atualizar ícone
                const icon = item.querySelector('i');
                if (icon) {
                    icon.classList.add('text-white');
                    icon.classList.remove('text-purple-300');
                }
            }
        });
        
        // Animar contadores de notificação
        const notificationCounters = document.querySelectorAll('.bg-red-500, .bg-yellow-500');
        notificationCounters.forEach(counter => {
            counter.style.animation = 'pulse 2s infinite';
        });
    });
    
    // Adicionar estilo de animação para os contadores
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(139, 92, 246, 0.5) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(139, 92, 246, 0.5);
            border-radius: 20px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(139, 92, 246, 0.7);
        }
        
        .group:hover .group-hover\\:scale-110 {
            transform: scale(1.1);
        }
    `;
    document.head.appendChild(style);
</script>