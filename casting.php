<?php
require_once 'includes/config.php';

// Filtros e busca
$search = $_GET['search'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$status_filter = 'ativo'; // Só mostra modelos ativos

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Construir query
$sql = "SELECT m.*, u.nome, u.email, u.telefone 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        WHERE m.status = ?";
$count_sql = "SELECT COUNT(*) as total 
              FROM modelos m 
              JOIN usuarios u ON m.usuario_id = u.id 
              WHERE m.status = ?";
$params = [$status_filter];
$count_params = [$status_filter];

if ($search) {
    $sql .= " AND (u.nome LIKE ? OR m.tipo_profissao LIKE ? OR m.experiencia LIKE ?)";
    $count_sql .= " AND (u.nome LIKE ? OR m.tipo_profissao LIKE ? OR m.experiencia LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

if ($tipo_filter) {
    $sql .= " AND m.tipo_profissao = ?";
    $count_sql .= " AND m.tipo_profissao = ?";
    $params[] = $tipo_filter;
    $count_params[] = $tipo_filter;
}

$sql .= " ORDER BY u.nome ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Executar queries
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $modelos = $stmt->fetchAll();

    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($count_params);
    $total_modelos = $stmt_count->fetch()['total'];
    $total_paginas = ceil($total_modelos / $limit);
} catch (PDOException $e) {
    $modelos = [];
    $total_modelos = 0;
    $total_paginas = 1;
}

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

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="pt-32 pb-20 bg-gradient-to-br from-black via-purple-900 to-pink-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white">
                Nosso <span class="gradient-text">Casting</span>
            </h1>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Descubra os talentos incríveis que fazem parte da família Stars Models. 
                Encontre o rosto perfeito para sua próxima campanha.
            </p>
        </div>
    </div>
</section>

<!-- Filtros e Busca -->
<section class="py-8 bg-gradient-to-b from-purple-900/20 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20">
            <form method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-purple-300 mb-2">Buscar Modelos</label>
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                            placeholder="Buscar por nome, tipo ou experiência..." 
                            class="w-full px-4 py-3 pl-10 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i data-feather="search" class="absolute left-3 top-3 text-purple-400"></i>
                    </div>
                </div>
                
                <div class="w-full lg:w-64">
                    <label class="block text-sm font-medium text-purple-300 mb-2">Tipo de Modelo</label>
                    <select name="tipo" class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Todos os tipos</option>
                        <?php foreach($tipos_profissao as $key => $nome): ?>
                            <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex space-x-2 w-full lg:w-auto">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition duration-300 font-medium flex items-center space-x-2">
                        <i data-feather="filter" class="w-4 h-4"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="casting.php" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition duration-300 font-medium flex items-center space-x-2">
                        <i data-feather="refresh-cw" class="w-4 h-4"></i>
                        <span>Limpar</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Lista de Modelos -->
<section class="py-12 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header com contador -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Modelos Disponíveis</h2>
                <p class="text-purple-300">
                    <?php echo $total_modelos; ?> 
                    <?php echo $total_modelos == 1 ? 'modelo encontrado' : 'modelos encontrados'; ?>
                </p>
            </div>
            
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
            <div class="mt-4 lg:mt-0">
                <a href="contato.php" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium flex items-center space-x-2">
                    <i data-feather="mail" class="w-4 h-4"></i>
                    <span>Contratar Talentos</span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($modelos)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach($modelos as $modelo): 
                // Cores baseadas no tipo de profissão
                $cores_tipo = [
                    'fashion' => 'from-pink-500 to-rose-500',
                    'comercial' => 'from-blue-500 to-cyan-500',
                    'ator' => 'from-green-500 to-emerald-500',
                    'atriz' => 'from-purple-500 to-pink-500',
                    'alta-costura' => 'from-yellow-500 to-orange-500',
                    'fitness' => 'from-red-500 to-pink-500',
                    'plus-size' => 'from-indigo-500 to-purple-500',
                    'kids' => 'from-teal-500 to-cyan-500',
                    'adolescente' => 'from-orange-500 to-red-500'
                ];
                
                $cor_tipo = $cores_tipo[$modelo['tipo_profissao']] ?? 'from-purple-500 to-pink-500';
            ?>
            <div class="model-card bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl overflow-hidden border border-purple-500/20 hover:border-purple-500/40 transition duration-300" data-aos="fade-up">
                <!-- Header com foto/avatar -->
                <div class="relative">
                    <div class="h-48 bg-gradient-to-br <?php echo $cor_tipo; ?> flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm">
                                <span class="text-2xl font-bold text-white">
                                    <?php echo strtoupper(substr($modelo['nome'], 0, 1)); ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($modelo['nome']); ?></h3>
                        </div>
                    </div>
                    
                    <!-- Badge do tipo -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-black/50 backdrop-blur-sm text-white rounded-full text-sm border border-white/20">
                            <?php echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Modelo'; ?>
                        </span>
                    </div>
                </div>

                <!-- Informações do modelo -->
                <div class="p-6">
                    <!-- Dados físicos -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <?php if($modelo['altura']): ?>
                        <div class="text-center">
                            <div class="text-sm text-purple-300 mb-1">Altura</div>
                            <div class="text-white font-semibold"><?php echo $modelo['altura']; ?>m</div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- <?php if($modelo['idade']): ?>
                        <div class="text-center">
                            <div class="text-sm text-purple-300 mb-1">Idade</div>
                            <div class="text-white font-semibold"><?php echo $modelo['idade']; ?> anos</div>
                        </div>
                        <?php endif; ?> -->
                    </div>

                    <!-- <?php if($modelo['busto'] && $modelo['cintura'] && $modelo['quadril']): ?>
                    <div class="mb-4">
                        <div class="text-sm text-purple-300 mb-2 text-center">Medidas</div>
                        <div class="flex justify-center space-x-4 text-sm text-white">
                            <span>B: <?php echo $modelo['busto']; ?>cm</span>
                            <span>C: <?php echo $modelo['cintura']; ?>cm</span>
                            <span>Q: <?php echo $modelo['quadril']; ?>cm</span>
                        </div>
                    </div>
                    <?php endif; ?> -->

                    <?php if($modelo['experiencia']): ?>
                    <div class="mb-4">
                        <div class="text-sm text-purple-300 mb-2">Experiência</div>
                        <p class="text-white text-sm line-clamp-2 leading-relaxed">
                            <?php echo htmlspecialchars(substr($modelo['experiencia'], 0, 100)); ?>
                            <?php if(strlen($modelo['experiencia']) > 100): ?>...<?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Ações -->
                    <div class="flex space-x-2">
                        <a href="modelo.php?id=<?php echo $modelo['id']; ?>" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300 text-center text-sm font-medium">
                            Ver Perfil
                        </a>
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
                        <button onclick="contactModelo(<?php echo $modelo['id']; ?>, '<?php echo htmlspecialchars($modelo['nome']); ?>')" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition duration-300 text-sm font-medium flex items-center space-x-1">
                            <i data-feather="message-circle" class="w-4 h-4"></i>
                            <span>Contatar</span>
                        </button>
                        <?php endif; ?>
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
                <i data-feather="users" class="w-16 h-16 text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Nenhum modelo encontrado</h3>
            <p class="text-purple-300 mb-6 max-w-md mx-auto">
                <?php if($search || $tipo_filter): ?>
                    Tente ajustar os filtros de busca ou 
                    <a href="casting.php" class="text-purple-400 hover:text-purple-300 underline">limpar os filtros</a>.
                <?php else: ?>
                    Nossos talentos estarão disponíveis em breve. 
                    <a href="contato.php" class="text-purple-400 hover:text-purple-300 underline">Entre em contato</a> para mais informações.
                <?php endif; ?>
            </p>
            <?php if($search || $tipo_filter): ?>
                <a href="casting.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium inline-flex items-center space-x-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span>Limpar Filtros</span>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-purple-900 via-purple-800 to-pink-900">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8" data-aos="fade-up">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-white">
            Encontrou o Talento Perfeito?
        </h2>
        
        <p class="text-xl text-purple-200 mb-8 max-w-2xl mx-auto">
            Entre em contato conosco para contratar nossos modelos ou 
            cadastre-se como talento para fazer parte do nosso casting.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="contato.php" class="bg-white text-purple-700 hover:bg-purple-100 px-8 py-4 rounded-xl transition duration-300 text-lg font-bold shadow-2xl hover:shadow-white/25 flex items-center space-x-3">
                <i data-feather="mail" class="w-5 h-5"></i>
                <span>Falar com Especialista</span>
            </a>
            
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="cadastro.php" class="bg-transparent hover:bg-white/10 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold border-2 border-white flex items-center space-x-3">
                    <i data-feather="user-plus" class="w-5 h-5"></i>
                    <span>Cadastrar como Modelo</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    function contactModelo(modeloId, modeloNome) {
        const message = `Olá! Gostaria de mais informações sobre o modelo ${modeloNome}.`;
        window.location.href = `contato.php?assunto=Interesse no modelo ${modeloNome}&mensagem=${encodeURIComponent(message)}`;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // Animar cards ao scroll
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
        
        document.querySelectorAll('.model-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>

<?php include 'includes/footer.php'; ?>