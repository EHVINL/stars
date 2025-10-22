<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Processar ações
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    try {
        switch ($action) {
            case 'marcar_respondido':
                $stmt = $pdo->prepare("UPDATE contatos SET status = 'respondido' WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Mensagem marcada como respondida!";
                break;
                
            case 'marcar_novo':
                $stmt = $pdo->prepare("UPDATE contatos SET status = 'novo' WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Mensagem marcada como nova!";
                break;
                
            case 'fechar':
                $stmt = $pdo->prepare("UPDATE contatos SET status = 'fechado' WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Mensagem fechada!";
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM contatos WHERE id = ?");
                $stmt->execute([$id]);
                $success = "Mensagem excluída com sucesso!";
                break;
        }
    } catch (PDOException $e) {
        $error = "Erro ao processar ação: " . $e->getMessage();
    }
}

// Processar resposta em lote
if (isset($_POST['acao_lote']) && isset($_POST['mensagens_selecionadas'])) {
    $ids = $_POST['mensagens_selecionadas'];
    $acao = $_POST['acao_lote'];
    
    if (!empty($ids)) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        try {
            switch ($acao) {
                case 'marcar_respondido':
                    $stmt = $pdo->prepare("UPDATE contatos SET status = 'respondido' WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                    $success = count($ids) . " mensagem(s) marcada(s) como respondida(s)!";
                    break;
                    
                case 'marcar_novo':
                    $stmt = $pdo->prepare("UPDATE contatos SET status = 'novo' WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                    $success = count($ids) . " mensagem(s) marcada(s) como nova(s)!";
                    break;
                    
                case 'fechar':
                    $stmt = $pdo->prepare("UPDATE contatos SET status = 'fechado' WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                    $success = count($ids) . " mensagem(s) fechada(s)!";
                    break;
                    
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM contatos WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                    $success = count($ids) . " mensagem(s) excluída(s)!";
                    break;
            }
        } catch (PDOException $e) {
            $error = "Erro ao processar ação em lote: " . $e->getMessage();
        }
    }
}

// Filtros
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

// Construir query
$sql = "SELECT * FROM contatos WHERE 1=1";
$params = [];

if ($status_filter) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $sql .= " AND (nome LIKE ? OR email LIKE ? OR assunto LIKE ? OR mensagem LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($data_inicio) {
    $sql .= " AND DATE(data_contato) >= ?";
    $params[] = $data_inicio;
}

if ($data_fim) {
    $sql .= " AND DATE(data_contato) <= ?";
    $params[] = $data_fim;
}

$sql .= " ORDER BY 
    CASE WHEN status = 'novo' THEN 1 ELSE 2 END,
    data_contato DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contatos = $stmt->fetchAll();

// Estatísticas
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM contatos GROUP BY status");
$stats = $stmt->fetchAll();

// Total de mensagens
$total_mensagens = count($contatos);
$novas_mensagens = array_reduce($stats, function($carry, $item) {
    return $item['status'] === 'novo' ? $item['total'] : $carry;
}, 0);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gerenciar Mensagens - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .mensagem-nova {
            border-left: 4px solid #ef4444;
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.05) 0%, transparent 100%);
        }
        .mensagem-respondida {
            border-left: 4px solid #10b981;
        }
        .mensagem-fechada {
            border-left: 4px solid #6b7280;
        }
        .checkbox-lote:checked + div {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="ml-64 flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                            <i data-feather="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-800 ml-4">Gerenciar Mensagens</h1>
                        <?php if($novas_mensagens > 0): ?>
                        <span class="ml-3 bg-red-500 text-white text-sm px-2 py-1 rounded-full animate-pulse">
                            <?php echo $novas_mensagens; ?> nova(s)
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Mensagens de Status -->
                <?php if(isset($success)): ?>
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                        <?php echo $success; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i data-feather="alert-circle" class="w-5 h-5 mr-3"></i>
                        <?php echo $error; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <?php foreach($stats as $stat): 
                        $color = $stat['status'] == 'novo' ? 'bg-red-500' : 
                                ($stat['status'] == 'respondido' ? 'bg-green-500' : 'bg-gray-500');
                        $text_color = $stat['status'] == 'novo' ? 'text-red-600' : 
                                    ($stat['status'] == 'respondido' ? 'text-green-600' : 'text-gray-600');
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
                    
                    <div class="bg-white p-4 rounded-lg shadow-sm border">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800"><?php echo $total_mensagens; ?></div>
                                <div class="text-gray-600">Total</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros e Ações em Lote -->
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Busca -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                    placeholder="Nome, email, assunto..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Todos os status</option>
                                    <option value="novo" <?php echo $status_filter === 'novo' ? 'selected' : ''; ?>>Novas</option>
                                    <option value="respondido" <?php echo $status_filter === 'respondido' ? 'selected' : ''; ?>>Respondidas</option>
                                    <option value="fechado" <?php echo $status_filter === 'fechado' ? 'selected' : ''; ?>>Fechadas</option>
                                </select>
                            </div>

                            <!-- Data Início -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                                <input type="date" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <!-- Data Fim -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                                <input type="date" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center">
                                <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                                Aplicar Filtros
                            </button>
                            
                            <a href="admin_contatos.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Limpar
                            </a>
                            
                            <button type="button" onclick="exportarMensagens()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                                <i data-feather="download" class="w-4 h-4 mr-2"></i>
                                Exportar
                            </button>
                        </div>
                    </form>

                    <!-- Ações em Lote -->
                    <?php if($total_mensagens > 0): ?>
                    <form method="POST" id="formLote" class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="selecionar_todos" class="checkbox-lote hidden">
                                    <label for="selecionar_todos" class="cursor-pointer">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center transition duration-200">
                                            <i data-feather="check" class="w-3 h-3 text-white hidden"></i>
                                        </div>
                                    </label>
                                    <span class="ml-2 text-sm text-gray-700">Selecionar todas</span>
                                </div>
                                
                                <select name="acao_lote" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                                    <option value="">Ação em lote...</option>
                                    <option value="marcar_respondido">Marcar como Respondido</option>
                                    <option value="marcar_novo">Marcar como Novo</option>
                                    <option value="fechar">Fechar Mensagem</option>
                                    <option value="delete">Excluir Mensagens</option>
                                </select>
                                
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                    Aplicar
                                </button>
                            </div>
                            
                            <div class="text-sm text-gray-600">
                                <?php echo $total_mensagens; ?> mensagem(ens) encontrada(s)
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Lista de Mensagens -->
                <div class="space-y-4">
                    <?php foreach($contatos as $contato): 
                        $status_class = $contato['status'] == 'novo' ? 'mensagem-nova' : 
                                      ($contato['status'] == 'respondido' ? 'mensagem-respondida' : 'mensagem-fechada');
                    ?>
                    <div class="bg-white rounded-lg shadow-sm border <?php echo $status_class; ?>">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($contato['nome']); ?></h3>
                                        
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php echo $contato['status'] == 'novo' ? 'bg-red-100 text-red-800' : 
                                                  ($contato['status'] == 'respondido' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo ucfirst($contato['status']); ?>
                                        </span>
                                        
                                        <?php if($contato['status'] == 'novo'): ?>
                                        <span class="px-2 py-1 text-xs bg-red-500 text-white rounded-full animate-pulse">
                                            NOVA
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <i data-feather="mail" class="w-4 h-4 mr-1"></i>
                                            <?php echo htmlspecialchars($contato['email']); ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i data-feather="calendar" class="w-4 h-4 mr-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($contato['data_contato'])); ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i data-feather="clock" class="w-4 h-4 mr-1"></i>
                                            <?php 
                                            $data_contato = new DateTime($contato['data_contato']);
                                            $agora = new DateTime();
                                            $diferenca = $agora->diff($data_contato);
                                            
                                            if ($diferenca->d > 0) {
                                                echo $diferenca->d . ' dia(s) atrás';
                                            } elseif ($diferenca->h > 0) {
                                                echo $diferenca->h . ' hora(s) atrás';
                                            } else {
                                                echo $diferenca->i . ' minuto(s) atrás';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-4">
                                    <!-- Checkbox para lote -->
                                    <input type="checkbox" name="mensagens_selecionadas[]" value="<?php echo $contato['id']; ?>" 
                                           class="checkbox-lote hidden" id="msg_<?php echo $contato['id']; ?>">
                                    <label for="msg_<?php echo $contato['id']; ?>" class="cursor-pointer">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center transition duration-200">
                                            <i data-feather="check" class="w-3 h-3 text-white hidden"></i>
                                        </div>
                                    </label>
                                    
                                    <!-- Ações individuais -->
                                    <div class="flex space-x-1">
                                        <?php if($contato['status'] == 'novo'): ?>
                                        <a href="admin_contatos.php?action=marcar_respondido&id=<?php echo $contato['id']; ?>" 
                                           class="p-2 text-green-600 hover:bg-green-50 rounded transition"
                                           title="Marcar como respondido">
                                            <i data-feather="check" class="w-4 h-4"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="admin_contatos.php?action=marcar_novo&id=<?php echo $contato['id']; ?>" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded transition"
                                           title="Marcar como nova">
                                            <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="mailto:<?php echo urlencode($contato['email']); ?>?subject=Re: <?php echo urlencode($contato['assunto']); ?>" 
                                           class="p-2 text-purple-600 hover:bg-purple-50 rounded transition"
                                           title="Responder por email">
                                            <i data-feather="reply" class="w-4 h-4"></i>
                                        </a>
                                        
                                        <a href="admin_contatos.php?action=fechar&id=<?php echo $contato['id']; ?>" 
                                           class="p-2 text-gray-600 hover:bg-gray-50 rounded transition"
                                           title="Fechar mensagem">
                                            <i data-feather="archive" class="w-4 h-4"></i>
                                        </a>
                                        
                                        <a href="admin_contatos.php?action=delete&id=<?php echo $contato['id']; ?>" 
                                           onclick="return confirm('Tem certeza que deseja excluir esta mensagem?')"
                                           class="p-2 text-red-600 hover:bg-red-50 rounded transition"
                                           title="Excluir mensagem">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($contato['assunto']): ?>
                            <div class="mb-3">
                                <strong class="text-gray-700">Assunto:</strong>
                                <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($contato['assunto']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <p class="text-gray-700 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($contato['mensagem']); ?></p>
                            </div>
                            
                            <!-- Ações Rápidas -->
                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200">
                                <a href="mailto:<?php echo urlencode($contato['email']); ?>?subject=Re: <?php echo urlencode($contato['assunto']); ?>&body=Prezado(a) <?php echo urlencode($contato['nome']); ?>," 
                                   class="inline-flex items-center px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                    <i data-feather="mail" class="w-3 h-3 mr-1"></i>
                                    Responder
                                </a>
                                
                                <button onclick="copiarEmail('<?php echo htmlspecialchars($contato['email']); ?>')" 
                                        class="inline-flex items-center px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                                    <i data-feather="copy" class="w-3 h-3 mr-1"></i>
                                    Copiar Email
                                </button>
                                
                                <button onclick="copiarMensagem(<?php echo $contato['id']; ?>)" 
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                    <i data-feather="clipboard" class="w-3 h-3 mr-1"></i>
                                    Copiar Mensagem
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if(empty($contatos)): ?>
                <div class="text-center py-12">
                    <i data-feather="inbox" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Nenhuma mensagem encontrada</h3>
                    <p class="text-gray-600">Todas as mensagens estão respondidas ou ajuste os filtros</p>
                </div>
                <?php endif; ?>

                <!-- Paginação -->
                <?php if($total_mensagens > 10): ?>
                <div class="mt-8 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Mostrando <?php echo min(10, $total_mensagens); ?> de <?php echo $total_mensagens; ?> mensagens
                    </div>
                    
                    <nav class="flex space-x-2">
                        <button class="px-3 py-2 bg-gray-800 text-gray-400 rounded-lg cursor-not-allowed">
                            <i data-feather="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="px-3 py-2 bg-purple-600 text-white rounded-lg">1</button>
                        <button class="px-3 py-2 bg-gray-800 text-gray-300 hover:bg-gray-700 rounded-lg">2</button>
                        <button class="px-3 py-2 bg-gray-800 text-gray-300 hover:bg-gray-700 rounded-lg">3</button>
                        <button class="px-3 py-2 bg-gray-800 text-gray-300 hover:bg-gray-700 rounded-lg">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </nav>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script>
        feather.replace();
        
        // Menu mobile toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('open');
        });
        
        // Selecionar todos os checkboxes
        document.getElementById('selecionar_todos').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="mensagens_selecionadas[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                atualizarCheckboxVisual(checkbox);
            });
        });
        
        // Atualizar visual dos checkboxes
        function atualizarCheckboxVisual(checkbox) {
            const label = checkbox.nextElementSibling;
            const icon = label.querySelector('i');
            
            if (checkbox.checked) {
                label.querySelector('div').classList.add('bg-purple-600', 'border-purple-600');
                icon.classList.remove('hidden');
            } else {
                label.querySelector('div').classList.remove('bg-purple-600', 'border-purple-600');
                icon.classList.add('hidden');
            }
        }
        
        // Inicializar checkboxes
        document.querySelectorAll('input[name="mensagens_selecionadas[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                atualizarCheckboxVisual(this);
                atualizarSelecionarTodos();
            });
            atualizarCheckboxVisual(checkbox);
        });
        
        function atualizarSelecionarTodos() {
            const checkboxes = document.querySelectorAll('input[name="mensagens_selecionadas[]"]');
            const selecionarTodos = document.getElementById('selecionar_todos');
            const todosSelecionados = Array.from(checkboxes).every(checkbox => checkbox.checked);
            selecionarTodos.checked = todosSelecionados;
            atualizarCheckboxVisual(selecionarTodos);
        }
        
        // Copiar email para clipboard
        function copiarEmail(email) {
            navigator.clipboard.writeText(email).then(() => {
                alert('Email copiado para a área de transferência: ' + email);
            });
        }
        
        // Copiar mensagem completa
        function copiarMensagem(id) {
            const mensagemElement = document.querySelector(`#msg_${id}`).closest('.bg-white').querySelector('.bg-gray-50 p');
            const texto = `Nome: ${mensagemElement.closest('.bg-white').querySelector('h3').textContent}\n` +
                         `Email: ${mensagemElement.closest('.bg-white').querySelector('span.flex.items-center').textContent}\n` +
                         `Assunto: ${mensagemElement.closest('.bg-white').querySelector('strong + span')?.textContent || 'Sem assunto'}\n` +
                         `Mensagem: ${mensagemElement.textContent}`;
            
            navigator.clipboard.writeText(texto).then(() => {
                alert('Mensagem copiada para a área de transferência!');
            });
        }
        
        // Exportar mensagens (simulação)
        function exportarMensagens() {
            const filtros = new URLSearchParams({
                status: '<?php echo $status_filter; ?>',
                search: '<?php echo $search; ?>',
                data_inicio: '<?php echo $data_inicio; ?>',
                data_fim: '<?php echo $data_fim; ?>'
            }).toString();
            
            alert('Exportando mensagens com filtros:\n' + 
                  'Status: <?php echo $status_filter ?: "Todos"; ?>\n' +
                  'Busca: <?php echo $search ?: "Nenhuma"; ?>\n' +
                  'Período: <?php echo $data_inicio ? $data_inicio . " a " . $data_fim : "Todos"; ?>\n\n' +
                  'Em um sistema real, isso geraria um arquivo CSV/PDF.');
            
            // Em produção: window.location.href = 'exportar_mensagens.php?' + filtros;
        }
        
        // Auto-scroll para novas mensagens
        document.addEventListener('DOMContentLoaded', function() {
            const novasMensagens = document.querySelectorAll('.mensagem-nova');
            if (novasMensagens.length > 0) {
                novasMensagens[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Destacar novas mensagens
                novasMensagens.forEach(msg => {
                    msg.style.animation = 'pulse 2s infinite';
                });
            }
        });
        
        // Validação do formulário de lote
        document.getElementById('formLote').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="mensagens_selecionadas[]"]:checked');
            const acao = document.querySelector('select[name="acao_lote"]').value;
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos uma mensagem para realizar a ação em lote.');
                return;
            }
            
            if (!acao) {
                e.preventDefault();
                alert('Selecione uma ação para realizar nas mensagens selecionadas.');
                return;
            }
            
            if (acao === 'delete') {
                if (!confirm(`Tem certeza que deseja excluir ${checkboxes.length} mensagem(ns)? Esta ação não pode ser desfeita.`)) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>