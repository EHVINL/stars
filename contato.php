<?php
require_once 'includes/config.php';

// Processar formulário de contato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);
    
    $erro = '';
    $sucesso = '';
    
    // Validações
    if (!$nome || !$email || !$assunto || !$mensagem) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!$email) {
        $erro = "Por favor, insira um e-mail válido.";
    } else {
        try {
            // Salvar no banco de dados
            $stmt = $pdo->prepare("INSERT INTO contatos (nome, email, telefone, assunto, mensagem, status) VALUES (?, ?, ?, ?, ?, 'pendente')");
            $stmt->execute([$nome, $email, $telefone, $assunto, $mensagem]);
            
            $sucesso = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
            
            // Limpar formulário
            $nome = $email = $telefone = $assunto = $mensagem = '';
            
        } catch (PDOException $e) {
            $erro = "Erro ao enviar mensagem. Tente novamente.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="pt-32 pb-20 bg-gradient-to-br from-black via-purple-900 to-pink-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white">
                Entre em <span class="gradient-text">Contato</span>
            </h1>
            <p class="text-xl text-purple-200 max-w-3xl mx-auto">
                Estamos aqui para ajudar. Envie sua mensagem e retornaremos o mais breve possível.
            </p>
        </div>
    </div>
</section>

<!-- Conteúdo Principal -->
<section class="py-12 bg-gradient-to-b from-purple-900/20 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Formulário de Contato -->
            <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-8 border border-purple-500/20" data-aos="fade-right">
                <h2 class="text-2xl font-bold text-white mb-6">Envie sua Mensagem</h2>
                
                <?php if(isset($erro)): ?>
                <div class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6">
                    <?php echo $erro; ?>
                </div>
                <?php endif; ?>
                
                <?php if(isset($sucesso)): ?>
                <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6">
                    <?php echo $sucesso; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-purple-300 mb-2">
                                Nome Completo <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" 
                                   class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-purple-300 mb-2">
                                E-mail <span class="text-red-400">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                   class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-purple-300 mb-2">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo isset($telefone) ? htmlspecialchars($telefone) : ''; ?>" 
                               class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="assunto" class="block text-sm font-medium text-purple-300 mb-2">
                            Assunto <span class="text-red-400">*</span>
                        </label>
                        <select id="assunto" name="assunto" 
                                class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                required>
                            <option value="">Selecione um assunto</option>
                            <option value="suporte" <?php echo (isset($assunto) && $assunto == 'suporte') ? 'selected' : ''; ?>>Suporte Técnico</option>
                            <option value="vendas" <?php echo (isset($assunto) && $assunto == 'vendas') ? 'selected' : ''; ?>>Dúvidas sobre Vendas</option>
                            <option value="parceria" <?php echo (isset($assunto) && $assunto == 'parceria') ? 'selected' : ''; ?>>Proposta de Parceria</option>
                            <option value="reclamacao" <?php echo (isset($assunto) && $assunto == 'reclamacao') ? 'selected' : ''; ?>>Reclamação</option>
                            <option value="sugestao" <?php echo (isset($assunto) && $assunto == 'sugestao') ? 'selected' : ''; ?>>Sugestão</option>
                            <option value="outro" <?php echo (isset($assunto) && $assunto == 'outro') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="mensagem" class="block text-sm font-medium text-purple-300 mb-2">
                            Mensagem <span class="text-red-400">*</span>
                        </label>
                        <textarea id="mensagem" name="mensagem" rows="6"
                                  class="w-full px-4 py-3 bg-black/50 border border-purple-500/30 rounded-lg text-white placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  required><?php echo isset($mensagem) ? htmlspecialchars($mensagem) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-4 rounded-lg transition duration-300 font-bold flex items-center justify-center space-x-2">
                        <i data-feather="send" class="w-5 h-5"></i>
                        <span>Enviar Mensagem</span>
                    </button>
                </form>
            </div>
            
            <!-- Informações de Contato -->
            <div class="space-y-8" data-aos="fade-left">
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-8 border border-purple-500/20">
                    <h2 class="text-2xl font-bold text-white mb-6">Informações de Contato</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="map-pin" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Endereço</h3>
                                <p class="text-purple-300">
                                    Rua Exemplo, 123<br>
                                    Centro, São Paulo - SP<br>
                                    CEP: 01234-567
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="phone" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Telefone</h3>
                                <p class="text-purple-300">
                                    (11) 9999-9999<br>
                                    (11) 8888-8888
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="mail" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">E-mail</h3>
                                <p class="text-purple-300">
                                    contato@starsmodels.com<br>
                                    suporte@starsmodels.com
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="clock" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-1">Horário de Atendimento</h3>
                                <p class="text-purple-300">
                                    Segunda a Sexta: 8h às 18h<br>
                                    Sábado: 8h às 12h
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Rápido -->
                <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-8 border border-purple-500/20">
                    <h2 class="text-2xl font-bold text-white mb-6">Perguntas Frequentes</h2>
                    
                    <div class="space-y-4">
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center cursor-pointer p-4 bg-black/30 rounded-lg hover:bg-black/40 transition duration-300">
                                <span class="text-white font-medium">Qual o prazo para resposta?</span>
                                <i data-feather="chevron-down" class="w-5 h-5 text-purple-400 transition-transform duration-300"></i>
                            </div>
                            <div class="faq-answer text-purple-300 p-4 hidden">
                                Respondemos todas as mensagens em até 24 horas úteis.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center cursor-pointer p-4 bg-black/30 rounded-lg hover:bg-black/40 transition duration-300">
                                <span class="text-white font-medium">Vocês atendem em todo o Brasil?</span>
                                <i data-feather="chevron-down" class="w-5 h-5 text-purple-400 transition-transform duration-300"></i>
                            </div>
                            <div class="faq-answer text-purple-300 p-4 hidden">
                                Sim, nosso atendimento é nacional através dos canais online.
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center cursor-pointer p-4 bg-black/30 rounded-lg hover:bg-black/40 transition duration-300">
                                <span class="text-white font-medium">Posso agendar uma reunião?</span>
                                <i data-feather="chevron-down" class="w-5 h-5 text-purple-400 transition-transform duration-300"></i>
                            </div>
                            <div class="faq-answer text-purple-300 p-4 hidden">
                                Claro! Entre em contato por telefone ou e-mail para agendarmos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mapa Section -->
<section class="py-12 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-white mb-4">
                Onde <span class="gradient-text">Estamos</span>
            </h2>
            <p class="text-purple-200 max-w-2xl mx-auto">
                Venha nos visitar em nosso escritório principal em São Paulo
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-gray-900 to-purple-900/30 rounded-2xl p-8 border border-purple-500/20" data-aos="fade-up">
            <div class="h-96 bg-gradient-to-br from-purple-600/20 to-pink-600/20 rounded-xl flex items-center justify-center">
                <div class="text-center text-purple-300">
                    <i data-feather="map" class="w-16 h-16 mx-auto mb-4 text-purple-400"></i>
                    <p class="text-lg font-medium">Mapa Integrado do Google Maps</p>
                    <p class="text-sm mt-2">Rua Exemplo, 123 - Centro, São Paulo - SP</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                
                // Toggle answer
                answer.classList.toggle('hidden');
                
                // Rotate icon
                if (answer.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });
        
        // Máscara para telefone
        const telefoneInput = document.getElementById('telefone');
        if (telefoneInput) {
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>