<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - Stars Models</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #000000;
            color: #ffffff;
        }

        .privacy-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .privacy-header {
            background: #1f1f1f;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
            margin-top: 30px;
        }

        .privacy-header h1 {
            color: #ffffff;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .privacy-header p {
            color: #d1d5db;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .last-updated {
            color: #9ca3af;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Navigation - CORES ROXO/ROSA DO SITE */
        .privacy-nav {
            background: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .privacy-nav h3 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-link {
            padding: 8px 16px;
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            border: 1px solid #8b5cf6;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);
            color: white;
            border-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Content */
        .privacy-content {
            background: #1f1f1f;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .privacy-section {
            margin-bottom: 40px;
            scroll-margin-top: 100px;
        }

        .privacy-section:last-child {
            margin-bottom: 0;
        }

        .privacy-section h2 {
            color: #ffffff;
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #374151;
        }

        .privacy-section h3 {
            color: #e5e7eb;
            font-size: 1.3rem;
            margin: 25px 0 15px 0;
        }

        .privacy-section p {
            color: #d1d5db;
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .privacy-section ul, .privacy-section ol {
            margin: 15px 0;
            padding-left: 30px;
        }

        .privacy-section li {
            color: #d1d5db;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .highlight-box {
            background: #374151;
            border-left: 4px solid #8b5cf6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .warning-box {
            background: #7c2d12;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .important-note {
            background: #1e3a8a;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        /* Definition Lists */
        .definition-list {
            margin: 20px 0;
        }

        .definition-term {
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .definition-description {
            color: #d1d5db;
            margin-bottom: 15px;
            padding-left: 20px;
        }

        /* Table */
        .privacy-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .privacy-table th,
        .privacy-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #374151;
        }

        .privacy-table th {
            background: #374151;
            font-weight: bold;
            color: #ffffff;
        }

        .privacy-table tr:nth-child(even) {
            background: #374151;
        }

        /* Footer */
        .privacy-footer {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .contact-link {
            color: #8b5cf6;
            text-decoration: none;
        }

        .contact-link:hover {
            text-decoration: underline;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .star-glow {
            filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.6)) drop-shadow(0 0 12px rgba(236, 72, 153, 0.4));
        }

        /* Responsive */
        @media (max-width: 768px) {
            .privacy-container {
                padding: 10px;
            }
            
            .privacy-header {
                padding: 30px 20px;
            }
            
            .privacy-header h1 {
                font-size: 2rem;
            }
            
            .privacy-content {
                padding: 30px 20px;
            }
            
            .nav-links {
                flex-direction: column;
            }
            
            .nav-link {
                text-align: center;
            }
        }

        /* Print Styles */
        @media print {
            .privacy-nav, .privacy-footer {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .privacy-container {
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body class="bg-black">
    <!-- Navegação Igual ao Home Public -->
    <nav class="bg-black shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="flex items-center space-x-3">
                            <!-- Estrela brilhante -->
                            <div class="star-glow">
                                <svg width="28" height="28" viewBox="0 0 24 24">
                                    <defs>
                                        <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#8b5cf6" />
                                            <stop offset="100%" stop-color="#ec4899" />
                                        </linearGradient>
                                        <filter id="glow">
                                            <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                                            <feMerge>
                                                <feMergeNode in="coloredBlur"/>
                                                <feMergeNode in="SourceGraphic"/>
                                            </feMerge>
                                        </filter>
                                    </defs>
                                    <path fill="url(#starGradient)" filter="url(#glow)" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <!-- Texto -->
                            <span class="text-2xl font-bold gradient-text">STARS MODELS</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                        Login
                    </a>
                    <a href="cadastro.php" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                        Cadastre-se
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="privacy-container">
        <!-- Header -->
        <header class="privacy-header">
            <h1>Política de Privacidade</h1>
            <p>Saiba como protegemos e utilizamos suas informações pessoais</p>
            <div class="last-updated">Última atualização: 15 de Janeiro de 2024</div>
        </header>

        <!-- Navigation -->
        <nav class="privacy-nav">
            <h3>Navegação Rápida</h3>
            <div class="nav-links">
                <a href="#introducao" class="nav-link">1. Introdução</a>
                <a href="#coleta" class="nav-link">2. Coleta de Dados</a>
                <a href="#uso" class="nav-link">3. Uso dos Dados</a>
                <a href="#compartilhamento" class="nav-link">4. Compartilhamento</a>
                <a href="#seguranca" class="nav-link">5. Segurança</a>
                <a href="#direitos" class="nav-link">6. Seus Direitos</a>
                <a href="#cookies" class="nav-link">7. Cookies</a>
                <a href="#alteracoes" class="nav-link">8. Alterações</a>
                <a href="#contato" class="nav-link">9. Contato</a>
            </div>
        </nav>

        <!-- Content -->
        <main class="privacy-content">
            <section id="introducao" class="privacy-section">
                <h2>1. Introdução</h2>
                <p>A Stars Models Agency valoriza sua privacidade e está comprometida em proteger suas informações pessoais. Esta Política de Privacidade explica como coletamos, usamos, compartilhamos e protegemos suas informações quando você utiliza nossa plataforma.</p>
                
                <div class="highlight-box">
                    <p><strong>Importante:</strong> Ao utilizar nossos serviços, você concorda com os termos desta Política de Privacidade. Recomendamos que leia este documento cuidadosamente.</p>
                </div>
            </section>

            <section id="coleta" class="privacy-section">
                <h2>2. Coleta de Dados</h2>
                
                <h3>2.1 Informações que Coletamos</h3>
                <p>Coletamos os seguintes tipos de informações:</p>
                <ul>
                    <li><strong>Informações Pessoais:</strong> Nome, e-mail, telefone, data de nascimento</li>
                    <li><strong>Informações Profissionais:</strong> Experiência, portfolio, medidas (para modelos)</li>
                    <li><strong>Informações de Empresa:</strong> CNPJ, razão social (para clientes)</li>
                    <li><strong>Informações Técnicas:</strong> IP, tipo de navegador, dispositivo</li>
                    <li><strong>Dados de Uso:</strong> Páginas visitadas, tempo de sessão</li>
                </ul>

                <h3>2.2 Como Coletamos</h3>
                <p>Coletamos informações através de:</p>
                <ul>
                    <li>Formulários de cadastro e perfil</li>
                    <li>Interações com nossa plataforma</li>
                    <li>Comunicações por e-mail</li>
                    <li>Cookies e tecnologias similares</li>
                </ul>

                <div class="warning-box">
                    <p><strong>Atenção:</strong> Coletamos apenas informações necessárias para fornecer nossos serviços e melhorar sua experiência.</p>
                </div>
            </section>

            <section id="uso" class="privacy-section">
                <h2>3. Uso dos Dados</h2>
                
                <h3>3.1 Finalidades do Uso</h3>
                <p>Utilizamos suas informações para:</p>
                <ul>
                    <li>Fornecer e melhorar nossos serviços</li>
                    <li>Processar cadastros e gerenciar contas</li>
                    <li>Facilitar conexões entre modelos e clientes</li>
                    <li>Enviar comunicações importantes</li>
                    <li>Personalizar sua experiência</li>
                    <li>Cumprir obrigações legais</li>
                </ul>

                <h3>3.2 Base Legal</h3>
                <p>Nosso tratamento de dados é baseado em:</p>
                <ul>
                    <li><strong>Consentimento:</strong> Quando você nos fornece dados voluntariamente</li>
                    <li><strong>Contrato:</strong> Para execução de serviços contratados</li>
                    <li><strong>Legítimo Interesse:</strong> Para melhorar nossos serviços</li>
                    <li><strong>Obrigação Legal:</strong> Para cumprir leis e regulamentos</li>
                </ul>
            </section>

            <section id="compartilhamento" class="privacy-section">
                <h2>4. Compartilhamento de Dados</h2>
                
                <h3>4.1 Quando Compartilhamos</h3>
                <p>Podemos compartilhar suas informações com:</p>
                <ul>
                    <li><strong>Clientes/Agências:</strong> Perfil de modelos para oportunidades</li>
                    <li><strong>Modelos:</strong> Informações de jobs e contratantes</li>
                    <li><strong>Prestadores de Serviço:</strong> Hospedagem, análise de dados</li>
                    <li><strong>Autoridades:</strong> Quando exigido por lei</li>
                </ul>

                <h3>4.2 Controle de Compartilhamento</h3>
                <p>Você tem controle sobre:</p>
                <ul>
                    <li>Quais informações do perfil são públicas</li>
                    <li>Comunicações de marketing</li>
                    <li>Visibilidade para outros usuários</li>
                </ul>

                <div class="important-note">
                    <p><strong>Nota:</strong> Não vendemos suas informações pessoais para terceiros.</p>
                </div>
            </section>

            <section id="seguranca" class="privacy-section">
                <h2>5. Segurança de Dados</h2>
                
                <h3>5.1 Medidas de Proteção</h3>
                <p>Implementamos medidas de segurança robustas:</p>
                <ul>
                    <li>Criptografia de dados sensíveis</li>
                    <li>Controle de acesso baseado em função</li>
                    <li>Monitoramento contínuo de segurança</li>
                    <li>Backups regulares</li>
                    <li>Treinamento de equipe em proteção de dados</li>
                </ul>

                <h3>5.2 Retenção de Dados</h3>
                <p>Mantemos suas informações apenas pelo tempo necessário:</p>
                <ul>
                    <li>Dados de conta: Enquanto a conta estiver ativa</li>
                    <li>Dados de transação: 5 anos (exigência legal)</li>
                    <li>Dados de uso: 2 anos para melhorias</li>
                </ul>
            </section>

            <section id="direitos" class="privacy-section">
                <h2>6. Seus Direitos</h2>
                
                <h3>6.1 Direitos do Titular</h3>
                <p>De acordo com a LGPD, você tem direito a:</p>
                <ul>
                    <li><strong>Acesso:</strong> Saber quais dados temos sobre você</li>
                    <li><strong>Correção:</strong> Retificar dados incompletos ou desatualizados</li>
                    <li><strong>Exclusão:</strong> Solicitar a eliminação de dados</li>
                    <li><strong>Portabilidade:</strong> Receber dados em formato estruturado</li>
                    <li><strong>Revogação:</strong> Retirar consentimento a qualquer momento</li>
                    <li><strong>Oposição:</strong> Opor-se a determinado tratamento</li>
                </ul>

                <h3>6.2 Como Exercer Seus Direitos</h3>
                <p>Para exercer seus direitos, entre em contato através do e-mail: <a href="mailto:privacidade@starsmodels.com" class="contact-link">privacidade@starsmodels.com</a></p>

                <table class="privacy-table">
                    <thead>
                        <tr>
                            <th>Direito</th>
                            <th>Prazo de Resposta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Acesso e Confirmação</td>
                            <td>15 dias</td>
                        </tr>
                        <tr>
                            <td>Correção e Exclusão</td>
                            <td>15 dias</td>
                        </tr>
                        <tr>
                            <td>Portabilidade</td>
                            <td>15 dias</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section id="cookies" class="privacy-section">
                <h2>7. Cookies e Tecnologias Similares</h2>
                
                <h3>7.1 O que São Cookies</h3>
                <p>Cookies são pequenos arquivos armazenados no seu dispositivo que nos ajudam a:</p>
                <ul>
                    <li>Lembrar suas preferências</li>
                    <li>Melhorar a segurança</li>
                    <li>Analisar o uso da plataforma</li>
                    <li>Personalizar conteúdo</li>
                </ul>

                <h3>7.2 Controle de Cookies</h3>
                <p>Você pode controlar cookies através:</p>
                <ul>
                    <li>Configurações do navegador</li>
                    <li>Ferramentas de opt-out</li>
                    <li>Nossos avisos de consentimento</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Importante:</strong> A desativação de cookies pode afetar a funcionalidade de alguns recursos da plataforma.</p>
                </div>
            </section>

            <section id="alteracoes" class="privacy-section">
                <h2>8. Alterações na Política</h2>
                <p>Podemos atualizar esta Política de Privacidade periodicamente. Notificaremos sobre alterações significativas através de:</p>
                
                <ul>
                    <li>E-mail para usuários cadastrados</li>
                    <li>Aviso em nossa plataforma</li>
                    <li>Atualização da data de "Última atualização"</li>
                </ul>

                <div class="warning-box">
                    <p><strong>Atenção:</strong> O uso continuado de nossos serviços após alterações constitui aceitação da nova política.</p>
                </div>
            </section>

            <section id="contato" class="privacy-section">
                <h2>9. Contato e Dúvidas</h2>
                
                <h3>9.1 Encarregado de Proteção de Dados</h3>
                <p>Nosso Encarregado de Proteção de Dados (DPO) está disponível para:</p>
                <ul>
                    <li>Esclarecer dúvidas sobre privacidade</li>
                    <li>Receber solicitações de direitos</li>
                    <li>Tratar incidentes de segurança</li>
                </ul>

                <h3>9.2 Como Nos Contactar</h3>
                <div class="definition-list">
                    <div class="definition-term">E-mail do DPO</div>
                    <div class="definition-description"><a href="mailto:privacidade@starsmodels.com" class="contact-link">privacidade@starsmodels.com</a></div>
                    
                    <div class="definition-term">Telefone</div>
                    <div class="definition-description">(61) 4184-4847</div>
                    
                    <div class="definition-term">Endereço</div>
                    <div class="definition-description">Asa Norte, Brasília - DF, Brasil</div>
                    
                    <div class="definition-term">Prazo de Resposta</div>
                    <div class="definition-description">Até 15 dias úteis</div>
                </div>

                <div class="important-note">
                    <p><strong>Nota:</strong> Para exercer seus direitos ou reportar preocupações com privacidade, entre em contato com nosso DPO.</p>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="privacy-footer">
            <p>Em caso de dúvidas sobre esta Política de Privacidade, entre em contato conosco através do e-mail <a href="mailto:privacidade@starsmodels.com" class="contact-link">privacidade@starsmodels.com</a></p>
            <p>© 2024 Stars Models Agency. Todos os direitos reservados.</p>
        </footer>
    </div>

    <script>
        // Navigation active state
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Scroll to section
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        // Update active nav link on scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('.privacy-section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 150;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    currentSection = '#' + section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === currentSection) {
                    link.classList.add('active');
                }
            });
        });

        // Print functionality
        function printPrivacy() {
            window.print();
        }

        // Add print button dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.privacy-header');
            const printButton = document.createElement('button');
            printButton.textContent = '📄 Imprimir Política';
            printButton.style.cssText = `
                background: #6c757d;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 15px;
                transition: background-color 0.3s ease;
            `;
            printButton.onmouseover = function() { this.style.background = '#5a6268'; }
            printButton.onmouseout = function() { this.style.background = '#6c757d'; }
            printButton.onclick = printPrivacy;
            
            header.appendChild(printButton);
        });
    </script>
</body>
</html>