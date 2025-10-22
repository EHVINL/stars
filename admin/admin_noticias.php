<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Processar formulário de criar/editar notícia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $conteudo = trim($_POST['conteudo']);
    $imagem = trim($_POST['imagem'] ?? '');
    $resumo = trim($_POST['resumo'] ?? '');
    $categoria = $_POST['categoria'] ?? 'geral';
    
    // Validações
    if (empty($titulo) || empty($conteudo)) {
        $_SESSION['flash_message'] = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        try {
            if (isset($_POST['noticia_id']) && !empty($_POST['noticia_id'])) {
                // Editar notícia existente
                $noticia_id = (int)$_POST['noticia_id'];
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, conteudo = ?, imagem = ?, resumo = ?, categoria = ? WHERE id = ?");
                $stmt->execute([$titulo, $conteudo, $imagem, $resumo, $categoria, $noticia_id]);
                $_SESSION['flash_message'] = "Notícia atualizada com sucesso!";
            } else {
                // Criar nova notícia
                $stmt = $pdo->prepare("INSERT INTO noticias (titulo, conteudo, imagem, resumo, categoria, autor_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $conteudo, $imagem, $resumo, $categoria, $_SESSION['user_id']]);
                $_SESSION['flash_message'] = "Notícia publicada com sucesso!";
            }
            
            header('Location: admin_noticias.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Erro ao salvar notícia: " . $e->getMessage();
        }
    }
}

// Excluir Notícia
if (isset($_GET['delete'])) {
    $noticia_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$noticia_id]);
        $_SESSION['flash_message'] = "Notícia excluída com sucesso!";
        header('Location: admin_noticias.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Erro ao excluir notícia: " . $e->getMessage();
    }
}

// Buscar dados para edição
$editing_noticia = null;
if (isset($_GET['edit'])) {
    $noticia_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$noticia_id]);
    $editing_noticia = $stmt->fetch();
}

// Filtros
$search = $_GET['search'] ?? '';
$categoria_filter = $_GET['categoria'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT n.*, u.nome as autor_nome 
        FROM noticias n 
        JOIN usuarios u ON n.autor_id = u.id 
        WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM noticias n WHERE 1=1";
$params = [];
$count_params = [];

if ($search) {
    $sql .= " AND (n.titulo LIKE ? OR n.conteudo LIKE ? OR n.resumo LIKE ?)";
    $count_sql .= " AND (n.titulo LIKE ? OR n.conteudo LIKE ? OR n.resumo LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

if ($categoria_filter) {
    $sql .= " AND n.categoria = ?";
    $count_sql .= " AND n.categoria = ?";
    $params[] = $categoria_filter;
    $count_params[] = $categoria_filter;
}

$sql .= " ORDER BY n.data_publicacao DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($count_params);
$total_noticias = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_noticias / $limit);

// Estatísticas
$stmt_stats = $pdo->query("SELECT COUNT(*) as total FROM noticias");
$total_noticias_stats = $stmt_stats->fetch()['total'];

$stmt_recentes = $pdo->query("SELECT COUNT(*) as total FROM noticias WHERE data_publicacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$noticias_recentes = $stmt_recentes->fetch()['total'];

// Categorias
$categorias = [
    'moda' => 'Moda',
    'beauty' => 'Beleza',
    'desfiles' => 'Desfiles', 
    'campanhas' => 'Campanhas',
    'entrevistas' => 'Entrevistas',
    'eventos' => 'Eventos',
    'premiacoes' => 'Premiações',
    'tendências' => 'Tendências',
    'geral' => 'Geral'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Notícias - Admin</title>
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
        
        .noticia-card {
            transition: all 0.3s ease;
        }
        
        .noticia-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .categoria-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .image-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                    
                    <a href="admin_modelos.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="user-check" class="w-5 h-5"></i>
                        <span>Modelos</span>
                    </a>
                    
                    <a href="admin_jobs.php" class="sidebar-item flex items-center space-x-3 p-3">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Jobs</span>
                    </a>
                    
                    <a href="admin_noticias.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                        <span>Notícias</span>
                        <span class="bg-purple-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $total_noticias; ?></span>
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Notícias</h1>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $total_noticias_stats; ?></div>
                        <div class="text-gray-600">Total de Notícias</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $noticias_recentes; ?></div>
                        <div class="text-gray-600">Últimos 7 Dias</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $_SESSION['user_name']; ?></div>
                        <div class="text-gray-600">Autor Logado</div>
                    </div>
                </div>

                <!-- Header com Botão Nova Notícia -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Conteúdo Publicado</h2>
                        <p class="text-gray-600">Gerencie todas as notícias e artigos</p>
                    </div>
                    <button onclick="abrirModal()" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition duration-300 flex items-center shadow-lg">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Nova Notícia
                    </button>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                    placeholder="Buscar notícias..." 
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <i data-feather="search" class="absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                            <select name="categoria" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todas as categorias</option>
                                <?php foreach($categorias as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $categoria_filter === $key ? 'selected' : ''; ?>>
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
                            <a href="admin_noticias.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Lista de Notícias -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <?php if(!empty($noticias)): ?>
                        <?php foreach($noticias as $noticia): 
                            $categoria_cor = [
                                'moda' => 'bg-pink-100 text-pink-800',
                                'beauty' => 'bg-purple-100 text-purple-800',
                                'desfiles' => 'bg-blue-100 text-blue-800',
                                'campanhas' => 'bg-green-100 text-green-800',
                                'entrevistas' => 'bg-yellow-100 text-yellow-800',
                                'eventos' => 'bg-red-100 text-red-800',
                                'premiacoes' => 'bg-indigo-100 text-indigo-800',
                                'tendências' => 'bg-teal-100 text-teal-800',
                                'geral' => 'bg-gray-100 text-gray-800'
                            ][$noticia['categoria']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <div class="noticia-card bg-white rounded-xl overflow-hidden border border-gray-200">
                            <!-- Imagem -->
                            <?php if($noticia['imagem']): ?>
                                <div class="h-48 overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($noticia['imagem']); ?>" 
                                         alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                                         class="w-full h-full object-cover hover:scale-105 transition duration-500">
                                </div>
                            <?php else: ?>
                                <div class="h-48 image-preview flex items-center justify-center">
                                    <i data-feather="image" class="w-12 h-12 text-white opacity-50"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Conteúdo -->
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="categoria-badge <?php echo $categoria_cor; ?>">
                                        <?php echo $categorias[$noticia['categoria']] ?? 'Geral'; ?>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($noticia['data_publicacao'])); ?>
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 leading-tight">
                                    <?php echo htmlspecialchars($noticia['titulo']); ?>
                                </h3>
                                
                                <?php if($noticia['resumo']): ?>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        <?php echo htmlspecialchars($noticia['resumo']); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        <?php echo strip_tags(substr($noticia['conteudo'], 0, 100)); ?>...
                                    </p>
                                <?php endif; ?>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span class="flex items-center">
                                        <i data-feather="user" class="w-3 h-3 mr-1"></i>
                                        <?php echo htmlspecialchars($noticia['autor_nome']); ?>
                                    </span>
                                    <span>
                                        <?php echo strlen($noticia['conteudo']); ?> caracteres
                                    </span>
                                </div>
                                
                                <!-- Ações -->
                                <div class="flex justify-between pt-3 border-t border-gray-200">
                                    <a href="../noticias.php#noticia-<?php echo $noticia['id']; ?>" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                                        <i data-feather="eye" class="w-3 h-3 mr-1"></i>
                                        Ver no Site
                                    </a>
                                    
                                    <div class="flex space-x-2">
                                        <button onclick="editarNoticia(<?php echo $noticia['id']; ?>)" 
                                                class="text-green-600 hover:text-green-700 p-1 rounded transition duration-300"
                                                title="Editar notícia">
                                            <i data-feather="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="confirmarExclusao(<?php echo $noticia['id']; ?>, '<?php echo htmlspecialchars($noticia['titulo']); ?>')" 
                                                class="text-red-600 hover:text-red-700 p-1 rounded transition duration-300"
                                                title="Excluir notícia">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200">
                            <i data-feather="file-text" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900">Nenhuma notícia encontrada</h3>
                            <p class="text-gray-600">Crie a primeira notícia ou ajuste os filtros</p>
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
                            <span class="font-medium"><?php echo min($offset + $limit, $total_noticias); ?></span>
                            de 
                            <span class="font-medium"><?php echo $total_noticias; ?></span>
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

    <!-- Modal Criar/Editar Notícia -->
    <div id="noticiaModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold" id="modalTitle">
                        <?php echo $editing_noticia ? 'Editar Notícia' : 'Nova Notícia'; ?>
                    </h3>
                    <button onclick="fecharModal()" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" id="noticiaForm" class="p-6">
                <input type="hidden" name="noticia_id" id="noticiaId" value="<?php echo $editing_noticia ? $editing_noticia['id'] : ''; ?>">
                
                <div class="space-y-6">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título da Notícia *</label>
                        <input type="text" name="titulo" required 
                            value="<?php echo $editing_noticia ? htmlspecialchars($editing_noticia['titulo']) : ''; ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg font-medium"
                            placeholder="Digite o título da notícia...">
                    </div>
                    
                    <!-- Resumo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Resumo</label>
                        <textarea name="resumo" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                            placeholder="Breve resumo da notícia (opcional)"><?php echo $editing_noticia ? htmlspecialchars($editing_noticia['resumo'] ?? '') : ''; ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Aparece como descrição nos cards e previews</p>
                    </div>
                    
                    <!-- Categoria e Imagem -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                            <select name="categoria" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <?php foreach($categorias as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>" 
                                        <?php echo ($editing_noticia && $editing_noticia['categoria'] === $key) ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL da Imagem</label>
                            <input type="url" name="imagem" 
                                value="<?php echo $editing_noticia ? htmlspecialchars($editing_noticia['imagem'] ?? '') : ''; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="https://exemplo.com/imagem.jpg">
                            <p class="text-sm text-gray-500 mt-1">Cole a URL de uma imagem para a notícia</p>
                        </div>
                    </div>
                    
                    <!-- Conteúdo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conteúdo *</label>
                        <textarea name="conteudo" rows="12" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                            placeholder="Escreva o conteúdo completo da notícia aqui..."><?php echo $editing_noticia ? htmlspecialchars($editing_noticia['conteudo']) : ''; ?></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-500">Use quebras de linha para parágrafos</p>
                            <span id="contador-caracteres" class="text-sm text-gray-500">0 caracteres</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                    <button type="button" onclick="fecharModal()" 
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-300 font-medium flex items-center">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i>
                        <?php echo $editing_noticia ? 'Atualizar Notícia' : 'Publicar Notícia'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();
        
        function abrirModal(noticiaId = null) {
            const modal = document.getElementById('noticiaModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (noticiaId) {
                // Em produção, carregaria os dados via AJAX
                console.log('Editando notícia:', noticiaId);
            }
        }
        
        function fecharModal() {
            document.getElementById('noticiaModal').classList.add('hidden');
            document.getElementById('noticiaModal').classList.remove('flex');
        }
        
        function editarNoticia(id) {
            window.location.href = 'admin_noticias.php?edit=' + id;
        }
        
        function confirmarExclusao(id, titulo) {
            if (confirm('Tem certeza que deseja excluir a notícia "' + titulo + '"?\n\nEsta ação não pode ser desfeita!')) {
                window.location.href = 'admin_noticias.php?delete=' + id;
            }
        }
        
        // Contador de caracteres
        const conteudoTextarea = document.getElementById('noticiaForm')?.querySelector('textarea[name="conteudo"]');
        const contador = document.getElementById('contador-caracteres');
        
        if (conteudoTextarea && contador) {
            conteudoTextarea.addEventListener('input', function() {
                const length = this.value.length;
                contador.textContent = length + ' caracteres';
                
                if (length < 100) {
                    contador.classList.add('text-red-500');
                    contador.classList.remove('text-gray-500', 'text-green-500');
                } else if (length >= 100) {
                    contador.classList.add('text-green-500');
                    contador.classList.remove('text-gray-500', 'text-red-500');
                } else {
                    contador.classList.add('text-gray-500');
                    contador.classList.remove('text-red-500', 'text-green-500');
                }
            });
            
            // Inicializar contador
            if (conteudoTextarea.value) {
                conteudoTextarea.dispatchEvent(new Event('input'));
            }
        }
        
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        
        // Fechar modal ao clicar fora
        document.getElementById('noticiaModal').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
        
        // Auto-abrir modal se estiver editando
        <?php if($editing_noticia): ?>
        document.addEventListener('DOMContentLoaded', function() {
            abrirModal();
        });
        <?php endif; ?>
        
        // Validação antes do envio
        document.getElementById('noticiaForm')?.addEventListener('submit', function(e) {
            const titulo = this.querySelector('input[name="titulo"]').value;
            const conteudo = this.querySelector('textarea[name="conteudo"]').value;
            
            if (!titulo.trim()) {
                e.preventDefault();
                alert('Por favor, preencha o título da notícia.');
                return;
            }
            
            if (!conteudo.trim() || conteudo.length < 50) {
                e.preventDefault();
                alert('O conteúdo da notícia deve ter pelo menos 50 caracteres.');
                return;
            }
        });
    </script>
</body>
</html>