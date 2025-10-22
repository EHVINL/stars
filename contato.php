<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Entre em Contato Conosco</title>
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

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 50px;
        }

        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        /* Formulário de Contato */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Informações de Contato */
        .contact-info {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .contact-info h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .contact-item:hover {
            background-color: #f8f9fa;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .contact-icon i {
            color: white;
            font-size: 1.2rem;
        }

        .contact-details h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .contact-details p {
            color: #666;
            line-height: 1.5;
        }

        /* Mapa */
        .map-section {
            margin-bottom: 50px;
        }

        .map-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .map-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .map-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        /* FAQ */
        .faq-section {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 50px;
        }

        .faq-section h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: between;
            align-items: center;
            font-weight: bold;
            color: #333;
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            color: #666;
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 200px;
        }

        /* Mensagens de Status */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="contact-container">
        <header class="page-header">
            <h1>Entre em Contato</h1>
            <p>Estamos aqui para ajudar. Envie sua mensagem e retornaremos em breve.</p>
        </header>

        <div class="contact-content">
            <!-- Formulário de Contato -->
            <section class="contact-form">
                <h2>Envie sua Mensagem</h2>
                
                <div id="alert-message" class="alert" style="display: none;"></div>
                
                <form id="contactForm" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome Completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto <span class="required">*</span></label>
                        <select id="assunto" name="assunto" class="form-control" required>
                            <option value="">Selecione um assunto</option>
                            <option value="suporte">Suporte Técnico</option>
                            <option value="vendas">Dúvidas sobre Vendas</option>
                            <option value="parceria">Proposta de Parceria</option>
                            <option value="reclamacao">Reclamação</option>
                            <option value="sugestao">Sugestão</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem <span class="required">*</span></label>
                        <textarea id="mensagem" name="mensagem" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Enviar Mensagem</button>
                </form>
            </section>

            <!-- Informações de Contato -->
            <section class="contact-info">
                <h2>Informações de Contato</h2>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i>📍</i>
                    </div>
                    <div class="contact-details">
                        <h3>Endereço</h3>
                        <p>Rua Exemplo, 123<br>Centro, São Paulo - SP<br>CEP: 01234-567</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i>📞</i>
                    </div>
                    <div class="contact-details">
                        <h3>Telefone</h3>
                        <p>(11) 9999-9999<br>(11) 8888-8888</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i>✉️</i>
                    </div>
                    <div class="contact-details">
                        <h3>E-mail</h3>
                        <p>contato@empresa.com<br>suporte@empresa.com</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i>🕒</i>
                    </div>
                    <div class="contact-details">
                        <h3>Horário de Atendimento</h3>
                        <p>Segunda a Sexta: 8h às 18h<br>Sábado: 8h às 12h</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Mapa -->
        <section class="map-section">
            <h2>Onde Estamos</h2>
            <div class="map-container">
                <div class="map-placeholder">
                    [Mapa do Google Maps será integrado aqui]
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="faq-section">
            <h2>Perguntas Frequentes</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    Qual o prazo para resposta?
                </div>
                <div class="faq-answer">
                    Respondemos todas as mensagens em até 24 horas úteis.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Vocês atendem em todo o Brasil?
                </div>
                <div class="faq-answer">
                    Sim, nosso atendimento é nacional através dos canais online.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Posso agendar uma reunião?
                </div>
                <div class="faq-answer">
                    Claro! Entre em contato por telefone ou e-mail para agendarmos.
                </div>
            </div>
        </section>
    </div>

    <script>
        // Validação do Formulário
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const assunto = document.getElementById('assunto').value;
            const mensagem = document.getElementById('mensagem').value;
            const alertMessage = document.getElementById('alert-message');

            // Validação simples
            if (!nome || !email || !assunto || !mensagem) {
                showAlert('Por favor, preencha todos os campos obrigatórios.', 'error');
                return;
            }

            if (!isValidEmail(email)) {
                showAlert('Por favor, insira um e-mail válido.', 'error');
                return;
            }

            // Simulação de envio (substitua por AJAX/PHP real)
            showAlert('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
            document.getElementById('contactForm').reset();
        });

        function showAlert(message, type) {
            const alert = document.getElementById('alert-message');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                item.classList.toggle('active');
            });
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    </script>

    <?php
    // Backend PHP para processar o formulário (descomente quando necessário)
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
        $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
        $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);
        
        // Validações
        if (!$nome || !$email || !$assunto || !$mensagem) {
            echo "<script>showAlert('Por favor, preencha todos os campos obrigatórios.', 'error');</script>";
        } elseif (!$email) {
            echo "<script>showAlert('Por favor, insira um e-mail válido.', 'error');</script>";
        } else {
            // Processar o envio (email, salvar no banco, etc.)
            $to = "contato@empresa.com";
            $subject = "Contato do Site: " . $assunto;
            $message = "Nome: $nome\nE-mail: $email\nTelefone: $telefone\nAssunto: $assunto\nMensagem:\n$mensagem";
            $headers = "From: $email";
            
            if (mail($to, $subject, $message, $headers)) {
                echo "<script>showAlert('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');</script>";
            } else {
                echo "<script>showAlert('Erro ao enviar mensagem. Tente novamente.', 'error');</script>";
            }
        }
    }
    
    ?>
</body>
</html>
<!-- Descomente e ajuste o código PHP no final

Configure o e-mail para envio real

Substitua o placeholder do mapa pela API do Google Maps

Ajuste as informações de contato para suas reais -->