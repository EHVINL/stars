<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Configurações padrão (em um sistema real, isso viria do banco)
$configuracoes = [
    'site_nome' => 'Stars Models Agency',
    'site_email' => 'contato@starsmodels.com',
    'site_telefone' => '(61) 98765-4321',
    'site_endereco' => 'Asa Norte, Brasília - DF',
    'manutencao' => '0',
    'novos_cadastros' => '1'
];

if ($_POST) {
    $success = "Configurações salvas com sucesso!";
    // Em um sistema real, salvaria no banco
}

// Backup do banco (simulação)
if (isset($_GET['backup'])) {
    $success = "Backup do banco de dados realizado com sucesso!";
}

// Limpar cache (simulação)
if (isset($_GET['limpar_cache'])) {
    $success = "Cache limpo com sucesso!";
}

// Estatísticas do sistema
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos");
$total_modelos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
$total_jobs = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM noticias");
$total_noticias = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Configurações - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="ml-64 flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                            <i data-feather="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Configurações do Sistema</h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <?php if(isset($success)): ?>
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Configurações Gerais -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="border-b p-6">
                                <h2 class="text-xl font-bold text-gray-800">Configurações Gerais</h2>
                                <p class="text-gray-600 mt-1">Configure as informações básicas do site</p>
                            </div>
                            
                            <form method="POST" class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Site</label>
                                        <input type="text" name="site_nome" value="<?php echo $configuracoes['site_nome']; ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email de Contato</label>
                                        <input type="email" name="site_email" value="<?php echo $configuracoes['site_email']; ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                                        <input type="text" name="site_telefone" value="<?php echo $configuracoes['site_telefone']; ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                                        <input type="text" name="site_endereco" value="<?php echo $configuracoes['site_endereco']; ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-6 border-t">
                                    <div>
                                        <h3 class="font-medium text-gray-800">Modo Manutenção</h3>
                                        <p class="text-sm text-gray-600">O site ficará indisponível para visitantes</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="manutencao" value="1" 
                                            <?php echo $configuracoes['manutencao'] == '1' ? 'checked' : ''; ?>
                                            class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between pt-4 border-t">
                                    <div>
                                        <h3 class="font-medium text-gray-800">Permitir Novos Cadastros</h3>
                                        <p class="text-sm text-gray-600">Habilitar/desabilitar novos registros</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="novos_cadastros" value="1" 
                                            <?php echo $configuracoes['novos_cadastros'] == '1' ? 'checked' : ''; ?>
                                            class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                    </label>
                                </div>
                                
                                <div class="flex justify-end pt-6">
                                    <button type="submit" 
                                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                                        <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                                        Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Estatísticas do Sistema -->
                        <div class="mt-6 bg-white rounded-lg shadow-sm border">
                            <div class="border-b p-6">
                                <h2 class="text-xl font-bold text-gray-800">Estatísticas do Sistema</h2>
                                <p class="text-gray-600 mt-1">Visão geral da base de dados</p>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600"><?php echo $total_usuarios; ?></div>
                                        <div class="text-blue-800">Usuários</div>
                                    </div>
                                    
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600"><?php echo $total_modelos; ?></div>
                                        <div class="text-green-800">Modelos</div>
                                    </div>
                                    
                                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                                        <div class="text-2xl font-bold text-purple-600"><?php echo $total_jobs; ?></div>
                                        <div class="text-purple-800">Jobs</div>
                                    </div>
                                    
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600"><?php echo $total_noticias; ?></div>
                                        <div class="text-yellow-800">Notícias</div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="font-medium text-gray-800 mb-3">Distribuição de Usuários</h3>
                                        <?php
                                        $stmt = $pdo->query("SELECT tipo, COUNT(*) as total FROM usuarios GROUP BY tipo");
                                        $tipos_usuarios = $stmt->fetchAll();
                                        
                                        foreach($tipos_usuarios as $tipo):
                                            $percent = ($tipo['total'] / $total_usuarios) * 100;
                                        ?>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600"><?php echo ucfirst($tipo['tipo']); ?>s</span>
                                                <span class="text-gray-800"><?php echo $tipo['total']; ?> (<?php echo round($percent, 1); ?>%)</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div>
                                        <h3 class="font-medium text-gray-800 mb-3">Status dos Modelos</h3>
                                        <?php
                                        $stmt = $pdo->query("SELECT status, COUNT(*) as total FROM modelos GROUP BY status");
                                        $status_modelos = $stmt->fetchAll();
                                        
                                        $cores = [
                                            'ativo' => 'bg-green-500',
                                            'inativo' => 'bg-gray-500', 
                                            'pendente' => 'bg-yellow-500'
                                        ];
                                        
                                        foreach($status_modelos as $status):
                                            $total_modelos_status = array_sum(array_column($status_modelos, 'total'));
                                            $percent = $total_modelos_status > 0 ? ($status['total'] / $total_modelos_status) * 100 : 0;
                                        ?>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600 flex items-center">
                                                    <span class="w-3 h-3 <?php echo $cores[$status['status']]; ?> rounded-full mr-2"></span>
                                                    <?php echo ucfirst($status['status']); ?>
                                                </span>
                                                <span class="text-gray-800"><?php echo $status['total']; ?> (<?php echo round($percent, 1); ?>%)</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="<?php echo $cores[$status['status']]; ?> h-2 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ferramentas do Sistema -->
                    <div class="space-y-6">
                        <!-- Backup -->
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Backup do Sistema</h3>
                            <p class="text-gray-600 mb-4">Crie um backup completo do banco de dados</p>
                            <a href="admin_config.php?backup=1" 
                               class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                                <i data-feather="download" class="w-4 h-4 mr-2"></i>
                                Fazer Backup Agora
                            </a>
                            <p class="text-gray-500 text-sm mt-3">
                                <i data-feather="info" class="w-4 h-4 inline mr-1"></i>
                                Último backup: <?php echo date('d/m/Y H:i'); ?>
                            </p>
                        </div>

                        <!-- Limpar Cache -->
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Limpar Cache</h3>
                            <p class="text-gray-600 mb-4">Limpe o cache do sistema para melhor performance</p>
                            <a href="admin_config.php?limpar_cache=1" 
                               class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-300">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar Cache
                            </a>
                        </div>

                        <!-- Informações do Sistema -->
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Informações do Sistema</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">PHP Version</span>
                                    <span class="font-medium"><?php echo phpversion(); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Database</span>
                                    <span class="font-medium">MySQL</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Servidor</span>
                                    <span class="font-medium"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Usuários Online</span>
                                    <span class="font-medium"><?php echo rand(5, 50); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Espaço em Disco</span>
                                    <span class="font-medium"><?php echo round(disk_free_space("/") / (1024 * 1024 * 1024), 1); ?> GB livre</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Ações Rápidas</h3>
                            <div class="space-y-3">
                                <a href="../home.php" target="_blank" 
                                   class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-300">
                                    <span>Ver Site</span>
                                    <i data-feather="external-link" class="w-4 h-4 text-gray-400"></i>
                                </a>
                                <a href="admin.php" 
                                   class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-300">
                                    <span>Dashboard</span>
                                    <i data-feather="home" class="w-4 h-4 text-gray-400"></i>
                                </a>
                                <a href="../logout.php" 
                                   class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-red-600 transition duration-300">
                                    <span>Sair do Sistema</span>
                                    <i data-feather="log-out" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Logs do Sistema -->
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Logs Recentes</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Sistema iniciado</span>
                                    <span class="text-gray-400 ml-auto"><?php echo date('H:i'); ?></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Backup automático</span>
                                    <span class="text-gray-400 ml-auto"><?php echo date('H:i', strtotime('-1 hour')); ?></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Novo usuário</span>
                                    <span class="text-gray-400 ml-auto"><?php echo date('H:i', strtotime('-2 hours')); ?></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Job publicado</span>
                                    <span class="text-gray-400 ml-auto"><?php echo date('H:i', strtotime('-3 hours')); ?></span>
                                </div>
                            </div>
                            <a href="#" class="block text-center mt-4 text-purple-600 hover:text-purple-700 text-sm font-medium">
                                Ver logs completos →
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });

        // Simular ações do sistema
        document.querySelector('a[href*="backup"]').addEventListener('click', function(e) {
            if (!confirm('Deseja realmente fazer backup do banco de dados?')) {
                e.preventDefault();
            }
        });

        document.querySelector('a[href*="limpar_cache"]').addEventListener('click', function(e) {
            if (!confirm('Deseja limpar todo o cache do sistema?')) {
                e.preventDefault();
            }
        });

        // Atualizar estatísticas em tempo real
        function atualizarEstatisticas() {
            // Em um sistema real, faria uma requisição AJAX
            console.log('Atualizando estatísticas...');
        }

        // Atualizar a cada 30 segundos
        setInterval(atualizarEstatisticas, 30000);
    </script>
</body>
</html>