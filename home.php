<?php
require_once 'includes/config.php';

// Buscar dados para a home
try {
    // Estatísticas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'ativo'");
    $total_modelos = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'aberto'");
    $total_jobs = $stmt->fetch()['total'];
    
    // Modelos em destaque
    $stmt = $pdo->query("
        SELECT m.*, u.nome 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        WHERE m.status = 'ativo' 
        ORDER BY RAND() 
        LIMIT 6
    ");
    $modelos_destaque = $stmt->fetchAll();
    
    // Jobs recentes
    $stmt = $pdo->query("
        SELECT * FROM jobs 
        WHERE status = 'aberto' 
        ORDER BY data_publicacao DESC 
        LIMIT 3
    ");
    $jobs_recentes = $stmt->fetchAll();
    
    // Notícias recentes
    $stmt = $pdo->query("
        SELECT n.*, u.nome as autor_nome 
        FROM noticias n 
        JOIN usuarios u ON n.autor_id = u.id 
        ORDER BY n.data_publicacao DESC 
        LIMIT 3
    ");
    $noticias_recentes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_modelos = 0;
    $total_jobs = 0;
    $modelos_destaque = [];
    $jobs_recentes = [];
    $noticias_recentes = [];
}
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-bg min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/80"></div>
    
    <div class="relative z-10 text-center max-w-4xl mx-auto px-4" data-aos="fade-up">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
            Conectando 
            <span class="gradient-text">Talentos</span> 
            <br>e 
            <span class="gradient-text">Oportunidades</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-purple-200 mb-8 leading-relaxed">
            A maior plataforma de modelos do Brasil. Descubra novos talentos, 
            encontre oportunidades incríveis e faça parte da revolução da moda brasileira.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="cadastro.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold shadow-2xl hover:shadow-purple-500/25 flex items-center space-x-3">
                    <i data-feather="user-plus" class="w-5 h-5"></i>
                    <span>Começar Agora</span>
                </a>
            <?php endif; ?>
            
            <a href="casting.php" class="glass-effect hover:bg-white/20 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold border border-purple-500/30 flex items-center space-x-3">
                <i data-feather="users" class="w-5 h-5"></i>
                <span>Ver Casting</span>
            </a>
        </div>
        
        <!-- Estatísticas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-2xl mx-auto">
            <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">+<?php echo $total_modelos; ?></div>
                <div class="text-purple-300 text-sm">Modelos Ativos</div>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">+<?php echo $total_jobs; ?></div>
                <div class="text-purple-300 text-sm">Oportunidades</div>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">+500</div>
                <div class="text-purple-300 text-sm">Clientes Ativos</div>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">+10</div>
                <div class="text-purple-300 text-sm">Anos no Mercado</div>
            </div>
        </div>
    </div>
</section>

<!-- Modelos em Destaque -->
<section class="py-20 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Talentos <span class="gradient-text">Em Destaque</span>
            </h2>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Conheça alguns dos nossos modelos que estão fazendo sucesso no mercado
            </p>
        </div>
        
        <?php if(!empty($modelos_destaque)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($modelos_destaque as $modelo): 
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
            <div class="model-card bg-gradient-to-br from-purple-900/50 to-pink-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                <div class="text-center">
                    <!-- Foto do modelo -->
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-purple-500/30 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                        <?php echo strtoupper(substr($modelo['nome'], 0, 1)); ?>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($modelo['nome']); ?></h3>
                    
                    <div class="flex justify-center items-center space-x-2 mb-4">
                        <span class="px-3 py-1 bg-purple-600/30 text-purple-300 rounded-full text-sm border border-purple-500/30">
                            <?php echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Modelo'; ?>
                        </span>
                    </div>
                    
                    <?php if($modelo['altura']): ?>
                    <div class="text-purple-300 text-sm mb-4">
                        <i data-feather="ruler" class="w-4 h-4 inline mr-2"></i>
                        Altura: <?php echo $modelo['altura']; ?>m
                    </div>
                    <?php endif; ?>
                    
                    <a href="modelo.php?id=<?php echo $modelo['id']; ?>" class="inline-flex items-center space-x-2 text-purple-300 hover:text-white transition duration-300">
                        <span>Ver Perfil Completo</span>
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="casting.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl transition duration-300 font-bold inline-flex items-center space-x-3">
                <i data-feather="users" class="w-5 h-5"></i>
                <span>Ver Todos os Modelos</span>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-12" data-aos="fade-up">
            <i data-feather="users" class="w-16 h-16 text-purple-400 mx-auto mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">Em breve!</h3>
            <p class="text-purple-300">Nossos talentos estarão disponíveis em breve.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Oportunidades Recentes -->
<section class="py-20 bg-gradient-to-b from-purple-900/20 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="gradient-text">Oportunidades</span> em Destaque
            </h2>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Confira as últimas oportunidades de trabalho disponíveis
            </p>
        </div>
        
        <?php if(!empty($jobs_recentes)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($jobs_recentes as $job): 
                $tipos_modelo = [
                    'fashion' => 'Fashion',
                    'comercial' => 'Comercial',
                    'ator' => 'Ator',
                    'atriz' => 'Atriz',
                    'alta-costura' => 'Alta Costura',
                    'fitness' => 'Fitness',
                    'plus-size' => 'Plus Size',
                    'kids' => 'Kids',
                    'adolescente' => 'Adolescente'
                ];
            ?>
            <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition duration-300" data-aos="fade-up">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-purple-600/30 text-purple-300 rounded-full text-sm border border-purple-500/30">
                        <?php echo $tipos_modelo[$job['tipo_modelo']] ?? 'Geral'; ?>
                    </span>
                    <span class="text-green-400 text-sm flex items-center">
                        <i data-feather="clock" class="w-4 h-4 mr-1"></i>
                        <?php echo date('d/m/Y', strtotime($job['data_publicacao'])); ?>
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-white mb-3"><?php echo htmlspecialchars($job['titulo']); ?></h3>
                
                <p class="text-purple-300 mb-4 line-clamp-2">
                    <?php echo strip_tags(substr($job['descricao'], 0, 120)); ?>...
                </p>
                
                <div class="flex items-center justify-between text-sm text-purple-400 mb-4">
                    <span class="flex items-center">
                        <i data-feather="map-pin" class="w-4 h-4 mr-1"></i>
                        <?php echo htmlspecialchars($job['localizacao']); ?>
                    </span>
                </div>
                
                <a href="jobs.php#job-<?php echo $job['id']; ?>" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg transition duration-300 text-center block font-medium">
                    Ver Detalhes
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="jobs.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl transition duration-300 font-bold inline-flex items-center space-x-3">
                <i data-feather="briefcase" class="w-5 h-5"></i>
                <span>Ver Todas as Oportunidades</span>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-12" data-aos="fade-up">
            <i data-feather="briefcase" class="w-16 h-16 text-purple-400 mx-auto mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">Em breve!</h3>
            <p class="text-purple-300">Novas oportunidades estarão disponíveis em breve.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Notícias Recentes -->
<section class="py-20 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Últimas <span class="gradient-text">Notícias</span>
            </h2>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Fique por dentro das novidades do mundo da moda e entretenimento
            </p>
        </div>
        
        <?php if(!empty($noticias_recentes)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($noticias_recentes as $noticia): ?>
            <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl overflow-hidden border border-purple-500/20 hover:border-purple-500/40 transition duration-300" data-aos="fade-up">
                <?php if(!empty($noticia['imagem'])): ?>
                <div class="h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <?php else: ?>
                <div class="h-48 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                    <i data-feather="file-text" class="w-12 h-12 text-white opacity-50"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 bg-purple-600/30 text-purple-300 rounded text-xs border border-purple-500/30">
                            <?php echo ucfirst($noticia['categoria'] ?? 'Geral'); ?>
                        </span>
                        <span class="text-purple-400 text-xs">
                            <?php echo date('d/m/Y', strtotime($noticia['data_publicacao'])); ?>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-3 line-clamp-2">
                        <?php echo htmlspecialchars($noticia['titulo']); ?>
                    </h3>
                    
                    <p class="text-purple-300 text-sm mb-4 line-clamp-2">
                        <?php 
                        $resumo = $noticia['resumo'] ?? strip_tags(substr($noticia['conteudo'], 0, 100));
                        echo htmlspecialchars($resumo) . '...';
                        ?>
                    </p>
                    
                    <div class="flex items-center justify-between text-sm text-purple-400">
                        <span>Por <?php echo htmlspecialchars($noticia['autor_nome']); ?></span>
                        <a href="noticias.php#noticia-<?php echo $noticia['id']; ?>" class="text-purple-300 hover:text-white transition duration-300 flex items-center space-x-1">
                            <span>Ler mais</span>
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="noticias.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-xl transition duration-300 font-bold inline-flex items-center space-x-3">
                <i data-feather="file-text" class="w-5 h-5"></i>
                <span>Ver Todas as Notícias</span>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-12" data-aos="fade-up">
            <i data-feather="file-text" class="w-16 h-16 text-purple-400 mx-auto mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">Em breve!</h3>
            <p class="text-purple-300">Novas notícias estarão disponíveis em breve.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Final -->
<section class="py-20 bg-gradient-to-br from-purple-900 via-purple-800 to-pink-900">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8" data-aos="fade-up">
        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-white">
            Pronto para Brilhar?
        </h2>
        
        <p class="text-xl text-purple-200 mb-8 max-w-2xl mx-auto">
            Junte-se à família Stars Models e descubra um mundo de oportunidades. 
            Seja você um talento em ascensão ou uma marca em busca do rosto perfeito.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="cadastro.php" class="bg-white text-purple-700 hover:bg-purple-100 px-8 py-4 rounded-xl transition duration-300 text-lg font-bold shadow-2xl hover:shadow-white/25 flex items-center space-x-3">
                    <i data-feather="user-plus" class="w-5 h-5"></i>
                    <span>Cadastrar Agora</span>
                </a>
            <?php endif; ?>
            
            <a href="contato.php" class="bg-transparent hover:bg-white/10 text-white px-8 py-4 rounded-xl transition duration-300 text-lg font-bold border-2 border-white flex items-center space-x-3">
                <i data-feather="mail" class="w-5 h-5"></i>
                <span>Falar com Especialista</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>