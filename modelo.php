<?php
require_once 'includes/config.php';

// Verificar se foi passado um ID
if (!isset($_GET['id'])) {
    header('Location: casting.php');
    exit;
}

$modelo_id = (int)$_GET['id'];

// Buscar dados do modelo
try {
    $stmt = $pdo->prepare("
        SELECT m.*, u.nome, u.email, u.telefone, u.created_at as usuario_criado 
        FROM modelos m 
        JOIN usuarios u ON m.usuario_id = u.id 
        WHERE m.id = ? AND m.status = 'ativo'
    ");
    $stmt->execute([$modelo_id]);
    $modelo = $stmt->fetch();
    
    if (!$modelo) {
        $_SESSION['flash_message'] = "Modelo não encontrado ou não disponível.";
        header('Location: casting.php');
        exit;
    }
    
    // Buscar jobs que o modelo se candidatou
    $stmt = $pdo->prepare("
        SELECT j.*, c.status as candidatura_status, c.data_candidatura
        FROM candidaturas c
        JOIN jobs j ON c.job_id = j.id
        WHERE c.modelo_id = ?
        ORDER BY c.data_candidatura DESC
        LIMIT 5
    ");
    $stmt->execute([$modelo_id]);
    $candidaturas = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $_SESSION['flash_message'] = "Erro ao carregar perfil do modelo.";
    header('Location: casting.php');
    exit;
}

// Tipos de profissão
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

// Cores para os tipos
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

$cor_tipo = $cores_tipo[$modelo['tipo_profissao']] ?? 'bg-purple-500';
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section do Modelo -->
<section class="pt-32 pb-20 bg-gradient-to-br from-black via-purple-900 to-pink-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-8" data-aos="fade-up">
            <!-- Foto/Avatar do Modelo -->
            <div class="lg:w-1/3 text-center lg:text-left">
                <div class="w-64 h-64 mx-auto lg:mx-0 bg-gradient-to-br <?php echo $cor_tipo; ?> rounded-full flex items-center justify-center text-white text-6xl font-bold shadow-2xl border-4 border-white/20">
                    <?php echo strtoupper(substr($modelo['nome'], 0, 1)); ?>
                </div>
            </div>
            
            <!-- Informações Principais -->
            <div class="lg:w-2/3 text-center lg:text-left">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold text-white mb-2"><?php echo htmlspecialchars($modelo['nome']); ?></h1>
                        <div class="flex flex-wrap gap-2 justify-center lg:justify-start mb-4">
                            <span class="px-4 py-2 <?php echo $cor_tipo; ?> text-white rounded-full text-sm font-medium">
                                <?php echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Modelo'; ?>
                            </span>
                            <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-full text-sm border border-green-500/30">
                                Disponível para Trabalho
                            </span>
                        </div>
                    </div>
                    
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
                    <div class="mt-4 sm:mt-0">
                        <button onclick="openContactModal()" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium flex items-center space-x-2">
                            <i data-feather="mail" class="w-4 h-4"></i>
                            <span>Contratar Talento</span>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Dados Físicos -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <?php if($modelo['altura']): ?>
                    <div class="text-center lg:text-left">
                        <div class="text-2xl font-bold text-white"><?php echo $modelo['altura']; ?>m</div>
                        <div class="text-purple-300 text-sm">Altura</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($modelo['idade']): ?>
                    <div class="text-center lg:text-left">
                        <div class="text-2xl font-bold text-white"><?php echo $modelo['idade']; ?></div>
                        <div class="text-purple-300 text-sm">Idade</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($modelo['peso']): ?>
                    <div class="text-center lg:text-left">
                        <div class="text-2xl font-bold text-white"><?php echo $modelo['peso']; ?>kg</div>
                        <div class="text-purple-300 text-sm">Peso</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($modelo['calcado']): ?>
                    <div class="text-center lg:text-left">
                        <div class="text-2xl font-bold text-white"><?php echo $modelo['calcado']; ?></div>
                        <div class="text-purple-300 text-sm">Calçado</div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Medidas -->
                <?php if($modelo['busto'] && $modelo['cintura'] && $modelo['quadril']): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-purple-300 mb-3">Medidas</h3>
                    <div class="flex justify-center lg:justify-start space-x-6 text-white">
                        <div class="text-center">
                            <div class="text-xl font-bold"><?php echo $modelo['busto']; ?>cm</div>
                            <div class="text-purple-300 text-sm">Busto</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold"><?php echo $modelo['cintura']; ?>cm</div>
                            <div class="text-purple-300 text-sm">Cintura</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold"><?php echo $modelo['quadril']; ?>cm</div>
                            <div class="text-purple-300 text-sm">Quadril</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Conteúdo Principal -->
<section class="py-12 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Sobre o Modelo -->
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-white mb-4">Sobre</h2>
                    
                    <?php if($modelo['experiencia']): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-purple-300 mb-3">Experiência Profissional</h3>
                        <p class="text-purple-200 leading-relaxed whitespace-pre-line">
                            <?php echo htmlspecialchars($modelo['experiencia']); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($modelo['formacao']): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-purple-300 mb-3">Formação</h3>
                        <p class="text-purple-200 leading-relaxed whitespace-pre-line">
                            <?php echo htmlspecialchars($modelo['formacao']); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($modelo['habilidades']): ?>
                    <div>
                        <h3 class="text-lg font-semibold text-purple-300 mb-3">Habilidades Especiais</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $habilidades = explode(',', $modelo['habilidades']);
                            foreach($habilidades as $habilidade): 
                                $habilidade = trim($habilidade);
                                if(!empty($habilidade)):
                            ?>
                                <span class="px-3 py-1 bg-purple-600/30 text-purple-300 rounded-full text-sm border border-purple-500/30">
                                    <?php echo htmlspecialchars($habilidade); ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Portfólio/Book (Placeholder) -->
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-white mb-4">Portfólio</h2>
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="image" class="w-8 h-8 text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Book Profissional</h3>
                        <p class="text-purple-300 mb-4">
                            Portfólio disponível mediante solicitação
                        </p>
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
                            <button onclick="openContactModal()" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium">
                                Solicitar Book Completo
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg transition duration-300 font-medium inline-block">
                                Fazer Login para Solicitar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Informações de Contato -->
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                    <h3 class="text-xl font-bold text-white mb-4">Informações</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-600/30 rounded-lg flex items-center justify-center">
                                <i data-feather="mail" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-sm text-purple-300">Email</div>
                                <div class="text-white font-medium"><?php echo htmlspecialchars($modelo['email']); ?></div>
                            </div>
                        </div>
                        
                        <?php if($modelo['telefone']): ?>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-600/30 rounded-lg flex items-center justify-center">
                                <i data-feather="phone" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-sm text-purple-300">Telefone</div>
                                <div class="text-white font-medium"><?php echo htmlspecialchars($modelo['telefone']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-600/30 rounded-lg flex items-center justify-center">
                                <i data-feather="calendar" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-sm text-purple-300">Na plataforma desde</div>
                                <div class="text-white font-medium"><?php echo date('m/Y', strtotime($modelo['usuario_criado'])); ?></div>
                            </div>
                        </div>
                        
                        <?php if($modelo['cidade'] || $modelo['estado']): ?>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-600/30 rounded-lg flex items-center justify-center">
                                <i data-feather="map-pin" class="w-5 h-5 text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-sm text-purple-300">Localização</div>
                                <div class="text-white font-medium">
                                    <?php 
                                    $localizacao = [];
                                    if($modelo['cidade']) $localizacao[] = $modelo['cidade'];
                                    if($modelo['estado']) $localizacao[] = $modelo['estado'];
                                    echo htmlspecialchars(implode(', ', $localizacao));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'): ?>
                    <div class="mt-6 pt-6 border-t border-purple-500/30">
                        <button onclick="openContactModal()" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                            <i data-feather="send" class="w-4 h-4"></i>
                            <span>Enviar Proposta</span>
                        </button>
                    </div>
                    <?php elseif(!isset($_SESSION['user_id'])): ?>
                    <div class="mt-6 pt-6 border-t border-purple-500/30">
                        <a href="login.php" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 rounded-lg transition duration-300 font-medium flex items-center justify-center space-x-2">
                            <i data-feather="log-in" class="w-4 h-4"></i>
                            <span>Fazer Login para Contatar</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Estatísticas (para o próprio modelo) -->
                <?php if(isset($_SESSION['user_id']) && getModeloData($pdo, $_SESSION['user_id']) && getModeloData($pdo, $_SESSION['user_id'])['id'] == $modelo_id): ?>
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                    <h3 class="text-xl font-bold text-white mb-4">Minhas Candidaturas</h3>
                    
                    <?php if(!empty($candidaturas)): ?>
                    <div class="space-y-3">
                        <?php foreach($candidaturas as $candidatura): 
                            $status_cor = [
                                'pendente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                'aprovado' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                'rejeitado' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                'contratado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30'
                            ][$candidatura['candidatura_status']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                        ?>
                        <div class="p-3 bg-black/30 rounded-lg border border-purple-500/20">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-medium text-sm"><?php echo htmlspecialchars($candidatura['titulo']); ?></h4>
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $status_cor; ?>">
                                    <?php echo ucfirst($candidatura['candidatura_status']); ?>
                                </span>
                            </div>
                            <div class="text-xs text-purple-300">
                                <?php echo date('d/m/Y', strtotime($candidatura['data_candidatura'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <a href="jobs.php" class="block w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg transition duration-300 text-center text-sm font-medium">
                        Ver Mais Oportunidades
                    </a>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i data-feather="briefcase" class="w-8 h-8 text-purple-400 mx-auto mb-2"></i>
                        <p class="text-purple-300 text-sm">Nenhuma candidatura ainda</p>
                        <a href="jobs.php" class="text-purple-400 hover:text-purple-300 text-sm underline mt-2 inline-block">
                            Explorar oportunidades
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Compartilhar Perfil -->
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-6 border border-purple-500/20" data-aos="fade-up">
                    <h3 class="text-xl font-bold text-white mb-4">Compartilhar Perfil</h3>
                    <div class="flex space-x-2">
                        <button onclick="shareProfile()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition duration-300 text-sm font-medium flex items-center justify-center space-x-1">
                            <i data-feather="share-2" class="w-4 h-4"></i>
                            <span>Compartilhar</span>
                        </button>
                        <button onclick="printProfile()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition duration-300 text-sm font-medium">
                            <i data-feather="printer" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Contato -->
<div id="contactModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-gradient-to-br from-gray-900 to-purple-900 rounded-2xl w-full max-w-md border border-purple-500/30">
        <div class="p-6 border-b border-purple-500/30">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Contatar <?php echo htmlspecialchars($modelo['nome']); ?></h3>
                <button onclick="closeContactModal()" class="text-purple-400 hover:text-white transition duration-300">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <form action="contato.php" method="GET" class="p-6">
            <input type="hidden" name="assunto" value="Interesse no modelo <?php echo htmlspecialchars($modelo['nome']); ?>">
            <input type="hidden" name="modelo_id" value="<?php echo $modelo_id; ?>">
            
            <div class="mb-6">
                <p class="text-purple-300 text-sm mb-4">
                    Você será redirecionado para nossa página de contato para enviar uma mensagem sobre o modelo <strong><?php echo htmlspecialchars($modelo['nome']); ?></strong>.
                </p>
                
                <div class="bg-purple-600/20 border border-purple-500/30 rounded-lg p-4">
                    <h4 class="text-white font-semibold mb-2">Informações do Modelo</h4>
                    <div class="text-sm text-purple-200 space-y-1">
                        <div><strong>Nome:</strong> <?php echo htmlspecialchars($modelo['nome']); ?></div>
                        <div><strong>Tipo:</strong> <?php echo $tipos_profissao[$modelo['tipo_profissao']] ?? 'Modelo'; ?></div>
                        <?php if($modelo['altura']): ?><div><strong>Altura:</strong> <?php echo $modelo['altura']; ?>m</div><?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="closeContactModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg transition duration-300 font-medium">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-3 rounded-lg transition duration-300 font-medium">
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Navegação -->
<section class="py-8 bg-gradient-to-b from-purple-900/20 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="casting.php" class="flex items-center space-x-2 text-purple-400 hover:text-purple-300 transition duration-300">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                <span>Voltar para o Casting</span>
            </a>
            
            <div class="flex items-center space-x-4 text-sm text-purple-300">
                <span>ID: #<?php echo str_pad($modelo_id, 4, '0', STR_PAD_LEFT); ?></span>
                <span>•</span>
                <span>Perfil verificado</span>
            </div>
        </div>
    </div>
</section>

<script>
    function openContactModal() {
        document.getElementById('contactModal').classList.remove('hidden');
        document.getElementById('contactModal').classList.add('flex');
    }
    
    function closeContactModal() {
        document.getElementById('contactModal').classList.add('hidden');
        document.getElementById('contactModal').classList.remove('flex');
    }
    
    function shareProfile() {
        const profileUrl = window.location.href;
        const profileTitle = 'Stars Models - ' + '<?php echo htmlspecialchars($modelo['nome']); ?>';
        
        if (navigator.share) {
            navigator.share({
                title: profileTitle,
                text: 'Confira este talento incrível da Stars Models!',
                url: profileUrl
            });
        } else {
            // Fallback para copiar link
            navigator.clipboard.writeText(profileUrl).then(() => {
                alert('Link do perfil copiado para a área de transferência!');
            });
        }
    }
    
    function printProfile() {
        window.print();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // Fechar modal ao clicar fora
        document.getElementById('contactModal').addEventListener('click', function(e) {
            if (e.target === this) closeContactModal();
        });
        
        // Adicionar smooth scroll para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>

<style>
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
            color: black !important;
        }
        
        .bg-gradient-to-br {
            background: white !important;
            color: black !important;
        }
        
        .text-white {
            color: black !important;
        }
        
        .text-purple-200, .text-purple-300 {
            color: #666 !important;
        }
        
        .border {
            border-color: #ddd !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>