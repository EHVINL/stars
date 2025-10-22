<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Ações sobre candidaturas
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if (in_array($action, ['approve', 'reject', 'hire'])) {
        $status_map = [
            'approve' => 'aprovado',
            'reject' => 'rejeitado', 
            'hire' => 'contratado'
        ];
        
        $stmt = $pdo->prepare("UPDATE candidaturas SET status = ?, data_avaliacao = NOW(), avaliado_por = ? WHERE id = ?");
        $stmt->execute([$status_map[$action], $_SESSION['user_id'], $id]);
        
        $success = "Candidatura " . $status_map[$action] . " com sucesso!";
    }
}

// Buscar candidaturas
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT c.*, 
               m.id as modelo_id, u.nome as modelo_nome, u.email as modelo_email,
               j.titulo as job_titulo, j.cliente_id,
               cli.nome as cliente_nome, cli.empresa as cliente_empresa
        FROM candidaturas c
        JOIN modelos m ON c.modelo_id = m.id
        JOIN usuarios u ON m.usuario_id = u.id
        JOIN jobs j ON c.job_id = j.id
        LEFT JOIN usuarios cli ON j.cliente_id = cli.id
        WHERE 1=1";
$params = [];

if ($status_filter) {
    $sql .= " AND c.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $sql .= " AND (u.nome LIKE ? OR j.titulo LIKE ? OR cli.nome LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY c.data_candidatura DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidaturas = $stmt->fetchAll();

// Estatísticas
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM candidaturas GROUP BY status");
$stats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Candidaturas - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="ml-0 md:ml-64 flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                            <i data-feather="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Candidaturas</h1>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <?php if(isset($success)): ?>
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <?php foreach($stats as $stat): 
                        $color = $stat['status'] == 'pendente' ? 'bg-yellow-500' : 
                                ($stat['status'] == 'aprovado' ? 'bg-blue-500' : 
                                ($stat['status'] == 'contratado' ? 'bg-green-500' : 'bg-gray-500'));
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
                <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                placeholder="Buscar por modelo, job ou cliente..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="">Todos os status</option>
                                <option value="pendente" <?php echo $status_filter === 'pendente' ? 'selected' : ''; ?>>Pendentes</option>
                                <option value="aprovado" <?php echo $status_filter === 'aprovado' ? 'selected' : ''; ?>>Aprovados</option>
                                <option value="rejeitado" <?php echo $status_filter === 'rejeitado' ? 'selected' : ''; ?>>Rejeitados</option>
                                <option value="contratado" <?php echo $status_filter === 'contratado' ? 'selected' : ''; ?>>Contratados</option>
                            </select>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i data-feather="search" class="w-4 h-4 inline mr-2"></i>Filtrar
                        </button>
                        <a href="admin_candidaturas.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>Limpar
                        </a>
                    </form>
                </div>

                <!-- Lista de Candidaturas -->
                <div class="space-y-4">
                    <?php foreach($candidaturas as $candidatura): 
                        $status_color = $candidatura['status'] == 'pendente' ? 'bg-yellow-100 text-yellow-800' : 
                                      ($candidatura['status'] == 'aprovado' ? 'bg-blue-100 text-blue-800' : 
                                      ($candidatura['status'] == 'contratado' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'));
                    ?>
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($candidatura['modelo_nome']); ?></h3>
                                        <p class="text-gray-600">Candidatou-se para: <strong><?php echo htmlspecialchars($candidatura['job_titulo']); ?></strong></p>
                                    </div>
                                    <span class="px-3 py-1 text-sm rounded-full <?php echo $status_color; ?>">
                                        <?php echo ucfirst($candidatura['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600"><strong>Modelo:</strong> <?php echo htmlspecialchars($candidatura['modelo_nome']); ?></p>
                                        <p class="text-sm text-gray-600"><strong>Email:</strong> <?php echo htmlspecialchars($candidatura['modelo_email']); ?></p>
                                        <p class="text-sm text-gray-600"><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($candidatura['data_candidatura'])); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600"><strong>Cliente:</strong> <?php echo htmlspecialchars($candidatura['cliente_nome']); ?></p>
                                        <p class="text-sm text-gray-600"><strong>Empresa:</strong> <?php echo htmlspecialchars($candidatura['cliente_empresa']); ?></p>
                                        <p class="text-sm text-gray-600"><strong>Job:</strong> <?php echo htmlspecialchars($candidatura['job_titulo']); ?></p>
                                    </div>
                                </div>
                                
                                <?php if($candidatura['mensagem']): ?>
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-gray-800 mb-2">Mensagem do Modelo:</h4>
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($candidatura['mensagem'])); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($candidatura['status'] == 'pendente'): ?>
                            <div class="flex flex-col space-y-2 lg:w-48">
                                <a href="admin_candidaturas.php?action=approve&id=<?php echo $candidatura['id']; ?>" 
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition duration-300">
                                    <i data-feather="check" class="w-4 h-4 inline mr-2"></i>Aprovar
                                </a>
                                <a href="admin_candidaturas.php?action=reject&id=<?php echo $candidatura['id']; ?>" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-center transition duration-300">
                                    <i data-feather="x" class="w-4 h-4 inline mr-2"></i>Rejeitar
                                </a>
                                <a href="admin_candidaturas.php?action=hire&id=<?php echo $candidatura['id']; ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition duration-300">
                                    <i data-feather="user-check" class="w-4 h-4 inline mr-2"></i>Contratar
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if(empty($candidaturas)): ?>
                <div class="text-center py-12">
                    <i data-feather="file-text" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Nenhuma candidatura encontrada</h3>
                    <p class="text-gray-600">Todas as candidaturas estão processadas ou ajuste os filtros</p>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        
        // Confirmação para ações
        document.querySelectorAll('a[href*="action="]').forEach(link => {
            link.addEventListener('click', function(e) {
                const action = this.href.includes('approve') ? 'aprovar' :
                             this.href.includes('reject') ? 'rejeitar' :
                             this.href.includes('hire') ? 'contratar' : 'processar';
                
                if (!confirm(`Tem certeza que deseja ${action} esta candidatura?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>