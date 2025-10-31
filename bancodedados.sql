-- =============================================================================
-- BANCO DE DADOS STARS MODELS - VERSÃO COMPLETA E LIMPA
-- =============================================================================

CREATE DATABASE IF NOT EXISTS stars_models;
USE stars_models;

-- =============================================================================
-- TABELAS PRINCIPAIS
-- =============================================================================

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente', 'modelo') DEFAULT 'cliente',
    cpf VARCHAR(14),
    telefone VARCHAR(20),
    endereco TEXT,
    empresa VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de modelos
CREATE TABLE modelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    altura DECIMAL(4,2),
    busto DECIMAL(5,2),
    quadril DECIMAL(5,2),
    tipo_profissao ENUM('fashion', 'comercial', 'ator', 'atriz', 'alta-costura', 'fitness', 'plus-size', 'kids', 'adolescente'),
    experiencia TEXT,
    portfolio TEXT,
    status ENUM('ativo', 'inativo', 'pendente') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    cnpj VARCHAR(18),
    razao_social VARCHAR(100),
    nome_fantasia VARCHAR(100),
    ramo_atuacao VARCHAR(100),
    tamanho_empresa ENUM('pequena', 'media', 'grande'),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de jobs/trabalhos
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    tipo_modelo VARCHAR(50),
    localizacao VARCHAR(100),
    remuneracao DECIMAL(10,2),
    data_evento DATE,
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aberto', 'fechado', 'concluido', 'cancelado') DEFAULT 'aberto',
    cliente_id INT,
    vagas INT DEFAULT 1,
    requisitos TEXT,
    beneficios TEXT,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id)
);

-- Tabela de notícias
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    conteudo TEXT,
    imagem VARCHAR(255),
    categoria ENUM('moda', 'beauty', 'desfiles', 'campanhas', 'entrevistas', 'eventos', 'premios'),
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    autor_id INT,
    views INT DEFAULT 0,
    status ENUM('publicada', 'rascunho', 'arquivada') DEFAULT 'rascunho',
    FOREIGN KEY (autor_id) REFERENCES usuarios(id)
);

-- Tabela de contatos/suporte
CREATE TABLE contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    assunto VARCHAR(200),
    mensagem TEXT,
    status ENUM('novo', 'respondido', 'fechado') DEFAULT 'novo',
    data_contato TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resposta TEXT,
    data_resposta TIMESTAMP NULL,
    respondido_por INT,
    FOREIGN KEY (respondido_por) REFERENCES usuarios(id)
);

-- Tabela para candidaturas
CREATE TABLE candidaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    modelo_id INT,
    mensagem TEXT,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'contratado') DEFAULT 'pendente',
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_avaliacao TIMESTAMP NULL,
    avaliado_por INT,
    feedback TEXT,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (modelo_id) REFERENCES modelos(id),
    FOREIGN KEY (avaliado_por) REFERENCES usuarios(id)
);

-- Tabela de configurações do sistema
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'number', 'boolean', 'json'),
    descricao TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- =============================================================================
-- TABELAS COMPLEMENTARES
-- =============================================================================

-- Tabela de FAQ
CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta VARCHAR(300) NOT NULL,
    resposta TEXT NOT NULL,
    categoria VARCHAR(50) DEFAULT 'Geral',
    ordem INT DEFAULT 0,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tickets de suporte
CREATE TABLE tickets_suporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    assunto VARCHAR(200) NOT NULL,
    descricao TEXT NOT NULL,
    categoria ENUM('tecnico', 'faturamento', 'conta', 'outro') DEFAULT 'tecnico',
    prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'media',
    status ENUM('aberto', 'em_andamento', 'respondido', 'fechado') DEFAULT 'aberto',
    data_abertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de mensagens dos tickets
CREATE TABLE mensagens_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    anexo VARCHAR(255),
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('usuario', 'suporte') DEFAULT 'usuario',
    FOREIGN KEY (ticket_id) REFERENCES tickets_suporte(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de logs de acesso
CREATE TABLE logs_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- =============================================================================
-- ÍNDICES PARA PERFORMANCE
-- =============================================================================

CREATE INDEX idx_modelos_status ON modelos(status);
CREATE INDEX idx_modelos_tipo ON modelos(tipo_profissao);
CREATE INDEX idx_jobs_status ON jobs(status);
CREATE INDEX idx_jobs_data ON jobs(data_publicacao);
CREATE INDEX idx_noticias_data ON noticias(data_publicacao);
CREATE INDEX idx_contatos_status ON contatos(status);
CREATE INDEX idx_candidaturas_status ON candidaturas(status);
CREATE INDEX idx_faq_categoria ON faq(categoria);
CREATE INDEX idx_faq_status ON faq(status);
CREATE INDEX idx_tickets_status ON tickets_suporte(status);
CREATE INDEX idx_tickets_prioridade ON tickets_suporte(prioridade);
CREATE INDEX idx_mensagens_ticket ON mensagens_tickets(ticket_id);
CREATE INDEX idx_logs_data ON logs_acesso(data_registro);

-- =============================================================================
-- INSERIR DADOS INICIAIS
-- =============================================================================

-- Administrador principal atualizado
INSERT INTO usuarios (id, nome, email, senha, tipo, telefone, empresa) VALUES 
(1, 'Administrador Stars', 'admstars@starsmodels.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '(61) 3333-3333', 'Stars Models Agency');

-- Modelos de exemplo
INSERT INTO usuarios (nome, email, senha, tipo, telefone, created_at) VALUES 
('Ana Silva', 'ana.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 99999-9999', '2023-06-15'),
('Bruno Oliveira', 'bruno.oliveira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 88888-8888', '2023-07-20'),
('Carla Santos', 'carla.santos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 77777-7777', '2023-08-10'),
('Diego Costa', 'diego.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(21) 66666-6666', '2023-09-05'),
('Fernanda Lima', 'fernanda.lima@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(21) 55555-5555', '2023-10-12'),
('Gabriela Rocha', 'gabriela.rocha@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(31) 44444-4444', '2023-11-08'),
('Rafael Souza', 'rafael.souza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(31) 33333-3333', '2023-12-01');

-- Dados dos modelos
INSERT INTO modelos (usuario_id, altura, busto, quadril, tipo_profissao, experiencia, portfolio, status) VALUES 
(2, 1.78, 88.0, 92.0, 'fashion', 'Experiência em desfiles internacionais e campanhas publicitárias.', 'https://instagram.com/ana.silva.model', 'ativo'),
(3, 1.85, 95.0, 98.0, 'comercial', 'Trabalhos em comerciais de TV e campanhas impressas.', 'https://portfolio.brunooliveira.com', 'ativo'),
(4, 1.75, 86.0, 90.0, 'atriz', 'Participação em novelas e comerciais. Formada em teatro.', 'https://carlasantosactress.com', 'ativo'),
(5, 1.88, 98.0, 102.0, 'ator', 'Experiência em teatro e cinema.', 'https://instagram.com/diegocosta.actor', 'ativo'),
(6, 1.70, 84.0, 88.0, 'alta-costura', 'Desfiles para marcas de luxo.', 'https://fernandalimamodel.com', 'ativo'),
(7, 1.68, 92.0, 96.0, 'plus-size', 'Campanhas de moda inclusiva.', 'https://instagram.com/gabrielaplus', 'ativo'),
(8, 1.82, 94.0, 97.0, 'fitness', 'Modelo fitness para campanhas esportivas.', 'https://rafaelsouzafitness.com', 'ativo');

-- Clientes de exemplo
INSERT INTO usuarios (nome, email, senha, tipo, telefone, empresa) VALUES 
('Maria Fashion', 'contato@mariafashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(11) 22222-2222', 'Maria Fashion'),
('João Produções', 'producao@joaoproducoes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(21) 11111-1111', 'João Produções'),
('Beleza Natural', 'rh@belezanatural.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(31) 00000-0000', 'Beleza Natural Cosméticos'),
('Moda Jovem', 'casting@modajovem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(41) 12345-6789', 'Moda Jovem LTDA'),
('Esporte Total', 'contato@esportetotal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(51) 98765-4321', 'Esporte Total');

-- Dados dos clientes
INSERT INTO clientes (usuario_id, cnpj, razao_social, nome_fantasia, ramo_atuacao, tamanho_empresa) VALUES 
(9, '12.345.678/0001-90', 'Maria Fashion Comércio LTDA', 'Maria Fashion', 'Moda Feminina', 'media'),
(10, '98.765.432/0001-10', 'João Produções Artísticas ME', 'João Produções', 'Produção Audiovisual', 'pequena'),
(11, '11.223.344/0001-55', 'Beleza Natural Cosméticos SA', 'Beleza Natural', 'Cosméticos e Beleza', 'grande'),
(12, '55.667.788/0001-22', 'Moda Jovem Confecções LTDA', 'Moda Jovem', 'Moda Juvenil', 'media'),
(13, '99.887.766/0001-33', 'Esporte Total Comércio ME', 'Esporte Total', 'Artigos Esportivos', 'pequena');

-- Jobs de exemplo
INSERT INTO jobs (titulo, descricao, tipo_modelo, localizacao, remuneracao, data_evento, status, cliente_id, vagas, requisitos, beneficios) VALUES 
('Modelo para Campanha Verão 2024', 'Buscamos modelo para campanha de verão de marca de roupas praiana.', 'fashion', 'Rio de Janeiro', 5000.00, '2024-02-15', 'aberto', 9, 2, 'Altura mínima 1.70m, experiência em ensaios externos.', 'Cache competitivo, produção completa.'),
('Atriz para Comercial de TV', 'Seleção para atriz principal de comercial de produto de beleza.', 'atriz', 'São Paulo', 8000.00, '2024-02-20', 'aberto', 11, 1, 'Idade 25-35 anos, experiência em TV.', 'Cache atrativo, diretor renomado.'),
('Modelo Plus Size para Coleção Inclusiva', 'Marca inclusiva busca modelos plus size para nova coleção.', 'plus-size', 'Brasília', 3500.00, '2024-03-10', 'aberto', 12, 3, 'Modelo plus size, experiência em ensaios.', 'Participação em campanha nacional.'),
('Modelo Fitness para App Esportivo', 'Buscamos modelo fitness para campanha de aplicativo.', 'fitness', 'Remoto', 4000.00, '2024-02-28', 'aberto', 13, 1, 'Boa forma física, experiência em fotos esportivas.', 'Exposição nacional, conteúdo para portfolio.');

-- Notícias de exemplo
INSERT INTO noticias (titulo, conteudo, imagem, categoria, autor_id, views, status) VALUES 
('Stars Models conquista prêmio de Melhor Agência do Ano 2024', 'Pela terceira vez consecutiva, a Stars Models Agency foi eleita a Melhor Agência.', 'premios.jpg', 'premios', 1, 1250, 'publicada'),
('Nova parceria com grife internacional', 'Fechamos exclusividade com renomada grife francesa.', 'parceria.jpg', 'moda', 1, 890, 'publicada'),
('Modelo brasileira estrela campanha global', 'Nossa modelo foi selecionada para campanha mundial.', 'campanha.jpg', 'campanhas', 1, 1560, 'publicada');

-- Contatos de exemplo
INSERT INTO contatos (nome, email, assunto, mensagem, status, data_contato) VALUES 
('João Silva', 'joao.silva@email.com', 'Cadastro de Modelo', 'Gostaria de informações sobre cadastro.', 'respondido', '2024-01-10 14:30:00'),
('Maria Santos', 'maria.santos@email.com', 'Contratação de Modelos', 'Preciso de modelos para campanha.', 'novo', '2024-01-12 09:15:00');

-- Candidaturas de exemplo
INSERT INTO candidaturas (job_id, modelo_id, mensagem, status, data_candidatura) VALUES 
(1, 1, 'Tenho experiência em ensaios de praia.', 'pendente', '2024-01-15 10:00:00'),
(1, 3, 'Sou atriz mas também trabalho como modelo fashion.', 'aprovado', '2024-01-15 11:30:00');

-- Configurações do sistema
INSERT INTO configuracoes (chave, valor, tipo, descricao) VALUES 
('site_nome', 'Stars Models Agency', 'string', 'Nome do site'),
('site_email', 'contato@starsmodels.com', 'string', 'Email de contato'),
('site_telefone', '(61) 98765-4321', 'string', 'Telefone de contato'),
('modo_manutencao', '0', 'boolean', 'Ativar modo manutenção'),
('permitir_cadastros', '1', 'boolean', 'Permitir novos cadastros'),
('site_slogan', 'Conectando talentos a oportunidades brilhantes', 'string', 'Slogan da empresa'),
('email_suporte', 'suporte@starsmodels.com', 'string', 'Email do suporte técnico');

-- FAQs iniciais
INSERT INTO faq (pergunta, resposta, categoria, ordem) VALUES 
('Como me cadastrar como modelo?', 'Acesse a página de cadastro, selecione "Modelo" e preencha as informações.', 'Cadastro', 1),
('Quais são os requisitos para ser modelo?', 'Os requisitos variam conforme o tipo de modelo.', 'Requisitos', 2),
('Como funciona o processo de seleção?', 'Após o cadastro, nossa equipe analisa seu perfil.', 'Processo', 3);

-- Tickets de exemplo
INSERT INTO tickets_suporte (usuario_id, assunto, descricao, categoria, prioridade, status) VALUES 
(2, 'Problema com acesso à plataforma', 'Não consigo acessar minha conta.', 'tecnico', 'alta', 'aberto'),
(3, 'Dúvida sobre contrato', 'Gostaria de revisar o contrato.', 'conta', 'media', 'em_andamento');

-- Mensagens nos tickets
INSERT INTO mensagens_tickets (ticket_id, usuario_id, mensagem, tipo) VALUES 
(1, 2, 'Não consigo acessar minha conta.', 'usuario'),
(1, 1, 'Verificamos seu cadastro e reenviamos o link.', 'suporte');

-- Logs de exemplo
INSERT INTO logs_sistema (usuario_id, acao, descricao, ip_address, user_agent) VALUES 
(1, 'LOGIN', 'Administrador fez login no sistema', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0)'),
(2, 'ATUALIZAR_PERFIL', 'Modelo atualizou perfil', '192.168.1.101', 'Mozilla/5.0 (iPhone)');

INSERT INTO logs_acesso (usuario_id, acao, ip_address, user_agent) VALUES 
(2, 'LOGIN', '192.168.1.101', 'Mozilla/5.0 (iPhone)'),
(3, 'CANDIDATAR_JOB', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0)');

-- =============================================================================
-- VIEWS ÚTEIS
-- =============================================================================

CREATE VIEW view_modelos_ativos AS
SELECT m.*, u.nome, u.email, u.telefone
FROM modelos m
JOIN usuarios u ON m.usuario_id = u.id
WHERE m.status = 'ativo';

CREATE VIEW view_estatisticas_gerais AS
SELECT 
    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
    (SELECT COUNT(*) FROM modelos WHERE status = 'ativo') as modelos_ativos,
    (SELECT COUNT(*) FROM jobs WHERE status = 'aberto') as jobs_abertos,
    (SELECT COUNT(*) FROM contatos WHERE status = 'novo') as contatos_novos;

CREATE VIEW view_tickets_completos AS
SELECT t.*, u.nome as usuario_nome, u.email as usuario_email, u.tipo as usuario_tipo
FROM tickets_suporte t
JOIN usuarios u ON t.usuario_id = u.id;

CREATE VIEW view_modelos_completos AS
SELECT m.*, u.nome, u.email, u.telefone, u.created_at as data_cadastro
FROM modelos m
JOIN usuarios u ON m.usuario_id = u.id;

-- =============================================================================
-- PROCEDURES E TRIGGERS
-- =============================================================================

DELIMITER $$
CREATE PROCEDURE AtualizarStatusModelo(
    IN p_modelo_id INT,
    IN p_novo_status ENUM('ativo', 'inativo', 'pendente')
)
BEGIN
    UPDATE modelos SET status = p_novo_status, updated_at = CURRENT_TIMESTAMP 
    WHERE id = p_modelo_id;
    
    INSERT INTO logs_sistema (usuario_id, acao, descricao)
    VALUES (1, 'ATUALIZAR_STATUS', CONCAT('Status do modelo ', p_modelo_id, ' atualizado para ', p_novo_status));
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE FecharTicket(
    IN p_ticket_id INT,
    IN p_usuario_id INT
)
BEGIN
    UPDATE tickets_suporte SET status = 'fechado', data_atualizacao = CURRENT_TIMESTAMP
    WHERE id = p_ticket_id;
    
    INSERT INTO mensagens_tickets (ticket_id, usuario_id, mensagem, tipo)
    VALUES (p_ticket_id, p_usuario_id, 'Ticket fechado pelo sistema.', 'suporte');
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_modelo_update
    AFTER UPDATE ON modelos
    FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO logs_sistema (usuario_id, acao, descricao)
        VALUES (1, 'STATUS_MODELO', CONCAT('Modelo ', NEW.id, ' alterou status de ', OLD.status, ' para ', NEW.status));
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_job_insert
    AFTER INSERT ON jobs
    FOR EACH ROW
BEGIN
    INSERT INTO logs_sistema (usuario_id, acao, descricao)
    VALUES (NEW.cliente_id, 'NOVO_JOB', CONCAT('Novo job criado: ', NEW.titulo));
END$$
DELIMITER ;

-- =============================================================================
-- RESUMO FINAL
-- =============================================================================

SELECT 
    '✅ Banco Stars Models criado com sucesso!' as mensagem,
    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
    (SELECT COUNT(*) FROM modelos) as total_modelos,
    (SELECT COUNT(*) FROM jobs) as total_jobs,
    (SELECT COUNT(*) FROM noticias) as total_noticias,
    (SELECT COUNT(*) FROM faq) as total_faq,
    (SELECT COUNT(*) FROM tickets_suporte) as total_tickets;

SHOW TABLES;
-- Email: admstars@starsmodels.com
-- Senha: S74Rs