<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - Central de Ajuda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #333;
        }

        .support-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .action-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }

        .action-card p {
            color: #666;
            font-size: 0.95rem;
        }

        /* FAQ Section */
        .faq-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .faq-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .faq-categories {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .faq-category {
            padding: 10px 20px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-category.active,
        .faq-category:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background: #e9ecef;
        }

        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-question::after {
            transform: rotate(45deg);
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            color: #666;
            line-height: 1.6;
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }

        /* Contact Methods */
        .contact-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .contact-method {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .method-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }

        .contact-method h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        .contact-method p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .method-button {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .method-button:hover {
            background: #5a6fd8;
        }

        /* Status System */
        .status-system {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .status-system h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .status-item {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin: 0 auto 10px;
        }

        .status-operational {
            background: #28a745;
        }

        .status-maintenance {
            background: #ffc107;
        }

        .status-outage {
            background: #dc3545;
        }

        .status-item p {
            font-weight: 600;
            color: #333;
        }

        .status-item span {
            color: #666;
            font-size: 0.9rem;
        }

        /* Knowledge Base */
        .knowledge-base {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .knowledge-base h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .kb-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .kb-category {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .kb-category:hover {
            transform: translateY(-3px);
            background: #e9ecef;
        }

        .kb-category h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .kb-category p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .kb-count {
            color: #667eea;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Search Section */
        .search-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            text-align: center;
        }

        .search-section h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 2rem;
        }

        .search-box {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #667eea;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background: #5a6fd8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 20px;
            }
            
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .faq-categories {
                justify-content: flex-start;
            }
            
            .contact-methods {
                grid-template-columns: 1fr;
            }
        }

        .support-option {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .support-option h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .support-option p {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="support-container">
        <!-- Hero Section -->
        <section class="hero-section">
            <h1>Central de Suporte</h1>
            <p>Estamos aqui para ajudar você a resolver qualquer problema</p>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <div class="action-card" onclick="scrollToSection('faq')">
                <div class="action-icon">❓</div>
                <h3>Perguntas Frequentes</h3>
                <p>Encontre respostas para as dúvidas mais comuns</p>
            </div>

            <div class="action-card" onclick="scrollToSection('contact')">
                <div class="action-icon">📞</div>
                <h3>Contato Direto</h3>
                <p>Fale diretamente com nossa equipe de suporte</p>
            </div>

            <div class="action-card" onclick="scrollToSection('status')">
                <div class="action-icon">📊</div>
                <h3>Status do Sistema</h3>
                <p>Verifique o status atual de nossos serviços</p>
            </div>

            <div class="action-card" onclick="scrollToSection('knowledge')">
                <div class="action-icon">📚</div>
                <h3>Base de Conhecimento</h3>
                <p>Acesse tutoriais e documentação completa</p>
            </div>
        </section>

        <!-- Search Section -->
        <section class="search-section">
            <h2>Como podemos ajudar?</h2>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Digite sua dúvida ou problema...">
                <button class="search-button">🔍</button>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section" id="faq">
            <h2>Perguntas Frequentes</h2>
            
            <div class="faq-categories">
                <div class="faq-category active" data-category="all">Todos</div>
                <div class="faq-category" data-category="account">Conta</div>
                <div class="faq-category" data-category="billing">Cobrança</div>
                <div class="faq-category" data-category="technical">Técnico</div>
                <div class="faq-category" data-category="general">Geral</div>
            </div>

            <div class="faq-list">
                <div class="faq-item" data-category="account">
                    <div class="faq-question">Como criar uma conta?</div>
                    <div class="faq-answer">
                        Para criar uma conta, clique em "Cadastrar" no canto superior direito do site. Preencha seus dados pessoais, confirme seu e-mail e sua conta estará pronta para uso. O processo leva menos de 2 minutos.
                    </div>
                </div>

                <div class="faq-item" data-category="account">
                    <div class="faq-question">Esqueci minha senha, o que fazer?</div>
                    <div class="faq-answer">
                        Clique em "Esqueci minha senha" na página de login. Digite seu e-mail cadastrado e enviaremos um link para redefinição. O link é válido por 24 horas.
                    </div>
                </div>

                <div class="faq-item" data-category="billing">
                    <div class="faq-question">Quais formas de pagamento são aceitas?</div>
                    <div class="faq-answer">
                        Aceitamos cartão de crédito (todas as bandeiras), PIX, boleto bancário e débito online. Parcelamos em até 12x no cartão.
                    </div>
                </div>

                <div class="faq-item" data-category="technical">
                    <div class="faq-question">O sistema está lento, o que pode ser?</div>
                    <div class="faq-answer">
                        Pode ser congestionamento temporário da rede. Tente: 1) Limpar cache do navegador, 2) Usar outro navegador, 3) Verificar sua conexão de internet. Se persistir, entre em contato.
                    </div>
                </div>

                <div class="faq-item" data-category="general">
                    <div class="faq-question">Qual o horário de atendimento?</div>
                    <div class="faq-answer">
                        Atendemos de segunda a sexta, das 8h às 18h, e sábados das 9h às 13h. Fora desse horário, você pode enviar e-mail que responderemos no próximo dia útil.
                    </div>
                </div>

                <div class="faq-item" data-category="technical">
                    <div class="faq-question">Como faço backup dos meus dados?</div>
                    <div class="faq-answer">
                        O sistema faz backup automático diariamente. Para backup manual, acesse Configurações > Backup e clique em "Exportar Dados". Você receberá um e-mail com o arquivo.
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Methods -->
        <section class="contact-methods" id="contact">
            <div class="contact-method">
                <div class="method-icon">📞</div>
                <h3>Telefone</h3>
                <p>Atendimento direto com nossa equipe especializada</p>
                <p><strong>(11) 9999-9999</strong></p>
                <a href="tel:11999999999" class="method-button">Ligar Agora</a>
            </div>

            <div class="contact-method">
                <div class="method-icon">✉️</div>
                <h3>E-mail</h3>
                <p>Envie sua dúvida detalhada para análise completa</p>
                <p><strong>suporte@empresa.com</strong></p>
                <a href="mailto:suporte@empresa.com" class="method-button">Enviar E-mail</a>
            </div>

            <div class="contact-method">
                <div class="method-icon">💬</div>
                <h3>Chat Online</h3>
                <p>Atendimento instantâneo em tempo real</p>
                <p><strong>Disponível 24/7</strong></p>
                <button class="method-button" onclick="openChat()">Iniciar Chat</button>
            </div>
        </section>

        <!-- Status System -->
        <section class="status-system" id="status">
            <h2>Status do Sistema</h2>
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-indicator status-operational"></div>
                    <p>Sistema Principal</p>
                    <span>Operacional</span>
                </div>
                <div class="status-item">
                    <div class="status-indicator status-operational"></div>
                    <p>API</p>
                    <span>Operacional</span>
                </div>
                <div class="status-item">
                    <div class="status-indicator status-maintenance"></div>
                    <p>Serviços de E-mail</p>
                    <span>Manutenção</span>
                </div>
                <div class="status-item">
                    <div class="status-indicator status-operational"></div>
                    <p>Banco de Dados</p>
                    <span>Operacional</span>
                </div>
            </div>
        </section>

        <!-- Knowledge Base -->
        <section class="knowledge-base" id="knowledge">
            <h2>Base de Conhecimento</h2>
            <div class="kb-categories">
                <div class="kb-category">
                    <h3>Guias de Início Rápido</h3>
                    <p>Tutoriais para começar a usar o sistema</p>
                    <div class="kb-count">15 artigos</div>
                </div>

                <div class="kb-category">
                    <h3>Manuais Técnicos</h3>
                    <p>Documentação técnica avançada</p>
                    <div class="kb-count">28 artigos</div>
                </div>

                <div class="kb-category">
                    <h3>Videoaulas</h3>
                    <p>Tutoriais em vídeo passo a passo</p>
                    <div class="kb-count">42 vídeos</div>
                </div>

                <div class="kb-category">
                    <h3>Solucionar Problemas</h3>
                    <p>Resolução de erros comuns</p>
                    <div class="kb-count">35 artigos</div>
                </div>
            </div>
        </section>

        <!-- Additional Support Options -->
        <section class="faq-section">
            <h2>Outras Opções de Suporte</h2>
            
            <div class="support-option">
                <h4>📋 Abrir um Ticket</h4>
                <p>Para problemas complexos que requerem acompanhamento detalhado</p>
                <a href="ticket.php" class="method-button">Abrir Ticket</a>
            </div>

            <div class="support-option">
                <h4>👥 Comunidade</h4>
                <p>Converse com outros usuários e compartilhe soluções</p>
                <a href="comunidade.php" class="method-button">Acessar Comunidade</a>
            </div>

            <div class="support-option">
                <h4>📞 Suporte Prioritário</h4>
                <p>Atendimento exclusivo para clientes enterprise</p>
                <a href="prioritario.php" class="method-button">Falar com Especialista</a>
            </div>
        </section>
    </div>

    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                item.classList.toggle('active');
            });
        });

        // FAQ Categories Filter
        document.querySelectorAll('.faq-category').forEach(category => {
            category.addEventListener('click', () => {
                // Remove active class from all categories
                document.querySelectorAll('.faq-category').forEach(cat => {
                    cat.classList.remove('active');
                });
                
                // Add active class to clicked category
                category.classList.add('active');
                
                const selectedCategory = category.getAttribute('data-category');
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    if (selectedCategory === 'all' || item.getAttribute('data-category') === selectedCategory) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Search Functionality
        document.querySelector('.search-button').addEventListener('click', performSearch);
        document.querySelector('.search-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        function performSearch() {
            const searchTerm = document.querySelector('.search-input').value.toLowerCase();
            if (searchTerm.trim() === '') return;
            
            // Simulate search - in real implementation, this would call an API
            alert(`Buscando por: ${searchTerm}\n\nEm uma implementação real, esta função pesquisaria na base de conhecimento e FAQ.`);
        }

        // Scroll to Section
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Open Chat Simulation
        function openChat() {
            alert('Chat de suporte iniciado!\n\nEm uma implementação real, esta função abriria um widget de chat com nossa equipe.');
        }

        // Auto-expand FAQ based on URL hash
        window.addEventListener('load', () => {
            const urlHash = window.location.hash;
            if (urlHash) {
                const targetElement = document.querySelector(urlHash);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
</body>
</html>