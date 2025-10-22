<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso - Nossa Plataforma</title>
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

        .terms-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .terms-header {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }

        .terms-header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .terms-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .last-updated {
            color: #999;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Navigation */
        .terms-nav {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .terms-nav h3 {
            color: #333;
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
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            text-decoration: none;
            color: #667eea;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Content */
        .terms-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .terms-section {
            margin-bottom: 40px;
            scroll-margin-top: 100px;
        }

        .terms-section:last-child {
            margin-bottom: 0;
        }

        .terms-section h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f4;
        }

        .terms-section h3 {
            color: #444;
            font-size: 1.3rem;
            margin: 25px 0 15px 0;
        }

        .terms-section p {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .terms-section ul, .terms-section ol {
            margin: 15px 0;
            padding-left: 30px;
        }

        .terms-section li {
            color: #555;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .important-note {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
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
            color: #333;
            margin-bottom: 5px;
        }

        .definition-description {
            color: #555;
            margin-bottom: 15px;
            padding-left: 20px;
        }

        /* Table */
        .terms-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .terms-table th,
        .terms-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e9ecef;
        }

        .terms-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .terms-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Acceptance Section */
        .acceptance-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }

        .acceptance-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .acceptance-section p {
            color: #666;
            margin-bottom: 20px;
        }

        .accept-button {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .accept-button:hover {
            background: #218838;
        }

        /* Footer */
        .terms-footer {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 0.9rem;
        }

        .contact-link {
            color: #667eea;
            text-decoration: none;
        }

        .contact-link:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .terms-container {
                padding: 10px;
            }
            
            .terms-header {
                padding: 30px 20px;
            }
            
            .terms-header h1 {
                font-size: 2rem;
            }
            
            .terms-content {
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
            .terms-nav, .acceptance-section, .terms-footer {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .terms-container {
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="terms-container">
        <!-- Header -->
        <header class="terms-header">
            <h1>Termos de Uso</h1>
            <p>Leia atentamente os termos e condições que regem o uso de nossa plataforma</p>
            <div class="last-updated">Última atualização: 15 de Janeiro de 2024</div>
        </header>

        <!-- Navigation -->
        <nav class="terms-nav">
            <h3>Navegação Rápida</h3>
            <div class="nav-links">
                <a href="#aceitacao" class="nav-link">1. Aceitação</a>
                <a href="#cadastro" class="nav-link">2. Cadastro</a>
                <a href="#uso" class="nav-link">3. Uso da Plataforma</a>
                <a href="#propriedade" class="nav-link">4. Propriedade</a>
                <a href="#privacidade" class="nav-link">5. Privacidade</a>
                <a href="#responsabilidades" class="nav-link">6. Responsabilidades</a>
                <a href="#modificacoes" class="nav-link">7. Modificações</a>
                <a href="#rescisao" class="nav-link">8. Rescisão</a>
                <a href="#disposicoes" class="nav-link">9. Disposições</a>
            </div>
        </nav>

        <!-- Content -->
        <main class="terms-content">
            <section id="aceitacao" class="terms-section">
                <h2>1. Aceitação dos Termos</h2>
                <p>Ao acessar e utilizar nossa plataforma, você concorda em ficar vinculado por estes Termos de Uso e por todas as leis e regulamentos aplicáveis. Se você não concordar com algum destes termos, está proibido de usar ou acessar este site.</p>
                
                <div class="highlight-box">
                    <p><strong>Importante:</strong> Estes termos constituem um acordo legal entre você e nossa empresa. Recomendamos que leia cuidadosamente todo o documento.</p>
                </div>
            </section>

            <section id="cadastro" class="terms-section">
                <h2>2. Cadastro e Conta do Usuário</h2>
                
                <h3>2.1 Elegibilidade</h3>
                <p>Para utilizar nossos serviços, você deve:</p>
                <ul>
                    <li>Ter pelo menos 18 anos de idade</li>
                    <li>Fornecer informações precisas e completas</li>
                    <li>Manter a segurança de sua conta e senha</li>
                    <li>Aceitar total responsabilidade por todas as atividades que ocorram em sua conta</li>
                </ul>

                <h3>2.2 Verificação</h3>
                <p>Reservamo-nos o direito de:</p>
                <ul>
                    <li>Solicitar documentos para verificação de identidade</li>
                    <li>Recusar ou cancelar cadastros que não cumpram nossos requisitos</li>
                    <li>Suspender contas com atividades suspeitas</li>
                </ul>

                <div class="warning-box">
                    <p><strong>Atenção:</strong> Você é responsável por manter a confidencialidade de sua senha e por todas as atividades realizadas em sua conta.</p>
                </div>
            </section>

            <section id="uso" class="terms-section">
                <h2>3. Uso da Plataforma</h2>
                
                <h3>3.1 Conduta Permitida</h3>
                <p>Você concorda em usar a plataforma apenas para fins legais e de acordo com estes termos. Condutas permitidas incluem:</p>
                <ul>
                    <li>Acesso aos serviços conforme disponibilizado</li>
                    <li>Uso pessoal ou empresarial legítimo</li>
                    <li>Respeito aos direitos de outros usuários</li>
                </ul>

                <h3>3.2 Condutas Proibidas</h3>
                <p>É expressamente proibido:</p>
                <ul>
                    <li>Violar qualquer lei ou regulamento aplicável</li>
                    <li>Infringir direitos de propriedade intelectual</li>
                    <li>Distribuir malware ou código malicioso</li>
                    <li>Realizar atividades de hacking ou engenharia reversa</li>
                    <li>Spam ou envio de comunicações não solicitadas</li>
                    <li>Praticar assédio ou discriminação</li>
                </ul>

                <div class="important-note">
                    <p><strong>Nota:</strong> Reservamo-nos o direito de investigar e tomar ações legais contra qualquer violação destes termos.</p>
                </div>
            </section>

            <section id="propriedade" class="terms-section">
                <h2>4. Propriedade Intelectual</h2>
                
                <h3>4.1 Direitos da Empresa</h3>
                <p>Todos os direitos de propriedade intelectual relacionados à plataforma, incluindo但不限于:</p>
                <ul>
                    <li>Software e código fonte</li>
                    <li>Marcas registradas e logotipos</li>
                    <li>Design e interface do usuário</li>
                    <li>Documentação e manuais</li>
                </ul>

                <h3>4.2 Conteúdo do Usuário</h3>
                <p>Ao enviar conteúdo para nossa plataforma, você:</p>
                <ul>
                    <li>Garante que tem direito sobre o conteúdo</li>
                    <li>Concede licença não-exclusiva para utilização</li>
                    <li>Autoriza a exibição e distribuição conforme necessário</li>
                </ul>
            </section>

            <section id="privacidade" class="terms-section">
                <h2>5. Privacidade e Proteção de Dados</h2>
                <p>Nosso compromisso com sua privacidade está detalhado em nossa <a href="politica-privacidade.php" class="contact-link">Política de Privacidade</a>, que faz parte integrante destes Termos de Uso.</p>
                
                <h3>5.1 Coleta de Dados</h3>
                <p>Coletamos e utilizamos dados conforme necessário para:</p>
                <ul>
                    <li>Prestação dos serviços contratados</li>
                    <li>Melhoria contínua da plataforma</li>
                    <li>Cumprimento de obrigações legais</li>
                </ul>

                <h3>5.2 Segurança</h3>
                <p>Implementamos medidas de segurança técnicas e organizacionais para proteger seus dados, incluindo:</p>
                <ul>
                    <li>Criptografia de dados sensíveis</li>
                    <li>Controles de acesso rigorosos</li>
                    <li>Monitoramento contínuo de segurança</li>
                </ul>
            </section>

            <section id="responsabilidades" class="terms-section">
                <h2>6. Limitações de Responsabilidade</h2>
                
                <h3>6.1 Isenções</h3>
                <p>Não nos responsabilizamos por:</p>
                <ul>
                    <li>Danos indiretos, incidentais ou consequenciais</li>
                    <li>Interrupções temporárias do serviço</li>
                    <li>Ações de terceiros não controlados por nós</li>
                    <li>Uso indevido da plataforma por usuários</li>
                </ul>

                <h3>6.2 Garantias</h3>
                <p>A plataforma é fornecida "no estado em que se encontra", sem garantias de qualquer tipo, expressas ou implícitas.</p>

                <table class="terms-table">
                    <thead>
                        <tr>
                            <th>Situação</th>
                            <th>Responsabilidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Manutenção programada</td>
                            <td>Comunicação prévia de 48h</td>
                        </tr>
                        <tr>
                            <td>Problemas técnicos</td>
                            <td>Resolução em até 24h</td>
                        </tr>
                        <tr>
                            <td>Casos fortuitos</td>
                            <td>Isenção de responsabilidade</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section id="modificacoes" class="terms-section">
                <h2>7. Modificações dos Termos</h2>
                <p>Reservamo-nos o direito de modificar estes Termos de Uso a qualquer momento. As alterações entrarão em vigor:</p>
                
                <ul>
                    <li>Imediatamente para novos usuários</li>
                    <li>30 dias após a notificação para usuários existentes</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Notificação:</strong> Notificaremos sobre alterações significativas através de e-mail ou aviso na plataforma. O uso continuado após as modificações constitui aceitação dos novos termos.</p>
                </div>
            </section>

            <section id="rescisao" class="terms-section">
                <h2>8. Rescisão</h2>
                
                <h3>8.1 Pelo Usuário</h3>
                <p>Você pode encerrar sua conta a qualquer momento através das configurações da plataforma ou entrando em contato conosco.</p>

                <h3>8.2 Pela Empresa</h3>
                <p>Podemos suspender ou encerrar seu acesso imediatamente se:</p>
                <ul>
                    <li>Violar estes Termos de Uso</li>
                    <li>Praticar atividades fraudulentas</li>
                    <li>Colocar em risco a segurança da plataforma</li>
                    <li>Deixar de pagar pelos serviços</li>
                </ul>
            </section>

            <section id="disposicoes" class="terms-section">
                <h2>9. Disposições Finais</h2>
                
                <h3>9.1 Lei Aplicável</h3>
                <p>Estes Termos são regidos pelas leis da República Federativa do Brasil.</p>

                <h3>9.2 Foro</h3>
                <p>Fica eleito o foro da comarca de São Paulo/SP para dirimir quaisquer questões decorrentes destes termos.</p>

                <h3>9.3 Divisibilidade</h3>
                <p>Se qualquer disposição destes Termos for considerada inválida ou inexequível, as demais disposições permanecerão em pleno vigor e efeito.</p>

                <div class="definition-list">
                    <div class="definition-term">Plataforma</div>
                    <div class="definition-description">Refere-se ao website, aplicativos móveis, APIs e todos os serviços relacionados fornecidos por nossa empresa.</div>
                    
                    <div class="definition-term">Usuário</div>
                    <div class="definition-description">Qualquer pessoa que acesse ou utilize nossa plataforma, cadastrada ou não.</div>
                    
                    <div class="definition-term">Conteúdo</div>
                    <div class="definition-description">Qualquer informação, texto, imagem ou outro material disponibilizado através da plataforma.</div>
                </div>
            </section>
        </main>

        <!-- Acceptance -->
        <section class="acceptance-section">
            <h3>Você leu e compreendeu nossos Termos de Uso?</h3>
            <p>Ao utilizar nossa plataforma, você confirma que leu, compreendeu e concorda com todos os termos e condições aqui estabelecidos.</p>
            <button class="accept-button" onclick="acceptTerms()">Aceitar Termos de Uso</button>
        </section>

        <!-- Footer -->
        <footer class="terms-footer">
            <p>Em caso de dúvidas sobre estes Termos de Uso, entre em contato conosco através do e-mail <a href="mailto:juridico@empresa.com" class="contact-link">juridico@empresa.com</a></p>
            <p>© 2024 Nossa Empresa. Todos os direitos reservados.</p>
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
            const sections = document.querySelectorAll('.terms-section');
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

        // Accept Terms Function
        function acceptTerms() {
            // In a real implementation, this would save acceptance to database
            const acceptanceDate = new Date().toLocaleDateString('pt-BR');
            
            // Show confirmation
            alert(`Termos de Uso aceitos em ${acceptanceDate}\n\nObrigado por revisar e aceitar nossos termos. Você será redirecionado para a plataforma.`);
            
            // Redirect to main platform (simulated)
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        }

        // Print functionality
        function printTerms() {
            window.print();
        }

        // Add print button dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.terms-header');
            const printButton = document.createElement('button');
            printButton.textContent = '📄 Imprimir Termos';
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
            printButton.onclick = printTerms;
            
            header.appendChild(printButton);
        });
    </script>
</body>
</html>