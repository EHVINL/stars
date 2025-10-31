<?php
// admin/modelo_form.php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$modelo = null;
$action = 'add';
$title = 'Adicionar Modelo';

if (isset($_GET['id']) && $_GET['action'] === 'edit') {
    $modelo_id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT m.*, u.nome, u.email, u.telefone 
            FROM modelos m 
            JOIN usuarios u ON m.usuario_id = u.id 
            WHERE m.id = ?
        ");
        $stmt->execute([$modelo_id]);
        $modelo = $stmt->fetch();
        
        if ($modelo) {
            $action = 'edit';
            $title = 'Editar Modelo: ' . $modelo['nome'];
        }
    } catch (PDOException $e) {
        // Modelo não encontrado
    }
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Admin</title>
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
            border-color: #374151;
        }
        
        .bg-gray-100 {
            background-color: #111827 !important;
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
        
        .border-gray-300 {
            border-color: #4b5563 !important;
        }
        
        .border-t {
            border-color: #374151 !important;
        }
        
        .border {
            border-color: #374151 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(139, 92, 246, 0.1) !important;
        }
        
        .hover\:bg-gray-600:hover {
            background-color: #4b5563 !important;
        }
        
        .hover\:bg-gray-700:hover {
            background-color: #374151 !important;
        }
        
        .focus\:ring-purple-500:focus {
            --tw-ring-color: rgb(139 92 246 / 0.5) !important;
        }
        
        input, textarea, select {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
        }
        
        input::placeholder, textarea::placeholder {
            color: #9ca3af !important;
        }
        
        input:focus, textarea:focus, select:focus {
            background-color: #374151 !important;
            border-color: #8b5cf6 !important;
        }
    </style>
</head>
<body class="bg-black text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white">
            <div class="p-6">
                <!-- Logo MODIFICADA -->
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
                    <a href="modelos.php" class="sidebar-item active flex items-center space-x-3 p-3 bg-purple-600 rounded-lg">
                        <i data-feather="users" class="w-5 h-5"></i>
                        <span>Gerenciar Modelos</span>
                    </a>
                    <a href="jobs.php" class="sidebar-item flex items-center space-x-3 p-3 hover:bg-gray-700 rounded-lg transition duration-300">
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
                    <h1 class="text-2xl font-bold text-white"><?php echo $title; ?></h1>
                    <a href="modelos.php" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                        <i data-feather="arrow-left" class="w-4 h-4"></i>
                        <span>Voltar</span>
                    </a>
                </div>
            </header>

            <!-- Formulário -->
            <div class="p-6">
                <div class="bg-gray-900 rounded-xl shadow-sm p-6 max-w-4xl mx-auto border border-gray-800">
                    <form action="actions/save_modelo.php" method="POST" class="space-y-6">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $modelo['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informações Básicas -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-semibold text-white mb-4">Informações Básicas</h3>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nome Completo *</label>
                                <input type="text" name="nome" value="<?php echo $modelo['nome'] ?? ''; ?>" 
                                    required class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                                <input type="email" name="email" value="<?php echo $modelo['email'] ?? ''; ?>" 
                                    required class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Telefone</label>
                                <input type="tel" name="telefone" value="<?php echo $modelo['telefone'] ?? ''; ?>" 
                                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Profissão *</label>
                                <select name="tipo_profissao" required class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                    <option value="">Selecione...</option>
                                    <?php foreach($tipos_profissao as $key => $nome): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($modelo['tipo_profissao'] ?? '') === $key ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Dados Físicos -->
                        <div class="border-t border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Dados Físicos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Altura (m)</label>
                                    <input type="number" step="0.01" name="altura" value="<?php echo $modelo['altura'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Idade</label>
                                    <input type="number" name="idade" value="<?php echo $modelo['idade'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Peso (kg)</label>
                                    <input type="number" step="0.1" name="peso" value="<?php echo $modelo['peso'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Calçado</label>
                                    <input type="text" name="calcado" value="<?php echo $modelo['calcado'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Medidas -->
                        <div class="border-t border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Medidas (cm)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Busto</label>
                                    <input type="number" name="busto" value="<?php echo $modelo['busto'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Cintura</label>
                                    <input type="number" name="cintura" value="<?php echo $modelo['cintura'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Quadril</label>
                                    <input type="number" name="quadril" value="<?php echo $modelo['quadril'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="border-t border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Informações Adicionais</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Experiência Profissional</label>
                                <textarea name="experiencia" rows="4" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white"><?php echo $modelo['experiencia'] ?? ''; ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Formação</label>
                                <textarea name="formacao" rows="3" class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white"><?php echo $modelo['formacao'] ?? ''; ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Habilidades (separadas por vírgula)</label>
                                <input type="text" name="habilidades" value="<?php echo $modelo['habilidades'] ?? ''; ?>" 
                                    placeholder="Ex: Dança, Canto, Atuação..." 
                                    class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Cidade</label>
                                    <input type="text" name="cidade" value="<?php echo $modelo['cidade'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                                    <input type="text" name="estado" value="<?php echo $modelo['estado'] ?? ''; ?>" 
                                        class="w-full px-3 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-800 text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Status</h3>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="ativo" <?php echo ($modelo['status'] ?? 'ativo') === 'ativo' ? 'checked' : ''; ?> class="mr-2">
                                    <span class="text-sm text-gray-300">Ativo</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="inativo" <?php echo ($modelo['status'] ?? '') === 'inativo' ? 'checked' : ''; ?> class="mr-2">
                                    <span class="text-sm text-gray-300">Inativo</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="pendente" <?php echo ($modelo['status'] ?? '') === 'pendente' ? 'checked' : ''; ?> class="mr-2">
                                    <span class="text-sm text-gray-300">Pendente</span>
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="border-t border-gray-700 pt-6 flex justify-end space-x-4">
                            <a href="modelos.php" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-300">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                                <i data-feather="save" class="w-4 h-4"></i>
                                <span><?php echo $action === 'add' ? 'Adicionar Modelo' : 'Salvar Alterações'; ?></span>
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