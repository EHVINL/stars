<?php
// admin/job_form.php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$job = null;
$action = 'add';
$title = 'Criar Nova Vaga';

if (isset($_GET['id']) && $_GET['action'] === 'edit') {
    $job_id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT j.*, u.nome as cliente_nome 
            FROM jobs j 
            LEFT JOIN usuarios u ON j.cliente_id = u.id 
            WHERE j.id = ?
        ");
        $stmt->execute([$job_id]);
        $job = $stmt->fetch();
        
        if ($job) {
            $action = 'edit';
            $title = 'Editar Vaga: ' . $job['titulo'];
        }
    } catch (PDOException $e) {
        // Vaga não encontrada
    }
}

// Buscar clientes
try {
    $stmt = $pdo->query("
        SELECT u.id, u.nome, u.empresa 
        FROM usuarios u 
        WHERE u.tipo = 'cliente' 
        ORDER BY u.nome
    ");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
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
    <title><?php echo $title; ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">
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
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="modelos.php" class="flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Gerenciar Modelos</span>
                    </a>
                    <a href="jobs.php" class="flex items-center space-x-3 p-3 bg-purple-600 rounded-lg">
                        <i data-feather="briefcase" class="w-5 h-5"></i>
                        <span>Gerenciar Vagas</span>
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
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center p-6">
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo $title; ?></h1>
                    <a href="jobs.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                        <i data-feather="arrow-left" class="w-4 h-4"></i>
                        <span>Voltar</span>
                    </a>
                </div>
            </header>

            <!-- Formulário -->
            <div class="p-6">
                <div class="bg-white rounded-xl shadow-sm p-6 max-w-4xl mx-auto">
                    <form action="actions/save_job.php" method="POST" class="space-y-6">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                        <?php endif; ?>
                        
                        <!-- Informações Básicas -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações da Vaga</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Título da Vaga *</label>
                                    <input type="text" name="titulo" value="<?php echo $job['titulo'] ?? ''; ?>" 
                                        required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Ex: Modelo para Campanha de Verão">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Modelo *</label>
                                    <select name="tipo_modelo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="">Selecione...</option>
                                        <?php foreach($tipos_modelo as $key => $nome): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($job['tipo_modelo'] ?? '') === $key ? 'selected' : ''; ?>>
                                                <?php echo $nome; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                                    <select name="cliente_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="">Selecionar Cliente...</option>
                                        <?php foreach($clientes as $cliente): ?>
                                            <option value="<?php echo $cliente['id']; ?>" <?php echo ($job['cliente_id'] ?? '') == $cliente['id'] ? 'selected' : ''; ?>>
                                                <?php echo $cliente['nome']; ?> (<?php echo $cliente['empresa']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Descrição e Detalhes -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Descrição e Detalhes</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição da Vaga *</label>
                                <textarea name="descricao" rows="4" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="Descreva detalhadamente a vaga, atividades, expectativas..."><?php echo $job['descricao'] ?? ''; ?></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Localização *</label>
                                    <input type="text" name="localizacao" value="<?php echo $job['localizacao'] ?? ''; ?>" 
                                        required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Ex: São Paulo, SP">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Remuneração (R$)</label>
                                    <input type="number" step="0.01" name="remuneracao" value="<?php echo $job['remuneracao'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Datas e Vagas -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Datas e Vagas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data do Evento</label>
                                    <input type="date" name="data_evento" value="<?php echo $job['data_evento'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Vagas</label>
                                    <input type="number" name="vagas" value="<?php echo $job['vagas'] ?? 1; ?>" min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="aberto" <?php echo ($job['status'] ?? 'aberto') === 'aberto' ? 'selected' : ''; ?>>Aberto</option>
                                        <option value="fechado" <?php echo ($job['status'] ?? '') === 'fechado' ? 'selected' : ''; ?>>Fechado</option>
                                        <option value="pausado" <?php echo ($job['status'] ?? '') === 'pausado' ? 'selected' : ''; ?>>Pausado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Requisitos e Benefícios -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Requisitos e Benefícios</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Requisitos</label>
                                    <textarea name="requisitos" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Requisitos específicos para a vaga..."><?php echo $job['requisitos'] ?? ''; ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Benefícios</label>
                                    <textarea name="beneficios" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                        placeholder="Benefícios oferecidos..."><?php echo $job['beneficios'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="border-t pt-6 flex justify-end space-x-4">
                            <a href="jobs.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-300">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                                <i data-feather="save" class="w-4 h-4"></i>
                                <span><?php echo $action === 'add' ? 'Criar Vaga' : 'Salvar Alterações'; ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>