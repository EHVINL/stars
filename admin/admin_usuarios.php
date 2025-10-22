<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Buscar usuários
$search = $_GET['search'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT * FROM usuarios WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";
$params = [];
$count_params = [];

if ($search) {
    $sql .= " AND (nome LIKE ? OR email LIKE ?)";
    $count_sql .= " AND (nome LIKE ? OR email LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

if ($tipo_filter) {
    $sql .= " AND tipo = ?";
    $count_sql .= " AND tipo = ?";
    $params[] = $tipo_filter;
    $count_params[] = $tipo_filter;
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($count_params);
$total_usuarios = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_usuarios / $limit);

// Estatísticas
$stmt_stats = $pdo->query("SELECT tipo, COUNT(*) as total FROM usuarios GROUP BY tipo");
$distribuicao = $stmt_stats->fetchAll();

// Ações
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'delete' && $id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Usuário excluído com sucesso!";
        header('Location: admin_usuarios.php');
        exit;
    } elseif ($action === 'toggle_status') {
        // Em um sistema real, teríamos um campo de status
        $_SESSION['flash_message'] = "Status alterado com sucesso!";
        header('Location: admin_usuarios.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Usuários - Admin</title>
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
        
        .table-row:hover {
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .badge-admin { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-modelo { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .badge-cliente { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        
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
                    <a href="admin.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="admin_usuarios.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Usuários</span>
                        <span class="bg-blue-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $total_usuarios; ?></span>
                    </a>
                    
                    <a href="admin_modelos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="user-check" class="w-5 h-5"></i>
                        <span>Modelos</span>
                    </a>
                    
                    <a href="admin_jobs.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Jobs</span>
                    </a>
                    
                    <a href="admin_noticias.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                        <span>Notícias</span>
                    </a>
                    
                    <a href="admin_contatos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="mail" class="w-5 h-5"></i>
                        <span>Mensagens</span>
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Usuários</h1>
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

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Flash Message -->
                <?php if(isset($_SESSION['flash_message'])): ?>
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-lg">
                        <div class="flex items-center">
                            <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                            <?php echo $_SESSION['flash_message']; ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $total_usuarios; ?></div>
                        <div class="text-gray-600">Total de Usuários</div>
                    </div>
                    <?php foreach($distribuicao as $dist): ?>
                    <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $dist['total']; ?></div>
                        <div class="text-gray-600"><?php echo ucfirst($dist['tipo']); ?>s</div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Filtros e Busca -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                    placeholder="Buscar por nome ou email..." 
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <i data-feather="search" class="absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <select name="tipo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos os tipos</option>
                                <option value="admin" <?php echo $tipo_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="modelo" <?php echo $tipo_filter === 'modelo' ? 'selected' : ''; ?>>Modelo</option>
                                <option value="cliente" <?php echo $tipo_filter === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                            </select>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-300 flex items-center">
                                <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                                Filtrar
                            </button>
                            <a href="admin_usuarios.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Usuários -->
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-800">Lista de Usuários</h2>
                            <span class="text-sm text-gray-600">
                                <?php echo $total_usuarios; ?> 
                                <?php echo $total_usuarios == 1 ? 'usuário encontrado' : 'usuários encontrados'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($usuarios as $usuario): ?>
                                <tr class="table-row transition duration-300">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold mr-3 shadow-lg">
                                                <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($usuario['nome']); ?></div>
                                                <div class="text-sm text-gray-500">ID: #<?php echo $usuario['id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <?php echo htmlspecialchars($usuario['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs rounded-full badge-<?php echo $usuario['tipo']; ?> font-medium">
                                            <?php echo ucfirst($usuario['tipo']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <?php echo date('d/m/Y H:i', strtotime($usuario['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="editarUsuario(<?php echo $usuario['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition duration-300"
                                                title="Editar usuário">
                                                <i data-feather="edit" class="w-4 h-4"></i>
                                            </button>
                                            
                                            <?php if($usuario['id'] != $_SESSION['user_id']): ?>
                                            <button onclick="toggleStatus(<?php echo $usuario['id']; ?>)" 
                                                class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition duration-300"
                                                title="Alterar status">
                                                <i data-feather="user-check" class="w-4 h-4"></i>
                                            </button>
                                            
                                            <button onclick="confirmarExclusao(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nome']); ?>')" 
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition duration-300"
                                                title="Excluir usuário">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                            <?php else: ?>
                                            <span class="text-gray-400 p-2" title="Você não pode modificar seu próprio usuário">
                                                <i data-feather="lock" class="w-4 h-4"></i>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <?php if($total_paginas > 1): ?>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Mostrando 
                                <span class="font-medium"><?php echo ($offset + 1); ?></span>
                                a 
                                <span class="font-medium"><?php echo min($offset + $limit, $total_usuarios); ?></span>
                                de 
                                <span class="font-medium"><?php echo $total_usuarios; ?></span>
                                resultados
                            </div>
                            
                            <div class="flex space-x-1">
                                <?php if($page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                       class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                                        <i data-feather="chevron-left" class="w-4 h-4"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                                    <?php if($i == 1 || $i == $total_paginas || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                           class="px-3 py-2 border border-gray-300 rounded-lg transition duration-300 <?php echo $i == $page ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php elseif($i == $page - 3 || $i == $page + 3): ?>
                                        <span class="px-3 py-2 text-gray-500">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if($page < $total_paginas): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                       class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if(empty($usuarios)): ?>
                <div class="text-center py-12">
                    <i data-feather="users" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Nenhum usuário encontrado</h3>
                    <p class="text-gray-600">Tente ajustar os filtros de busca</p>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        function editarUsuario(id) {
            alert('Editar usuário ID: ' + id + '\n\nEm produção, abriria um modal de edição.');
            // window.location.href = 'editar_usuario.php?id=' + id;
        }
        
        function toggleStatus(id) {
            if (confirm('Deseja alterar o status deste usuário?')) {
                window.location.href = 'admin_usuarios.php?action=toggle_status&id=' + id;
            }
        }
        
        function confirmarExclusao(id, nome) {
            if (confirm('Tem certeza que deseja excluir o usuário "' + nome + '"?\n\nEsta ação não pode ser desfeita!')) {
                window.location.href = 'admin_usuarios.php?action=delete&id=' + id;
            }
        }
        
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
        
        // Auto-focus na busca
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
</body>
</html>