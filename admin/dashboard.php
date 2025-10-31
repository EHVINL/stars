<?php
// admin/dashboard.php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Buscar estatísticas do SEU banco
try {
    // Total de usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $stmt->fetch()['total'];
    
    // Modelos ativos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'ativo'");
    $total_modelos = $stmt->fetch()['total'];
    
    // Jobs abertos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'aberto'");
    $total_jobs = $stmt->fetch()['total'];
    
    // Contatos novos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contatos WHERE status = 'novo'");
    $novos_contatos = $stmt->fetch()['total'];
    
    // Modelos pendentes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'pendente'");
    $modelos_pendentes_count = $stmt->fetch()['total'];
    
    // Últimos modelos cadastrados
    $stmt = $pdo->query("
        SELECT m.*, u.nome, u.email 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        ORDER BY m.created_at DESC 
        LIMIT 5
    ");
    $ultimos_modelos = $stmt->fetchAll();

    // Últimas vagas criadas
    $stmt = $pdo->query("
        SELECT j.*, u.nome as cliente_nome 
        FROM jobs j 
        LEFT JOIN usuarios u ON j.cliente_id = u.id 
        ORDER BY j.data_publicacao DESC 
        LIMIT 5
    ");
    $ultimas_vagas = $stmt->fetchAll();

    // Modelos pendentes
    $stmt = $pdo->query("
        SELECT m.*, u.nome 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        WHERE m.status = 'pendente' 
        ORDER BY m.created_at DESC
    ");
    $modelos_pendentes = $stmt->fetchAll();

    // Tickets abertos
    $stmt = $pdo->query("
        SELECT COUNT(*) as total FROM tickets_suporte WHERE status = 'aberto'
    ");
    $tickets_abertos = $stmt->fetch()['total'];

} catch (PDOException $e) {
    // Em caso de erro, define valores padrão
    $total_usuarios = $total_modelos = $total_jobs = $novos_contatos = $modelos_pendentes_count = $tickets_abertos = 0;
    $ultimos_modelos = $ultimas_vagas = $modelos_pendentes = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Stars Models</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }
        .stat-card {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        }
    </style>
</head>
<body class="bg-gray-100">
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
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 bg-purple-600 rounded-lg">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="modelos.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Gerenciar Modelos</span>
                    </a>
                    <a href="jobs.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Gerenciar Vagas</span>
                    </a>
                    <a href="contatos.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="mail" class="w-5 h-5"></i>
                        <span>Contatos</span>
                    </a>
                    <a href="../logout.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="log-out" class="w-5 h-5"></i>
                        <span>Sair</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center p-6">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Administrativo</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Olá, <?php echo getUserName(); ?></span>
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            <?php echo strtoupper(substr(getUserName(), 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="stat-card text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-200 text-sm">Total de Usuários</p>
                            <p class="text-3xl font-bold"><?php echo $total_usuarios; ?></p>
                        </div>
                        <i data-feather="users" class="w-8 h-8 text-white opacity-80"></i>
                    </div>
                </div>

                <div class="stat-card text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-200 text-sm">Modelos Ativos</p>
                            <p class="text-3xl font-bold"><?php echo $total_modelos; ?></p>
                        </div>
                        <i data-feather="user-check" class="w-8 h-8 text-white opacity-80"></i>
                    </div>
                </div>

                <div class="stat-card text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-200 text-sm">Vagas Abertas</p>
                            <p class="text-3xl font-bold"><?php echo $total_jobs; ?></p>
                        </div>
                        <i data-feather="briefcase" class="w-8 h-8 text-white opacity-80"></i>
                    </div>
                </div>

                <div class="stat-card text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-200 text-sm">Contatos Novos</p>
                            <p class="text-3xl font-bold"><?php echo $novos_contatos; ?></p>
                        </div>
                        <i data-feather="mail" class="w-8 h-8 text-white opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Modelos Pendentes -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Modelos Pendentes</h2>
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                            <?php echo $modelos_pendentes_count; ?> pendentes
                        </span>
                    </div>
                    
                    <?php if (!empty($modelos_pendentes)): ?>
                        <div class="space-y-4">
                            <?php foreach ($modelos_pendentes as $modelo): ?>
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                        <?php echo strtoupper(substr($modelo['nome'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800"><?php echo $modelo['nome']; ?></p>
                                        <p class="text-sm text-gray-600">
                                            <?php 
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
                                            echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Sem tipo definido'; 
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="aprovarModelo(<?php echo $modelo['id']; ?>)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-300">
                                        Aprovar
                                    </button>
                                    <button onclick="rejeitarModelo(<?php echo $modelo['id']; ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition duration-300">
                                        Rejeitar
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-feather="check-circle" class="w-12 h-12 mx-auto mb-3 text-green-400"></i>
                            <p>Nenhum modelo pendente de aprovação</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Últimas Vagas -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Últimas Vagas</h2>
                    
                    <?php if (!empty($ultimas_vagas)): ?>
                        <div class="space-y-4">
                            <?php foreach ($ultimas_vagas as $vaga): ?>
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-800"><?php echo $vaga['titulo']; ?></h3>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                        <?php echo ucfirst($vaga['status']); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2"><?php echo $vaga['cliente_nome'] ?? 'N/A'; ?></p>
                                <p class="text-xs text-gray-500">
                                    Publicado em <?php echo date('d/m/Y', strtotime($vaga['data_publicacao'])); ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-feather="briefcase" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                            <p>Nenhuma vaga criada recentemente</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Últimos Modelos -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Últimos Modelos Cadastrados</h2>
                    
                    <?php if (!empty($ultimos_modelos)): ?>
                        <div class="space-y-4">
                            <?php foreach ($ultimos_modelos as $modelo): ?>
                            <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                                    <?php echo strtoupper(substr($modelo['nome'], 0, 1)); ?>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800"><?php echo $modelo['nome']; ?></p>
                                    <p class="text-sm text-gray-600"><?php echo $modelo['email']; ?></p>
                                </div>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-medium">
                                    <?php echo ucfirst($modelo['status']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i data-feather="users" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                            <p>Nenhum modelo cadastrado recentemente</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ações Rápidas -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Ações Rápidas</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="modelos.php?action=add" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition duration-300">
                            <i data-feather="user-plus" class="w-6 h-6 mx-auto mb-2"></i>
                            <p class="font-medium">Adicionar Modelo</p>
                        </a>
                        <a href="jobs.php?action=add" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition duration-300">
                            <i data-feather="plus" class="w-6 h-6 mx-auto mb-2"></i>
                            <p class="font-medium">Criar Vaga</p>
                        </a>
                        <a href="modelos.php" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition duration-300">
                            <i data-feather="users" class="w-6 h-6 mx-auto mb-2"></i>
                            <p class="font-medium">Ver Todos Modelos</p>
                        </a>
                        <a href="jobs.php" class="bg-orange-600 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition duration-300">
                            <i data-feather="briefcase" class="w-6 h-6 mx-auto mb-2"></i>
                            <p class="font-medium">Ver Todas Vagas</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace();
        
        function aprovarModelo(modeloId) {
            if (confirm('Deseja aprovar este modelo?')) {
                fetch(`actions/aprovar_modelo.php?id=${modeloId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao aprovar modelo');
                        }
                    });
            }
        }
        
        function rejeitarModelo(modeloId) {
            if (confirm('Deseja rejeitar este modelo?')) {
                fetch(`actions/rejeitar_modelo.php?id=${modeloId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro ao rejeitar modelo');
                        }
                    });
            }
        }
    </script>
</body>
</html>