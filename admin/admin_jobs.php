<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Processar formulário de criar/editar job
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $tipo_modelo = $_POST['tipo_modelo'];
    $localizacao = trim($_POST['localizacao']);
    $status = $_POST['status'];
    $requisitos = trim($_POST['requisitos'] ?? '');
    $beneficios = trim($_POST['beneficios'] ?? '');
    
    // Validações
    if (empty($titulo) || empty($descricao) || empty($tipo_modelo) || empty($localizacao)) {
        $_SESSION['flash_message'] = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        try {
            if (isset($_POST['job_id']) && !empty($_POST['job_id'])) {
                // Editar job existente
                $job_id = (int)$_POST['job_id'];
                $stmt = $pdo->prepare("UPDATE jobs SET titulo = ?, descricao = ?, tipo_modelo = ?, localizacao = ?, status = ?, requisitos = ?, beneficios = ? WHERE id = ?");
                $stmt->execute([$titulo, $descricao, $tipo_modelo, $localizacao, $status, $requisitos, $beneficios, $job_id]);
                $_SESSION['flash_message'] = "Job atualizado com sucesso!";
            } else {
                // Criar novo job
                $stmt = $pdo->prepare("INSERT INTO jobs (titulo, descricao, tipo_modelo, localizacao, status, requisitos, beneficios, cliente_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $descricao, $tipo_modelo, $localizacao, $status, $requisitos, $beneficios, 1]); // cliente_id temporário
                $_SESSION['flash_message'] = "Job criado com sucesso!";
            }
            
            header('Location: admin_jobs.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Erro ao salvar job: " . $e->getMessage();
        }
    }
}

// Excluir Job
if (isset($_GET['delete'])) {
    $job_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        $_SESSION['flash_message'] = "Job excluído com sucesso!";
        header('Location: admin_jobs.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Erro ao excluir job: " . $e->getMessage();
    }
}

// Buscar dados para edição
$editing_job = null;
if (isset($_GET['edit'])) {
    $job_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->execute([$job_id]);
    $editing_job = $stmt->fetch();
}

// Filtros
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT j.*, u.nome as cliente_nome, u.empresa,
        (SELECT COUNT(*) FROM candidaturas WHERE job_id = j.id) as total_candidaturas
        FROM jobs j 
        LEFT JOIN usuarios u ON j.cliente_id = u.id 
        WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM jobs j WHERE 1=1";
$params = [];
$count_params = [];

if ($status_filter) {
    $sql .= " AND j.status = ?";
    $count_sql .= " AND j.status = ?";
    $params[] = $status_filter;
    $count_params[] = $status_filter;
}

if ($tipo_filter) {
    $sql .= " AND j.tipo_modelo = ?";
    $count_sql .= " AND j.tipo_modelo = ?";
    $params[] = $tipo_filter;
    $count_params[] = $tipo_filter;
}

if ($search) {
    $sql .= " AND (j.titulo LIKE ? OR j.descricao LIKE ? OR j.localizacao LIKE ?)";
    $count_sql .= " AND (j.titulo LIKE ? OR j.descricao LIKE ? OR j.localizacao LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

$sql .= " ORDER BY j.data_publicacao DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($count_params);
$total_jobs = $stmt_count->fetch()['total'];
$total_paginas = ceil($total_jobs / $limit);

// Estatísticas
$stmt_stats = $pdo->query("SELECT status, COUNT(*) as total FROM jobs GROUP BY status");
$stats = $stmt_stats->fetchAll();

// Tipos de modelo para filtro
$tipos_modelo = [
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
    <title>Gerenciar Jobs - Admin</title>
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
        
        .job-card {
            transition: all 0.3s ease;
        }
        
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        
        .status-aberto { background: #dcfce7; color: #166534; }
        .status-fechado { background: #f3f4f6; color: #374151; }
        .status-concluido { background: #dbeafe; color: #1e40af; }
        
        .type-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
        }
        
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
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
                    
                    <a href="admin_jobs.php" class="sidebar-item active flex items-center space-x-3 p-3">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Jobs</span>
                        <span class="bg-purple-500 text-xs px-2 py-1 rounded-full ml-auto"><?php echo $total_jobs; ?></span>
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
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Jobs</h1>
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
                        $color = $stat['status'] == 'aberto' ? 'bg-green-500' : 
                                ($stat['status'] == 'concluido' ? 'bg-blue-500' : 'bg-gray-500');
                    ?>
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <div class="flex items-center">
                            <div class="w-3 h-3 <?php echo $color; ?> rounded-full mr-3"></div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800"><?php echo $stat['total']; ?></div>
                                <div class="text-gray-600"><?php echo ucfirst($stat['status']); ?>s</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Header com Botão Novo Job -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Oportunidades de Trabalho</h2>
                        <p class="text-gray-600">Gerencie todas as oportunidades da plataforma</p>
                    </div>
                    <button onclick="abrirModal()" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition duration-300 flex items-center shadow-lg">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Novo Job
                    </button>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                    placeholder="Buscar jobs..." 
                                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <i data-feather="search" class="absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos os status</option>
                                <option value="aberto" <?php echo $status_filter === 'aberto' ? 'selected' : ''; ?>>Abertos</option>
                                <option value="fechado" <?php echo $status_filter === 'fechado' ? 'selected' : ''; ?>>Fechados</option>
                                <option value="concluido" <?php echo $status_filter === 'concluido' ? 'selected' : ''; ?>>Concluídos</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <select name="tipo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos os tipos</option>
                                <?php foreach($tipos_modelo as $key => $nome): ?>
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
                            <a href="admin_jobs.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Lista de Jobs -->
                <div class="space-y-6">
                    <?php if(!empty($jobs)): ?>
                        <?php foreach($jobs as $job): 
                            $status_color = $job['status'] == 'aberto' ? 'status-aberto' : 
                                          ($job['status'] == 'concluido' ? 'status-concluido' : 'status-fechado');
                            
                            $type_color = [
                                'fashion' => 'bg-pink-100 text-pink-800',
                                'comercial' => 'bg-blue-100 text-blue-800',
                                'ator' => 'bg-green-100 text-green-800',
                                'atriz' => 'bg-purple-100 text-purple-800',
                                'alta-costura' => 'bg-yellow-100 text-yellow-800',
                                'fitness' => 'bg-red-100 text-red-800',
                                'plus-size' => 'bg-indigo-100 text-indigo-800',
                                'kids' => 'bg-teal-100 text-teal-800',
                                'adolescente' => 'bg-orange-100 text-orange-800'
                            ][$job['tipo_modelo']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <div class="job-card bg-white rounded-xl p-6 border border-gray-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($job['titulo']); ?></h3>
                                        <span class="status-badge <?php echo $status_color; ?>">
                                            <?php echo ucfirst($job['status']); ?>
                                        </span>
                                        <span class="type-badge <?php echo $type_color; ?>">
                                            <?php echo $tipos_modelo[$job['tipo_modelo']] ?? $job['tipo_modelo']; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                                            <?php echo htmlspecialchars($job['localizacao']); ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                                            <?php echo date('d/m/Y', strtotime($job['data_publicacao'])); ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i data-feather="users" class="w-4 h-4 mr-2"></i>
                                            <?php echo $job['total_candidaturas']; ?> candidatura(s)
                                        </span>
                                        <?php if($job['cliente_nome']): ?>
                                        <span class="flex items-center">
                                            <i data-feather="user" class="w-4 h-4 mr-2"></i>
                                            <?php echo htmlspecialchars($job['cliente_nome']); ?>
                                            <?php if($job['empresa']): ?>
                                                <span class="ml-1 text-purple-600">(<?php echo htmlspecialchars($job['empresa']); ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2 ml-4">
                                    <button onclick="editarJob(<?php echo $job['id']; ?>)" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-300"
                                            title="Editar job">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </button>
                                    <a href="../jobs.php#job-<?php echo $job['id']; ?>" 
                                       target="_blank"
                                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition duration-300"
                                       title="Ver no site">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    <button onclick="confirmarExclusao(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars($job['titulo']); ?>')" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-300"
                                            title="Excluir job">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="prose prose-sm max-w-none mb-4">
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($job['descricao'])); ?>
                                </p>
                            </div>
                            
                            <?php if($job['requisitos']): ?>
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-800 mb-1">Requisitos:</p>
                                <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($job['requisitos'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($job['beneficios']): ?>
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-800 mb-1">Benefícios:</p>
                                <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($job['beneficios'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <span class="text-sm text-gray-500">
                                    ID: #<?php echo str_pad($job['id'], 4, '0', STR_PAD_LEFT); ?>
                                </span>
                                <div class="flex space-x-2">
                                    <?php if($job['status'] == 'aberto'): ?>
                                        <button onclick="fecharJob(<?php echo $job['id']; ?>)" 
                                                class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded transition duration-300">
                                            Fechar Vaga
                                        </button>
                                    <?php elseif($job['status'] == 'fechado'): ?>
                                        <button onclick="reabrirJob(<?php echo $job['id']; ?>)" 
                                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded transition duration-300">
                                            Reabrir Vaga
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="verCandidaturas(<?php echo $job['id']; ?>)" 
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded transition duration-300">
                                        Ver Candidaturas
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                            <i data-feather="briefcase" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900">Nenhum job encontrado</h3>
                            <p class="text-gray-600">Crie o primeiro job ou ajuste os filtros</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paginação -->
                <?php if($total_paginas > 1): ?>
                <div class="bg-white rounded-lg p-6 border border-gray-200 mt-6">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Mostrando 
                            <span class="font-medium"><?php echo ($offset + 1); ?></span>
                            a 
                            <span class="font-medium"><?php echo min($offset + $limit, $total_jobs); ?></span>
                            de 
                            <span class="font-medium"><?php echo $total_jobs; ?></span>
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

    <!-- Modal Criar/Editar Job -->
    <div id="jobModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold" id="modalTitle">
                        <?php echo $editing_job ? 'Editar Job' : 'Novo Job'; ?>
                    </h3>
                    <button onclick="fecharModal()" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <form method="POST" id="jobForm" class="p-6">
                <input type="hidden" name="job_id" id="jobId" value="<?php echo $editing_job ? $editing_job['id'] : ''; ?>">
                
                <div class="space-y-6">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título do Job *</label>
                        <input type="text" name="titulo" required 
                            value="<?php echo $editing_job ? htmlspecialchars($editing_job['titulo']) : ''; ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ex: Modelo para Campanha de Verão 2024">
                    </div>
                    
                    <!-- Descrição -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrição *</label>
                        <textarea name="descricao" rows="4" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                            placeholder="Descreva detalhadamente a oportunidade, responsabilidades, etc."><?php echo $editing_job ? htmlspecialchars($editing_job['descricao']) : ''; ?></textarea>
                    </div>
                    
                    <!-- Tipo e Localização -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Modelo *</label>
                            <select name="tipo_modelo" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Selecione o tipo...</option>
                                <?php foreach($tipos_modelo as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>" 
                                        <?php echo ($editing_job && $editing_job['tipo_modelo'] === $key) ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Localização *</label>
                            <input type="text" name="localizacao" required
                                value="<?php echo $editing_job ? htmlspecialchars($editing_job['localizacao']) : ''; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Ex: São Paulo, Remoto, etc.">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="aberto" <?php echo ($editing_job && $editing_job['status'] === 'aberto') ? 'selected' : 'selected'; ?>>Aberto</option>
                            <option value="fechado" <?php echo ($editing_job && $editing_job['status'] === 'fechado') ? 'selected' : ''; ?>>Fechado</option>
                            <option value="concluido" <?php echo ($editing_job && $editing_job['status'] === 'concluido') ? 'selected' : ''; ?>>Concluído</option>
                        </select>
                    </div>
                    
                    <!-- Requisitos e Benefícios -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requisitos</label>
                            <textarea name="requisitos" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                placeholder="Requisitos específicos (altura, experiência, etc.)"><?php echo $editing_job ? htmlspecialchars($editing_job['requisitos'] ?? '') : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Benefícios</label>
                            <textarea name="beneficios" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                placeholder="Benefícios oferecidos (cache, produção, etc.)"><?php echo $editing_job ? htmlspecialchars($editing_job['beneficios'] ?? '') : ''; ?></textarea>
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
                        <?php echo $editing_job ? 'Atualizar Job' : 'Criar Job'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();
        
        function abrirModal(jobId = null) {
            const modal = document.getElementById('jobModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (jobId) {
                // Em produção, carregaria os dados via AJAX
                console.log('Editando job:', jobId);
            }
        }
        
        function fecharModal() {
            document.getElementById('jobModal').classList.add('hidden');
            document.getElementById('jobModal').classList.remove('flex');
            // Limpar formulário se necessário
        }
        
        function editarJob(id) {
            window.location.href = 'admin_jobs.php?edit=' + id;
        }
        
        function confirmarExclusao(id, titulo) {
            if (confirm('Tem certeza que deseja excluir o job "' + titulo + '"?\n\nEsta ação não pode ser desfeita!')) {
                window.location.href = 'admin_jobs.php?delete=' + id;
            }
        }
        
        function fecharJob(id) {
            if (confirm('Deseja fechar esta vaga?\n\nEla não aparecerá mais para novos candidatos.')) {
                // Em produção, faria uma requisição para atualizar o status
                alert('Vaga fechada com sucesso!');
                location.reload();
            }
        }
        
        function reabrirJob(id) {
            if (confirm('Deseja reabrir esta vaga?\n\nEla voltará a aparecer para novos candidatos.')) {
                // Em produção, faria uma requisição para atualizar o status
                alert('Vaga reaberta com sucesso!');
                location.reload();
            }
        }
        
        function verCandidaturas(id) {
            alert('Abrindo candidaturas do job ID: ' + id + '\n\nEm produção, redirecionaria para página de candidaturas.');
            // window.location.href = 'admin_candidaturas.php?job_id=' + id;
        }
        
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        
        // Fechar modal ao clicar fora
        document.getElementById('jobModal').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
        
        // Auto-abrir modal se estiver editando
        <?php if($editing_job): ?>
        document.addEventListener('DOMContentLoaded', function() {
            abrirModal();
        });
        <?php endif; ?>
    </script>
</body>
</html>