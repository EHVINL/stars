<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Ações
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE modelos SET status = 'ativo' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Modelo aprovado com sucesso!";
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE modelos SET status = 'inativo' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Modelo rejeitado!";
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM modelos WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Modelo excluído com sucesso!";
    }
    
    header('Location: admin_modelos.php');
    exit;
}

// Filtros
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT m.*, u.nome, u.email, u.telefone, u.created_at as usuario_criado 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total 
              FROM modelos m 
              JOIN usuarios u ON m.usuario_id = u.id 
              WHERE 1=1";
$params = [];
$count_params = [];

if ($status_filter) {
    $sql .= " AND m.status = ?";
    $count_sql .= " AND m.status = ?";
    $params[] = $status_filter;
    $count_params[] = $status_filter;
}

if ($tipo_filter) {
    $sql .= " AND m.tipo_profissao = ?";
    $count_sql .= " AND m.tipo_profissao = ?";
    $params[] = $tipo_filter;
    $count_params[] = $tipo_filter;
}

if ($search) {
    $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR m.tipo_profissao LIKE ?)";
    $count_sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR m.tipo_profissao LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

$sql .= " ORDER BY m.status = 'pendente' DESC, u.nome ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$modelos = $stmt->fetchAll();

$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($count_params);
$total_modelos = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_modelos / $limit);

// Estatísticas
$stmt_stats = $pdo->query("SELECT status, COUNT(*) as total FROM modelos GROUP BY status");
$stats = $stmt_stats->fetchAll();

// Tipos de profissão para filtro
$tipos_profissao = [
    'fashion' => 'Modelo Fashion',
    'comercial' => 'Modelo Comercial',
    'ator' => 'Ator', 
    'atriz' => 'Atriz',
    'alta-costura' => 'Alta Costura',
    'fitness' => 'Fitness',
    'plus-size' => 'Plus Size',
    'kids' => 'Kids',
    'adolescente' => 'Adolescente'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Modelos - Admin</title>
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
        
        .modelo-card {
            transition: all 0.3s ease;
        }
        
        .modelo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        
        .status-ativo { background: #dcfce7; color: #166534; }
        .status-pendente { background: #fef3c7; color: #92400e; }
        .status-inativo { background: #f3f4f6; color: #374151; }
        
        .badge-pulse {
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
                    
                    <a href="admin_usuarios.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Usuários</span>
                    </a>
                    
                    <a href="admin_modelos.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="user-check" class="w-5 h-5"></i>
                        <span>Modelos</span>
                        <?php 
                        $pendentes = array_reduce($stats, function($carry, $item) {
                            return $item['status'] === 'pendente' ? $item['total'] : $carry;
                        }, 0);
                        ?>
                        <?php if($pendentes > 0): ?>
                            <span class="badge-pulse bg-red-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $pendentes; ?></span>
                        <?php else: ?>
                            <span class="bg-green-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $total_modelos; ?></span>
                        <?php endif; ?>
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Modelos</h1>
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
                    <?php foreach($stats as $stat): 
                        $color = $stat['status'] == 'ativo' ? 'bg-green-500' : 
                                ($stat['status'] == 'pendente' ? 'bg-yellow-500' : 'bg-gray-500');
                        $text_color = $stat['status'] == 'ativo' ? 'text-green-600' : 
                                    ($stat['status'] == 'pendente' ? 'text-yellow-600' : 'text-gray-600');
                    ?>
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <div class="flex items-center">
                            <div class="w-3 h-3 <?php echo $color; ?> rounded-full mr-3"></div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800"><?php echo $stat['total']; ?></div>
                                <div class="text-gray-600"><?php echo ucfirst($stat['status']); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                    placeholder="Buscar modelos..." 
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <i data-feather="search" class="absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos os status</option>
                                <option value="pendente" <?php echo $status_filter === 'pendente' ? 'selected' : ''; ?>>Pendentes</option>
                                <option value="ativo" <?php echo $status_filter === 'ativo' ? 'selected' : ''; ?>>Ativos</option>
                                <option value="inativo" <?php echo $status_filter === 'inativo' ? 'selected' : ''; ?>>Inativos</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <select name="tipo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos os tipos</option>
                                <?php foreach($tipos_profissao as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-300 flex items-center">
                                <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                                Filtrar
                            </button>
                            <a href="admin_modelos.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Lista de Modelos -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Modelos Cadastrados</h2>
                        <span class="text-sm text-gray-600">
                            <?php echo $total_modelos; ?> 
                            <?php echo $total_modelos == 1 ? 'modelo encontrado' : 'modelos encontrados'; ?>
                        </span>
                    </div>

                    <?php if(!empty($modelos)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($modelos as $modelo): 
                            $status_color = $modelo['status'] == 'ativo' ? 'status-ativo' : 
                                          ($modelo['status'] == 'pendente' ? 'status-pendente' : 'status-inativo');
                            
                            // Imagem baseada no tipo de profissão
                            $imagens = [
                                'fashion' => 'https://cdn.pixabay.com/photo/2018/04/05/09/32/portrait-3292287_960_720.jpg',
                                'comercial' => 'https://cdn.pixabay.com/photo/2017/07/12/22/52/woman-2498668_1280.jpg',
                                'ator' => 'https://cdn.pixabay.com/photo/2021/06/17/01/32/man-6342455_1280.jpg',
                                'atriz' => 'https://cdn.pixabay.com/photo/2017/08/09/13/35/model-2614569_1280.jpg',
                                'alta-costura' => 'https://cdn.pixabay.com/photo/2025/09/28/12/42/wallpaper-9860463_1280.jpg',
                                'fitness' => 'https://cdn.pixabay.com/photo/2016/11/29/01/34/man-1866574_1280.jpg',
                                'plus-size' => 'https://cdn.pixabay.com/photo/2017/09/07/15/46/young-model-2725720_960_720.jpg',
                                'kids' => 'https://cdn.pixabay.com/photo/2015/07/11/14/53/plait-840124_1280.jpg',
                                'adolescente' => 'https://cdn.pixabay.com/photo/2020/05/08/04/26/girls-5144061_1280.jpg'
                            ];
                            
                            $imagem = $imagens[$modelo['tipo_profissao']] ?? 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png';
                        ?>
                        <div class="modelo-card bg-white rounded-xl overflow-hidden border border-gray-200">
                            <!-- Header do Card -->
                            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 text-white relative">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg"><?php echo htmlspecialchars($modelo['nome']); ?></h3>
                                        <p class="text-purple-200 text-sm"><?php echo htmlspecialchars($modelo['email']); ?></p>
                                    </div>
                                    <span class="status-badge <?php echo $status_color; ?>">
                                        <?php echo ucfirst($modelo['status']); ?>
                                    </span>
                                </div>
                                
                                <?php if($modelo['status'] == 'pendente'): ?>
                                <div class="absolute top-2 right-2">
                                    <span class="badge-pulse bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        Aguardando Aprovação
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Informações do Modelo -->
                            <div class="p-4">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-purple-200">
                                        <img src="<?php echo $imagem; ?>" alt="<?php echo htmlspecialchars($modelo['nome']); ?>" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800"><?php echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Modelo'; ?></p>
                                        <div class="flex items-center space-x-2 text-sm text-gray-600 mt-1">
                                            <?php if($modelo['altura']): ?>
                                                <span><?php echo $modelo['altura']; ?>m</span>
                                            <?php endif; ?>
                                            <?php if($modelo['busto'] && $modelo['quadril']): ?>
                                                <span>•</span>
                                                <span><?php echo $modelo['busto']; ?>/<?php echo $modelo['quadril']; ?>cm</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if($modelo['experiencia']): ?>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-1">Experiência</p>
                                    <p class="text-sm text-gray-700 line-clamp-2">
                                        <?php echo htmlspecialchars(substr($modelo['experiencia'], 0, 100)); ?>
                                        <?php if(strlen($modelo['experiencia']) > 100): ?>...<?php endif; ?>
                                    </p>
                                </div>
                                <?php endif; ?>

                                <!-- Ações -->
                                <div class="flex justify-between pt-4 border-t border-gray-200">
                                    <?php if($modelo['status'] == 'pendente'): ?>
                                        <button onclick="aprovarModelo(<?php echo $modelo['id']; ?>)" 
                                                class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 text-sm flex items-center">
                                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                                            Aprovar
                                        </button>
                                        <button onclick="rejeitarModelo(<?php echo $modelo['id']; ?>)" 
                                                class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 text-sm flex items-center">
                                            <i data-feather="x" class="w-3 h-3 mr-1"></i>
                                            Rejeitar
                                        </button>
                                    <?php else: ?>
                                        <a href="../modelo_perfil.php?id=<?php echo $modelo['id']; ?>" 
                                           target="_blank"
                                           class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm flex items-center">
                                            <i data-feather="eye" class="w-3 h-3 mr-1"></i>
                                            Ver Perfil
                                        </a>
                                        <button onclick="excluirModelo(<?php echo $modelo['id']; ?>, '<?php echo htmlspecialchars($modelo['nome']); ?>')" 
                                                class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 text-sm flex items-center">
                                            <i data-feather="trash-2" class="w-3 h-3 mr-1"></i>
                                            Excluir
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                        <i data-feather="user-x" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">Nenhum modelo encontrado</h3>
                        <p class="text-gray-600">Tente ajustar os filtros de busca</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Paginação -->
                <?php if($total_paginas > 1): ?>
                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Mostrando 
                            <span class="font-medium"><?php echo ($offset + 1); ?></span>
                            a 
                            <span class="font-medium"><?php echo min($offset + $limit, $total_modelos); ?></span>
                            de 
                            <span class="font-medium"><?php echo $total_modelos; ?></span>
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
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        function aprovarModelo(id) {
            if (confirm('Deseja aprovar este modelo?\n\nEle ficará visível no casting.')) {
                window.location.href = 'admin_modelos.php?action=approve&id=' + id;
            }
        }
        
        function rejeitarModelo(id) {
            if (confirm('Deseja rejeitar este modelo?\n\nEle não ficará visível no casting.')) {
                window.location.href = 'admin_modelos.php?action=reject&id=' + id;
            }
        }
        
        function excluirModelo(id, nome) {
            if (confirm('Tem certeza que deseja excluir o modelo "' + nome + '"?\n\nEsta ação não pode ser desfeita!')) {
                window.location.href = 'admin_modelos.php?action=delete&id=' + id;
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