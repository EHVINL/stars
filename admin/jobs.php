<?php
// admin/jobs.php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Buscar vagas com filtros
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';

$sql = "SELECT j.*, u.nome as cliente_nome, u.empresa,
        (SELECT COUNT(*) FROM candidaturas WHERE job_id = j.id) as total_candidaturas
        FROM jobs j 
        LEFT JOIN usuarios u ON j.cliente_id = u.id 
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (j.titulo LIKE ? OR j.descricao LIKE ? OR u.nome LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($status_filter) {
    $sql .= " AND j.status = ?";
    $params[] = $status_filter;
}

if ($tipo_filter) {
    $sql .= " AND j.tipo_modelo = ?";
    $params[] = $tipo_filter;
}

$sql .= " ORDER BY j.data_publicacao DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    $jobs = [];
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vagas - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000;
            color: #ffffff;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 50%, #7e22ce 100%);
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
        
        /* Cores para tema escuro */
        .bg-white {
            background-color: #1f2937 !important;
        }
        
        .bg-gray-100 {
            background-color: #111827 !important;
        }
        
        .bg-gray-50 {
            background-color: #374151 !important;
        }
        
        .text-gray-800 {
            color: #f3f4f6 !important;
        }
        
        .text-gray-700 {
            color: #d1d5db !important;
        }
        
        .text-gray-500 {
            color: #9ca3af !important;
        }
        
        .text-gray-900 {
            color: #f9fafb !important;
        }
        
        .border-gray-300 {
            border-color: #4b5563 !important;
        }
        
        .border-gray-200 {
            border-color: #374151 !important;
        }
        
        .divide-gray-200 > * + * {
            border-color: #374151 !important;
        }
        
        .hover\:bg-gray-50:hover {
            background-color: #374151 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(139, 92, 246, 0.1) !important;
        }
        
        .bg-blue-100 {
            background-color: #1e3a8a !important;
        }
        
        .text-blue-800 {
            color: #93c5fd !important;
        }
        
        .bg-green-100 {
            background-color: #065f46 !important;
        }
        
        .text-green-800 {
            color: #6ee7b7 !important;
        }
        
        .bg-red-100 {
            background-color: #7f1d1d !important;
        }
        
        .text-red-800 {
            color: #fca5a5 !important;
        }
        
        .bg-yellow-100 {
            background-color: #78350f !important;
        }
        
        .text-yellow-800 {
            color: #fcd34d !important;
        }
        
        .bg-gray-100 {
            background-color: #374151 !important;
        }
        
        .text-gray-800 {
            color: #d1d5db !important;
        }
        
        .hover\:bg-gray-700:hover {
            background-color: #4b5563 !important;
        }
        
        .bg-gray-500 {
            background-color: #6b7280 !important;
        }
        
        .hover\:bg-gray-600:hover {
            background-color: #4b5563 !important;
        }
        
        .text-blue-600 {
            color: #60a5fa !important;
        }
        
        .hover\:text-blue-900:hover {
            color: #93c5fd !important;
        }
        
        .text-green-600 {
            color: #34d399 !important;
        }
        
        .hover\:text-green-900:hover {
            color: #6ee7b7 !important;
        }
        
        .text-purple-600 {
            color: #a855f7 !important;
        }
        
        .hover\:text-purple-900:hover {
            color: #c4b5fd !important;
        }
        
        .focus\:ring-purple-500:focus {
            --tw-ring-color: rgb(139 92 246 / 0.5) !important;
        }
        
        .focus\:border-transparent:focus {
            border-color: transparent !important;
        }
    </style>
</head>
<body class="bg-black text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                        <i data-feather="star" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold">STARS MODELS</span>
                </div>
                
                <nav class="space-y-2">
                    <a href="admin.php" class="sidebar-item flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="modelos.php" class="sidebar-item flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Gerenciar Modelos</span>
                    </a>
                    <a href="jobs.php" class="sidebar-item active flex items-center space-x-3 p-3 bg-purple-600 rounded-lg">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Gerenciar Vagas</span>
                    </a>
                    <a href="../logout.php" class="sidebar-item flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="log-out" class="w-5 h-5"></i>
                        <span>Sair</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-gray-900 shadow-sm border-b border-gray-800">
                <div class="flex justify-between items-center p-6">
                    <h1 class="text-2xl font-bold text-white">Gerenciar Vagas</h1>
                    <a href="job_form.php?action=add" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        <span>Criar Vaga</span>
                    </a>
                </div>
            </header>

            <!-- Filtros -->
            <div class="p-6">
                <div class="bg-gray-900 rounded-xl shadow-sm p-6 mb-6 border border-gray-800">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Buscar</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                placeholder="Título, descrição..." 
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="aberto" <?php echo $status_filter === 'aberto' ? 'selected' : ''; ?>>Aberto</option>
                                <option value="fechado" <?php echo $status_filter === 'fechado' ? 'selected' : ''; ?>>Fechado</option>
                                <option value="pausado" <?php echo $status_filter === 'pausado' ? 'selected' : ''; ?>>Pausado</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Tipo</label>
                            <select name="tipo" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <?php foreach($tipos_modelo as $key => $nome): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex space-x-2 items-end">
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300">
                                Filtrar
                            </button>
                            <a href="jobs.php" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition duration-300 flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Vagas -->
                <div class="bg-gray-900 rounded-xl shadow-sm overflow-hidden border border-gray-800">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Vaga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Candidaturas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Publicação</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-900 divide-y divide-gray-800">
                                <?php if (!empty($jobs)): ?>
                                    <?php foreach ($jobs as $job): ?>
                                    <tr class="hover:bg-gray-800 transition duration-300">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-white"><?php echo $job['titulo']; ?></div>
                                            <div class="text-sm text-gray-400 truncate max-w-xs"><?php echo substr($job['descricao'], 0, 100); ?>...</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-white"><?php echo $job['cliente_nome'] ?? 'N/A'; ?></div>
                                            <div class="text-sm text-gray-400"><?php echo $job['empresa'] ?? ''; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs bg-blue-900 text-blue-300 rounded-full border border-blue-700">
                                                <?php echo $tipos_modelo[$job['tipo_modelo']] ?? 'Geral'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $status_colors = [
                                                'aberto' => 'bg-green-900 text-green-300 border-green-700',
                                                'fechado' => 'bg-red-900 text-red-300 border-red-700',
                                                'pausado' => 'bg-yellow-900 text-yellow-300 border-yellow-700'
                                            ];
                                            $color = $status_colors[$job['status']] ?? 'bg-gray-800 text-gray-400 border-gray-600';
                                            ?>
                                            <span class="px-2 py-1 text-xs <?php echo $color; ?> rounded-full border">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <?php echo $job['total_candidaturas']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo date('d/m/Y', strtotime($job['data_publicacao'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="../jobs.php#job-<?php echo $job['id']; ?>" target="_blank" class="text-blue-400 hover:text-blue-300 transition duration-300" title="Ver Vaga">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a href="job_form.php?action=edit&id=<?php echo $job['id']; ?>" class="text-green-400 hover:text-green-300 transition duration-300" title="Editar">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <button onclick="toggleJobStatus(<?php echo $job['id']; ?>, '<?php echo $job['status']; ?>')" class="text-purple-400 hover:text-purple-300 transition duration-300" title="Alterar Status">
                                                    <i data-feather="toggle-right" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <i data-feather="briefcase" class="w-12 h-12 mx-auto mb-3 text-gray-600"></i>
                                            <p class="text-gray-400">Nenhuma vaga encontrada</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace();
        
        function toggleJobStatus(jobId, currentStatus) {
            const newStatus = currentStatus === 'aberto' ? 'fechado' : 'aberto';
            const action = newStatus === 'aberto' ? 'reabrir' : 'fechar';
            
            if (confirm(`Deseja ${action} esta vaga?`)) {
                fetch(`actions/toggle_job_status.php?id=${jobId}&status=${newStatus}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao alterar status');
                        }
                    });
            }
        }
    </script>
</body>
</html>