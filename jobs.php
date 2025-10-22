<?php
require_once 'includes/config.php';

// Filtros e busca
$search = $_GET['search'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$status_filter = 'aberto'; // Só mostra jobs abertos
$localizacao_filter = $_GET['localizacao'] ?? '';

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT j.*, u.nome as cliente_nome, u.empresa,
        (SELECT COUNT(*) FROM candidaturas WHERE job_id = j.id) as total_candidaturas
        FROM jobs j 
        LEFT JOIN usuarios u ON j.cliente_id = u.id 
        WHERE j.status = ?";
$count_sql = "SELECT COUNT(*) as total FROM jobs j WHERE j.status = ?";
$params = [$status_filter];
$count_params = [$status_filter];

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

if ($tipo_filter) {
    $sql .= " AND j.tipo_modelo = ?";
    $count_sql .= " AND j.tipo_modelo = ?";
    $params[] = $tipo_filter;
    $count_params[] = $tipo_filter;
}

if ($localizacao_filter) {
    $sql .= " AND j.localizacao LIKE ?";
    $count_sql .= " AND j.localizacao LIKE ?";
    $params[] = "%$localizacao_filter%";
    $count_params[] = "%$localizacao_filter%";
}

$sql .= " ORDER BY j.data_publicacao DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();

    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($count_params);
    $total_jobs = $stmt_count->fetch()['total'];
    $total_paginas = ceil($total_jobs / $limit);
} catch (PDOException $e) {
    $jobs = [];
    $total_jobs = 0;
    $total_paginas = 1;
}

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

// Processar candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidatar'])) {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = "Você precisa estar logado para se candidatar.";
        header('Location: login.php');
        exit;
    }
    
    if ($_SESSION['user_type'] !== 'modelo') {
        $_SESSION['flash_message'] = "Apenas modelos podem se candidatar a jobs.";
        header('Location: jobs.php');
        exit;
    }
    
    $job_id = (int)$_POST['job_id'];
    $mensagem = sanitize($_POST['mensagem'] ?? '');
    $modelo_id = getModeloData($pdo, $_SESSION['user_id'])['id'];
    
    try {
        // Verificar se já se candidatou
        $stmt = $pdo->prepare("SELECT id FROM candidaturas WHERE job_id = ? AND modelo_id = ?");
        $stmt->execute([$job_id, $modelo_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['flash_message'] = "Você já se candidatou a esta vaga.";
        } else {
            // Criar candidatura
            $stmt = $pdo->prepare("INSERT INTO candidaturas (job_id, modelo_id, mensagem, status) VALUES (?, ?, ?, 'pendente')");
            $stmt->execute([$job_id, $modelo_id, $mensagem]);
            $_SESSION['flash_message'] = "Candidatura enviada com sucesso!";
        }
        
        header('Location: jobs.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Erro ao processar candidatura. Tente novamente.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="pt-32 pb-20 bg-gradient-to-br from-black via-purple-900 to-pink-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white">
                <span class="gradient-text">Oportunidades</span> de Trabalho
            </h1>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Encontre as melhores oportunidades para sua carreira. 
                Campanhas, desfiles, comerciais e muito mais esperam por você.
            </p>
        </div>
    </div>
</section>

<!-- Filtros e Busca -->
<section class="py-8 bg-gradient-to-b from-purple-900/20 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20">
            <form method="GET" class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-purple-300 mb-2">Buscar Vagas</label>
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                            placeholder="Título, descrição..." 
                            class="w-full px-4 py-3 pl-10 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i data-feather="search" class="absolute left-3 top-3 text-purple-400"></i>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-purple-300 mb-2">Tipo de Modelo</label>
                    <select name="tipo" class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Todos os tipos</option>
                        <?php foreach($tipos_modelo as $key => $nome): ?>
                            <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-purple-300 mb-2">Localização</label>
                    <input type="text" name="localizacao" value="<?php echo htmlspecialchars($localizacao_filter); ?>" 
                        placeholder="Cidade, estado..." 
                        class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                        <i data-feather="filter" class="w-4 h-4"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="jobs.php" class="px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition duration-300 font-medium flex items-center justify-center">
                        <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Lista de Jobs -->
<section class="py-12 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header com contador -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Vagas em Destaque</h2>
                <p class="text-purple-300">
                    <?php echo $total_jobs; ?> 
                    <?php echo $total_jobs == 1 ? 'vaga encontrada' : 'vagas encontradas'; ?>
                </p>
            </div>
            
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
            <div class="mt-4 lg:mt-0">
                <a href="contato.php?assunto=Nova Vaga" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium flex items-center space-x-2">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    <span>Publicar Vaga</span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($jobs)): ?>
        <div class="space-y-6">
            <?php foreach($jobs as $job): 
                // Cores baseadas no tipo
                $cores_tipo = [
                    'fashion' => 'bg-pink-500',
                    'comercial' => 'bg-blue-500',
                    'ator' => 'bg-green-500',
                    'atriz' => 'bg-purple-500',
                    'alta-costura' => 'bg-yellow-500',
                    'fitness' => 'bg-red-500',
                    'plus-size' => 'bg-indigo-500',
                    'kids' => 'bg-teal-500',
                    'adolescente' => 'bg-orange-500'
                ];
                
                $cor_tipo = $cores_tipo[$job['tipo_modelo']] ?? 'bg-purple-500';
            ?>
            <div class="job-card bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition duration-300" data-aos="fade-up" id="job-<?php echo $job['id']; ?>">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <!-- Informações principais -->
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($job['titulo']); ?></h3>
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="px-3 py-1 <?php echo $cor_tipo; ?> text-white rounded-full text-sm font-medium">
                                        <?php echo $tipos_modelo[$job['tipo_modelo']] ?? 'Geral'; ?>
                                    </span>
                                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm border border-green-500/30">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                    <?php if($job['total_candidaturas'] > 0): ?>
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm border border-blue-500/30">
                                        <?php echo $job['total_candidaturas']; ?> candidatura(s)
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-lg font-bold text-white mb-1">
                                    <?php echo date('d/m/Y', strtotime($job['data_publicacao'])); ?>
                                </div>
                                <div class="text-sm text-purple-300">
                                    Publicado há <?php echo time_elapsed_string($job['data_publicacao']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descrição -->
                        <p class="text-purple-200 mb-4 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars(substr($job['descricao'], 0, 200))); ?>
                            <?php if(strlen($job['descricao']) > 200): ?>...<?php endif; ?>
                        </p>
                        
                        <!-- Detalhes -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div class="flex items-center space-x-2 text-purple-300">
                                <i data-feather="map-pin" class="w-4 h-4 text-purple-400"></i>
                                <span><?php echo htmlspecialchars($job['localizacao']); ?></span>
                            </div>
                            
                            <?php if($job['cliente_nome']): ?>
                            <div class="flex items-center space-x-2 text-purple-300">
                                <i data-feather="user" class="w-4 h-4 text-purple-400"></i>
                                <span><?php echo htmlspecialchars($job['cliente_nome']); ?></span>
                                <?php if($job['empresa']): ?>
                                    <span class="text-purple-400">(<?php echo htmlspecialchars($job['empresa']); ?>)</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center space-x-2 text-purple-300">
                                <i data-feather="clock" class="w-4 h-4 text-purple-400"></i>
                                <span>ID: #<?php echo str_pad($job['id'], 4, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                        
                        <!-- Requisitos e Benefícios -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <?php if($job['requisitos']): ?>
                            <div>
                                <h4 class="text-sm font-semibold text-purple-300 mb-2">Requisitos</h4>
                                <p class="text-sm text-purple-200 line-clamp-2">
                                    <?php echo nl2br(htmlspecialchars(substr($job['requisitos'], 0, 100))); ?>
                                    <?php if(strlen($job['requisitos']) > 100): ?>...<?php endif; ?>
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($job['beneficios']): ?>
                            <div>
                                <h4 class="text-sm font-semibold text-purple-300 mb-2">Benefícios</h4>
                                <p class="text-sm text-purple-200 line-clamp-2">
                                    <?php echo nl2br(htmlspecialchars(substr($job['beneficios'], 0, 100))); ?>
                                    <?php if(strlen($job['beneficios']) > 100): ?>...<?php endif; ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Ações -->
                    <div class="lg:w-64 flex flex-col space-y-3">
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'modelo'): ?>
                            <!-- Botão de candidatura para modelos -->
                            <button 
                                onclick="openCandidaturaModal(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars($job['titulo']); ?>')"
                                class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2"
                            >
                                <i data-feather="send" class="w-4 h-4"></i>
                                <span>Candidatar-se</span>
                            </button>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <!-- Convite para login/cadastro -->
                            <a href="login.php" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                                <i data-feather="log-in" class="w-4 h-4"></i>
                                <span>Fazer Login para Candidatar</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="contato.php?assunto=Informações sobre vaga: <?php echo urlencode($job['titulo']); ?>" 
                           class="w-full bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                            <i data-feather="info" class="w-4 h-4"></i>
                            <span>Mais Informações</span>
                        </a>
                        
                        <button onclick="shareJob(<?php echo $job['id']; ?>)" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                            <i data-feather="share-2" class="w-4 h-4"></i>
                            <span>Compartilhar</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if($total_paginas > 1): ?>
        <div class="mt-12 flex justify-center">
            <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20">
                <div class="flex items-center space-x-2">
                    <?php if($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center justify-center transition duration-300">
                            <i data-feather="chevron-left" class="w-5 h-5"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if($i == 1 || $i == $total_paginas || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="w-10 h-10 rounded-lg transition duration-300 flex items-center justify-center font-medium <?php echo $i == $page ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-800 text-purple-300 hover:bg-gray-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif($i == $page - 3 || $i == $page + 3): ?>
                            <span class="text-purple-400">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_paginas): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="w-10 h-10 bg-purple-600 hover:bg-purple-700 text-white rounded-lg flex items-center justify-center transition duration-300">
                            <i data-feather="chevron-right" class="w-5 h-5"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Estado vazio -->
        <div class="text-center py-16" data-aos="fade-up">
            <div class="w-32 h-32 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-feather="briefcase" class="w-16 h-16 text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Nenhuma vaga encontrada</h3>
            <p class="text-purple-300 mb-6 max-w-md mx-auto">
                <?php if($search || $tipo_filter || $localizacao_filter): ?>
                    Tente ajustar os filtros de busca ou 
                    <a href="jobs.php" class="text-purple-400 hover:text-purple-300 underline">limpar os filtros</a>.
                <?php else: ?>
                    Novas oportunidades estarão disponíveis em breve. 
                    <a href="contato.php" class="text-purple-400 hover:text-purple-300 underline">Entre em contato</a> para ser notificado sobre novas vagas.
                <?php endif; ?>
            </p>
            <?php if($search || $tipo_filter || $localizacao_filter): ?>
                <a href="jobs.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium inline-flex items-center space-x-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span>Limpar Filtros</span>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal de Candidatura -->
<div id="candidaturaModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-gradient-to-br from-gray-900 to-purple-900 rounded-2xl w-full max-w-md border border-purple-500/30">
        <div class="p-6 border-b border-purple-500/30">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Enviar Candidatura</h3>
                <button onclick="closeCandidaturaModal()" class="text-purple-400 hover:text-white transition duration-300">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <form method="POST" class="p-6">
            <input type="hidden" name="candidatar" value="1">
            <input type="hidden" id="modalJobId" name="job_id">
            
            <div class="mb-4">
                <h4 id="modalJobTitle" class="text-lg font-semibold text-white mb-2"></h4>
                <p class="text-purple-300 text-sm">Confirme sua candidatura para esta vaga</p>
            </div>
            
            <div class="mb-6">
                <label for="mensagem" class="block text-sm font-medium text-purple-300 mb-2">Mensagem (opcional)</label>
                <textarea 
                    id="mensagem" 
                    name="mensagem" 
                    rows="4"
                    placeholder="Conte um pouco sobre você e por que você é a pessoa ideal para esta oportunidade..."
                    class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                ></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeCandidaturaModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg transition duration-300 font-medium">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 rounded-lg transition duration-300 font-medium">
                    Confirmar Candidatura
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-purple-900 via-purple-800 to-pink-900">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-white">
            Não Encontrou o que Procurava?
        </h2>
        
        <p class="text-xl text-purple-200 mb-8 max-w-2xl mx-auto">
            Cadastre-se como modelo para receber oportunidades exclusivas ou 
            entre em contato para publicar sua vaga.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="cadastro.php" class="bg-white text-purple-700 hover:bg-purple-100 px-8 py-4 rounded-xl transition duration-300 text-lg font-bold shadow-2xl hover:shadow-white/25 flex items-center space-x-3">
                    <i data-feather="user-plus" class="w-5 h-5"></i>
                    <span>Cadastrar como Modelo</span>
                </a>
            <?php endif; ?>
            
            <a href="contato.php?assunto=Nova Vaga" class="bg-transparent hover:bg-white/10 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold border-2 border-white flex items-center space-x-3">
                <i data-feather="briefcase" class="w-5 h-5"></i>
                <span>Publicar Vaga</span>
            </a>
        </div>
    </div>
</section>

<script>
    function openCandidaturaModal(jobId, jobTitle) {
        document.getElementById('modalJobId').value = jobId;
        document.getElementById('modalJobTitle').textContent = jobTitle;
        document.getElementById('candidaturaModal').classList.remove('hidden');
        document.getElementById('candidaturaModal').classList.add('flex');
    }
    
    function closeCandidaturaModal() {
        document.getElementById('candidaturaModal').classList.add('hidden');
        document.getElementById('candidaturaModal').classList.remove('flex');
    }
    
    function shareJob(jobId) {
        const jobUrl = window.location.origin + window.location.pathname + '#job-' + jobId;
        
        if (navigator.share) {
            navigator.share({
                title: 'Stars Models - Oportunidade',
                text: 'Confira esta oportunidade incrível!',
                url: jobUrl
            });
        } else {
            // Fallback para copiar link
            navigator.clipboard.writeText(jobUrl).then(() => {
                alert('Link da vaga copiado para a área de transferência!');
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // Fechar modal ao clicar fora
        document.getElementById('candidaturaModal').addEventListener('click', function(e) {
            if (e.target === this) closeCandidaturaModal();
        });
        
        // Animar cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.job-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>

<?php 
// Função auxiliar para calcular tempo decorrido
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calcula semanas a partir dos dias
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;

    $string = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );

    foreach ($string as $k => &$v) {
        if ($k == 'w') {
            $value = $weeks;
        } elseif ($k == 'd') {
            $value = $days;
        } else {
            $value = $diff->$k;
        }

        if ($value) {
            $v = $value . ' ' . $v . ($value > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' atrás' : 'agora mesmo';
}
?>

<?php include 'includes/footer.php'; ?>