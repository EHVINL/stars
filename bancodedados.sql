-- add esse primeiro
-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS stars_models;
USE stars_models;

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

-- Tabela de clientes (específica para informações de empresas)
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
-- INSERIR DADOS DE EXEMPLO
-- =============================================================================

-- Inserir admin padrão (senha: 123456)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@starsmodels.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserir modelos de exemplo
INSERT INTO usuarios (nome, email, senha, tipo, telefone, created_at) VALUES 
('Ana Silva', 'ana.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 99999-9999', '2023-06-15'),
('Bruno Oliveira', 'bruno.oliveira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 88888-8888', '2023-07-20'),
('Carla Santos', 'carla.santos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(11) 77777-7777', '2023-08-10'),
('Diego Costa', 'diego.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(21) 66666-6666', '2023-09-05'),
('Fernanda Lima', 'fernanda.lima@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(21) 55555-5555', '2023-10-12'),
('Gabriela Rocha', 'gabriela.rocha@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(31) 44444-4444', '2023-11-08'),
('Rafael Souza', 'rafael.souza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'modelo', '(31) 33333-3333', '2023-12-01');

-- Inserir dados dos modelos
INSERT INTO modelos (usuario_id, altura, busto, quadril, tipo_profissao, experiencia, portfolio, status) VALUES 
(2, 1.78, 88.0, 92.0, 'fashion', 'Experiência em desfiles internacionais e campanhas publicitárias. Curso de passarela na Escola de Modelos SP. Participação no São Paulo Fashion Week 2023.', 'https://instagram.com/ana.silva.model', 'ativo'),
(3, 1.85, 95.0, 98.0, 'comercial', 'Trabalhos em comerciais de TV e campanhas impressas para grandes marcas. 2 anos de experiência no mercado. Especialista em campanhas publicitárias.', 'https://portfolio.brunooliveira.com', 'ativo'),
(4, 1.75, 86.0, 90.0, 'atriz', 'Participação em novelas e comerciais. Formada em teatro pela UNB. Fluente em inglês e espanhol. Workshop de interpretação para TV.', 'https://carlasantosactress.com', 'ativo'),
(5, 1.88, 98.0, 102.0, 'ator', 'Experiência em teatro e cinema. Participação em 3 peças teatrais e 2 curtas-metragens. Curso de interpretação para cinema.', 'https://instagram.com/diegocosta.actor', 'ativo'),
(6, 1.70, 84.0, 88.0, 'alta-costura', 'Desfiles para marcas de luxo. Experiência internacional em Milão e Paris. Especialista em passarela de alta-costura.', 'https://fernandalimamodel.com', 'ativo'),
(7, 1.68, 92.0, 96.0, 'plus-size', 'Campanhas de moda inclusiva. Palestrante sobre diversidade na moda. Experiência em editoriais para revistas especializadas.', 'https://instagram.com/gabrielaplus', 'ativo'),
(8, 1.82, 94.0, 97.0, 'fitness', 'Modelo fitness para campanhas esportivas. Personal trainer certificado. Experiência em ensaios para marcas de suplementos.', 'https://rafaelsouzafitness.com', 'ativo');

-- Inserir clientes de exemplo
INSERT INTO usuarios (nome, email, senha, tipo, telefone, empresa) VALUES 
('Maria Fashion', 'contato@mariafashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(11) 22222-2222', 'Maria Fashion'),
('João Produções', 'producao@joaoproducoes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(21) 11111-1111', 'João Produções'),
('Beleza Natural', 'rh@belezanatural.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(31) 00000-0000', 'Beleza Natural Cosméticos'),
('Moda Jovem', 'casting@modajovem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(41) 12345-6789', 'Moda Jovem LTDA'),
('Esporte Total', 'contato@esportetotal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', '(51) 98765-4321', 'Esporte Total');

-- Inserir dados dos clientes
INSERT INTO clientes (usuario_id, cnpj, razao_social, nome_fantasia, ramo_atuacao, tamanho_empresa) VALUES 
(9, '12.345.678/0001-90', 'Maria Fashion Comércio LTDA', 'Maria Fashion', 'Moda Feminina', 'media'),
(10, '98.765.432/0001-10', 'João Produções Artísticas ME', 'João Produções', 'Produção Audiovisual', 'pequena'),
(11, '11.223.344/0001-55', 'Beleza Natural Cosméticos SA', 'Beleza Natural', 'Cosméticos e Beleza', 'grande'),
(12, '55.667.788/0001-22', 'Moda Jovem Confecções LTDA', 'Moda Jovem', 'Moda Juvenil', 'media'),
(13, '99.887.766/0001-33', 'Esporte Total Comércio ME', 'Esporte Total', 'Artigos Esportivos', 'pequena');

-- Inserir jobs de exemplo
INSERT INTO jobs (titulo, descricao, tipo_modelo, localizacao, remuneracao, data_evento, status, cliente_id, vagas, requisitos, beneficios) VALUES 
('Modelo para Campanha Verão 2024', 'Buscamos modelo para campanha de verão de marca de roupas praiana. Experiência em ensaios externos é desejável. Produção profissional com equipe completa.\n\nRequisitos:\n- Altura: 1.70m+\n- Disponibilidade para viagem\n- Boa comunicação\n\nLocal: Praias do Rio de Janeiro\nDuração: 3 dias de ensaio', 'fashion', 'Rio de Janeiro', 5000.00, '2024-02-15', 'aberto', 9, 2, 'Altura mínima 1.70m, experiência em ensaios externos, boa comunicação.', 'Cache competitivo, produção completa, material para portfolio, hospedagem e alimentação inclusas.'),
('Atriz para Comercial de TV', 'Seleção para atriz principal de comercial de produto de beleza. Idade: 25-35 anos. Experiência em TV desejável. Gravação em estúdio em São Paulo.\n\nPersonagem: Mulher profissional, elegante, idade 30-35 anos.\n\nGravações: 3 dias em estúdio em São Paulo', 'atriz', 'São Paulo', 8000.00, '2024-02-20', 'aberto', 11, 1, 'Idade 25-35 anos, experiência em TV, disponibilidade para gravações em São Paulo.', 'Cache atrativo, diretor renomado, material para reel, alimentação no set.'),
('Modelo Plus Size para Coleção Inclusiva', 'Marca inclusiva busca modelos plus size para nova coleção outono/inverno. Valorizamos diversidade e beleza real. Ensaios em estúdio em Brasília.\n\nProcuramos modelos que representem a diversidade brasileira. Todas as etnias e tipos corporais são bem-vindos.', 'plus-size', 'Brasília', 3500.00, '2024-03-10', 'aberto', 12, 3, 'Modelo plus size, experiência em ensaios fotográficos, boa expressão facial.', 'Participação em campanha nacional, book profissional, cachê competitivo.'),
('Modelo Fitness para App Esportivo', 'Buscamos modelo fitness para campanha de aplicativo de exercícios. Deve ter boa forma física e experiência em fotos esportivas.\n\nSerão produzidos vídeos e fotos para redes sociais e app. Conteúdo será utilizado por 1 ano.', 'fitness', 'Remoto', 4000.00, '2024-02-28', 'aberto', 13, 1, 'Boa forma física, experiência em fotos esportivas, disponibilidade para gravações.', 'Exposição nacional, conteúdo para portfolio, parceria de longo prazo.'),
('Ator para Papel em Websérie', 'Seleção para ator protagonista de websérie teen. Idade: 18-25 anos. Gravações em Goiânia. Personagem: jovem universitário.\n\nWebsérie com 8 episódios para plataforma de streaming. Gravações em abril e maio.', 'ator', 'Goiás', 6000.00, '2024-04-01', 'aberto', 10, 1, 'Idade 18-25 anos, experiência em atuação, disponibilidade para gravações em Goiânia.', 'Papel protagonista, diretor experiente, projeção nacional.'),
('Modelo Kids para Campanha Infantil', 'Marca de roupas infantis busca modelos kids (5-8 anos) para campanha de volta às aulas. Experiência com crianças é essencial.\n\nEnsaios em estúdio child-friendly com equipe especializada.', 'kids', 'Minas Gerais', 2000.00, '2024-02-25', 'aberto', 9, 4, 'Idade 5-8 anos, experiência em ensaios infantis, acompanhamento dos responsáveis.', 'Ambiente seguro e profissional, equipe especializada, material para portfolio.');

-- Inserir notícias de exemplo
INSERT INTO noticias (titulo, conteudo, imagem, categoria, autor_id, views, status) VALUES 
('Stars Models conquista prêmio de Melhor Agência do Ano 2024', 'Pela terceira vez consecutiva, a Stars Models Agency foi eleita a Melhor Agência de Modelos do Brasil no prestigiado Fashion Awards 2024. O prêmio reconhece nossa excelência em descobrir e desenvolver talentos, além de nossa atuação inovadora no mercado da moda.\n\n"Nossa missão sempre foi transformar sonhos em realidade. Este prêmio é o reconhecimento do trabalho dedicado de toda nossa equipe e da confiança de nossos modelos e clientes", declarou nossa diretora criativa durante a cerimônia.\n\nA entrega do prêmio aconteceu no Copacabana Palace, no Rio de Janeiro, com a presença das principais personalidades da moda nacional. O evento contou com desfile beneficente e leilão de peças exclusivas.', 'https://cdn.pixabay.com/photo/2015/06/27/21/21/prize-823854_1280.jpg', 'premios', 1, 1250, 'publicada'),
('Nova parceria com grife internacional revoluciona o mercado', 'Fechamos exclusividade com a renomada grife francesa para fornecimento de modelos brasileiros para suas campanhas internacionais. A parceria, que tem duração de dois anos, inclui desfiles em Paris, Milão e Nova York.\n\nSerão selecionados 15 modelos de nosso casting para representar a marca em suas coleções de alta-costura. "O talento brasileiro tem uma energia única que combina perfeitamente com a estética da marca", comentou o diretor criativo da grife.\n\nAs seleções começam no próximo mês e prometem revelar novos talentos para o mercado internacional. Esta parceria consolida a posição do Brasil no cenário global da moda.', 'https://cdn.pixabay.com/photo/2025/03/22/02/40/ai-generated-9486125_640.jpg', 'moda', 1, 890, 'publicada'),
('Modelo brasileira estrela campanha global de beleza', 'Nossa modelo Carolina Mendes foi selecionada para estrelar a campanha mundial da nova fragrância da marca de luxo. As gravações aconteceram em locações paradisíacas nas Maldivas e contaram com uma produção de alto nível.\n\n"É um sonho realizado. A Stars Models me preparou para este momento desde o início da minha carreira", emocionou-se a modelo durante as gravações.\n\nA campanha será veiculada em 50 países a partir do próximo mês, consolidando a presença brasileira no cenário global da beleza. Esta é a primeira vez que uma modelo brasileira estrela sozinha uma campanha global desta marca.', 'https://cdn.pixabay.com/photo/2025/05/18/09/15/ai-generated-9606987_960_720.jpg', 'campanhas', 1, 1560, 'publicada');

-- Inserir mensagens de contato de exemplo
INSERT INTO contatos (nome, email, assunto, mensagem, status, data_contato) VALUES 
('João Silva', 'joao.silva@email.com', 'Cadastro de Modelo', 'Gostaria de mais informações sobre o cadastro como modelo. Tenho 22 anos e 1,85m de altura. Qual é o processo de seleção? Preciso ter experiência prévia?', 'respondido', '2024-01-10 14:30:00'),
('Maria Santos', 'maria.santos@email.com', 'Contratação de Modelos', 'Preciso de modelos para uma campanha publicitária em Brasília. Poderiam me enviar o portfólio de modelos disponíveis? A campanha é para uma marca de cosméticos.', 'novo', '2024-01-12 09:15:00'),
('Carlos Oliveira', 'carlos.oliveira@email.com', 'Dúvidas Gerais', 'Gostaria de saber se trabalham com modelos plus size e quais são os requisitos. Tenho 1,70m e medidas 95-80-105.', 'respondido', '2024-01-11 16:45:00'),
('Ana Costa', 'ana.costa@email.com', 'Parcerias', 'Temos uma marca de roupas e gostaríamos de estabelecer uma parceria para desfiles. Podemos agendar uma reunião?', 'novo', '2024-01-13 11:20:00'),
('Pedro Almeida', 'pedro.almeida@email.com', 'Workshop de Modelos', 'Gostaria de informações sobre os workshops para novos modelos. Há vagas para o próximo mês? Qual o investimento?', 'respondido', '2024-01-14 08:30:00');

-- Inserir candidaturas de exemplo
INSERT INTO candidaturas (job_id, modelo_id, mensagem, status, data_candidatura) VALUES 
(1, 1, 'Tenho experiência em ensaios de praia e adoraria participar desta campanha de verão. Meu portfolio inclui trabalhos similares.', 'pendente', '2024-01-15 10:00:00'),
(1, 3, 'Sou atriz mas também trabalho como modelo fashion. Tenho boa experiência com ensaios externos e adoro trabalhar na praia.', 'aprovado', '2024-01-15 11:30:00'),
(2, 4, 'Tenho experiência em comerciais de TV e me identifico muito com o perfil da personagem. Fluente em inglês.', 'pendente', '2024-01-16 09:15:00'),
(3, 6, 'Como modelo plus size, acredito que posso representar muito bem a diversidade que vocês buscam. Tenho experiência em campanhas inclusivas.', 'aprovado', '2024-01-16 14:20:00');

-- Inserir configurações padrão do sistema
INSERT INTO configuracoes (chave, valor, tipo, descricao) VALUES 
('site_nome', 'Stars Models Agency', 'string', 'Nome do site'),
('site_email', 'contato@starsmodels.com', 'string', 'Email de contato principal'),
('site_telefone', '(61) 98765-4321', 'string', 'Telefone de contato'),
('site_endereco', 'Asa Norte, Brasília - DF', 'string', 'Endereço da empresa'),
('modo_manutencao', '0', 'boolean', 'Ativar modo manutenção'),
('permitir_cadastros', '1', 'boolean', 'Permitir novos cadastros'),
('itens_por_pagina', '10', 'number', 'Itens por página nas listagens'),
('email_notificacoes', '1', 'boolean', 'Enviar emails de notificação');

-- Inserir logs do sistema de exemplo
INSERT INTO logs_sistema (usuario_id, acao, descricao, ip_address, user_agent) VALUES 
(1, 'LOGIN', 'Administrador fez login no sistema', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'CRIAR_NOTICIA', 'Nova notícia publicada: Stars Models conquista prêmio', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'APROVAR_MODELO', 'Modelo Ana Silva aprovado no sistema', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

-- Criar índices para melhor performance
CREATE INDEX idx_modelos_status ON modelos(status);
CREATE INDEX idx_modelos_tipo ON modelos(tipo_profissao);
CREATE INDEX idx_jobs_status ON jobs(status);
CREATE INDEX idx_jobs_data ON jobs(data_publicacao);
CREATE INDEX idx_noticias_data ON noticias(data_publicacao);
CREATE INDEX idx_contatos_status ON contatos(status);
CREATE INDEX idx_candidaturas_status ON candidaturas(status);

-- Criar views úteis
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

-- Criar usuário para aplicação (opcional)
-- CREATE USER 'stars_user'@'localhost' IDENTIFIED BY 'senha_segura';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON stars_models.* TO 'stars_user'@'localhost';
-- FLUSH PRIVILEGES;



















-- dps esse
-- =============================================================================
-- ADICIONANDO TABELAS COMPLEMENTARES AO BANCO STARS_MODELS
-- =============================================================================

USE stars_models;

-- Tabela de FAQ (Faltando no seu banco)
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta VARCHAR(300) NOT NULL,
    resposta TEXT NOT NULL,
    categoria VARCHAR(50) DEFAULT 'Geral',
    ordem INT DEFAULT 0,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tickets de suporte (Faltando no seu banco)
CREATE TABLE IF NOT EXISTS tickets_suporte (
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

-- Tabela de mensagens dos tickets (Faltando no seu banco)
CREATE TABLE IF NOT EXISTS mensagens_tickets (
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

-- Tabela de logs de acesso (Complementar)
CREATE TABLE IF NOT EXISTS logs_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- =============================================================================
-- INSERIR DADOS NAS NOVAS TABELAS
-- =============================================================================

-- Inserir FAQs iniciais
INSERT INTO faq (pergunta, resposta, categoria, ordem) VALUES 
('Como me cadastrar como modelo?', 'Para se cadastrar como modelo, acesse a página de cadastro, selecione a opção "Modelo" e preencha todas as informações solicitadas incluindo medidas, experiência e portfolio. Nossa equipe avaliará seu perfil em até 5 dias úteis.', 'Cadastro', 1),
('Quais são os requisitos para ser modelo?', 'Os requisitos variam conforme o tipo de modelo. Para fashion: altura mínima de 1,70m (feminino) ou 1,80m (masculino). Para comercial e plus-size: medidas proporcionais e boa comunicação. Consulte nossa página de requisitos completos.', 'Requisitos', 2),
('Como funciona o processo de seleção?', 'Após o cadastro, nossa equipe analisa seu perfil. Se aprovado na triagem, você será convidado para um teste fotográfico e entrevista. O processo completo leva em média 7-10 dias úteis.', 'Processo', 3),
('Quanto tempo leva para começar a trabalhar?', 'Modelos aprovados geralmente começam a receber propostas em 2-3 semanas. O tempo pode variar conforme a demanda do mercado e seu perfil específico.', 'Trabalho', 4),
('Como são os pagamentos?', 'Os pagamentos são realizados via transferência bancária em até 15 dias úteis após a conclusão do trabalho. Fornecemos recibo e contrato para todos os jobs.', 'Pagamento', 5),
('Preciso de portfolio profissional?', 'Não é obrigatório, mas recomendamos. Para iniciantes, aceitamos fotos simples em fundo neutro. Após a aprovação, podemos ajudar na produção do book profissional.', 'Portfolio', 6);

-- Inserir tickets de exemplo
INSERT INTO tickets_suporte (usuario_id, assunto, descricao, categoria, prioridade, status) VALUES 
(2, 'Problema com acesso à plataforma', 'Não consigo acessar minha conta desde ontem. Recebo mensagem de "senha incorreta" mesmo resetando a senha.', 'tecnico', 'alta', 'aberto'),
(3, 'Dúvida sobre contrato', 'Gostaria de revisar o contrato do último job. Há algumas cláusulas que preciso entender melhor.', 'conta', 'media', 'em_andamento'),
(9, 'Problema com pagamento', 'O pagamento do job de dezembro ainda não foi processado. Já se passaram 20 dias úteis.', 'faturamento', 'alta', 'respondido');

-- Inserir mensagens nos tickets
INSERT INTO mensagens_tickets (ticket_id, usuario_id, mensagem, tipo) VALUES 
(1, 2, 'Olá, desde ontem à noite não consigo acessar minha conta. Já tentei resetar a senha três vezes mas não recebo o e-mail de recuperação. Podem me ajudar?', 'usuario'),
(1, 1, 'Olá Ana! Verificamos seu cadastro e reenviamos o link de recuperação. Por favor, verifique sua caixa de spam também. Se não receber em 10 minutos, entre em contato por telefone.', 'suporte'),
(2, 3, 'Boa tarde, gostaria de esclarecer algumas dúvidas sobre o contrato do job da campanha de verão. Especificamente sobre as cláusulas de exclusividade e direitos de imagem.', 'usuario'),
(3, 9, 'Prezados, o pagamento referente ao job #245 ainda não foi creditado. Segue comprovante do trabalho concluído em 15/12. Podem verificar?', 'usuario'),
(3, 1, 'Cara Maria, verificamos e identificamos um problema no processamento. Seu pagamento será realizado até amanhã. Pedimos desculpas pelo inconveniente.', 'suporte');

-- Inserir logs de acesso de exemplo
INSERT INTO logs_acesso (usuario_id, acao, ip_address, user_agent) VALUES 
(2, 'LOGIN', '192.168.1.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)'),
(3, 'ATUALIZAR_PERFIL', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(9, 'ABRIR_TICKET', '192.168.1.103', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'),
(2, 'CANDIDATAR_JOB', '192.168.1.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');

-- =============================================================================
-- ATUALIZAR CONFIGURAÇÕES EXISTENTES
-- =============================================================================

-- Atualizar configurações com informações específicas da Stars Models
INSERT INTO configuracoes (chave, valor, tipo, descricao) VALUES 
('site_slogan', 'Conectando talentos a oportunidades brilhantes', 'string', 'Slogan da empresa'),
('politica_privacidade', '1', 'boolean', 'Exibir política de privacidade'),
('termos_uso', '1', 'boolean', 'Exibir termos de uso'),
('max_fotos_portfolio', '20', 'number', 'Número máximo de fotos no portfolio'),
('dias_pagamento', '15', 'number', 'Prazo máximo para pagamentos'),
('email_suporte', 'suporte@starsmodels.com', 'string', 'Email do suporte técnico'),
('telefone_emergencia', '(61) 99999-9999', 'string', 'Telefone para emergências');

-- =============================================================================
-- CRIAR ÍNDICES ADICIONAIS
-- =============================================================================

CREATE INDEX IF NOT EXISTS idx_faq_categoria ON faq(categoria);
CREATE INDEX IF NOT EXISTS idx_faq_status ON faq(status);
CREATE INDEX IF NOT EXISTS idx_tickets_status ON tickets_suporte(status);
CREATE INDEX IF NOT EXISTS idx_tickets_prioridade ON tickets_suporte(prioridade);
CREATE INDEX IF NOT EXISTS idx_mensagens_ticket ON mensagens_tickets(ticket_id);
CREATE INDEX IF NOT EXISTS idx_logs_data ON logs_acesso(data_registro);

-- =============================================================================
-- CRIAR VIEWS ADICIONAIS
-- =============================================================================

-- View para tickets com informações do usuário
CREATE VIEW view_tickets_completos AS
SELECT 
    t.*,
    u.nome as usuario_nome,
    u.email as usuario_email,
    u.tipo as usuario_tipo
FROM tickets_suporte t
JOIN usuarios u ON t.usuario_id = u.id;

-- View para estatísticas de suporte
CREATE VIEW view_estatisticas_suporte AS
SELECT 
    COUNT(*) as total_tickets,
    SUM(CASE WHEN status = 'aberto' THEN 1 ELSE 0 END) as tickets_abertos,
    SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) as tickets_andamento,
    SUM(CASE WHEN status = 'respondido' THEN 1 ELSE 0 END) as tickets_respondidos,
    SUM(CASE WHEN prioridade = 'alta' THEN 1 ELSE 0 END) as tickets_alta_prioridade
FROM tickets_suporte;

-- View para modelos com informações completas
CREATE VIEW view_modelos_completos AS
SELECT 
    m.*,
    u.nome,
    u.email,
    u.telefone,
    u.created_at as data_cadastro
FROM modelos m
JOIN usuarios u ON m.usuario_id = u.id;

-- =============================================================================
-- PROCEDURES ÚTEIS
-- =============================================================================

-- Procedure para atualizar status do modelo
DELIMITER $$
CREATE PROCEDURE AtualizarStatusModelo(
    IN p_modelo_id INT,
    IN p_novo_status ENUM('ativo', 'inativo', 'pendente')
)
BEGIN
    UPDATE modelos 
    SET status = p_novo_status, 
        updated_at = CURRENT_TIMESTAMP 
    WHERE id = p_modelo_id;
    
    INSERT INTO logs_sistema (usuario_id, acao, descricao)
    VALUES (1, 'ATUALIZAR_STATUS', CONCAT('Status do modelo ', p_modelo_id, ' atualizado para ', p_novo_status));
END$$
DELIMITER ;

-- Procedure para fechar ticket
DELIMITER $$
CREATE PROCEDURE FecharTicket(
    IN p_ticket_id INT,
    IN p_usuario_id INT
)
BEGIN
    UPDATE tickets_suporte 
    SET status = 'fechado',
        data_atualizacao = CURRENT_TIMESTAMP
    WHERE id = p_ticket_id;
    
    INSERT INTO mensagens_tickets (ticket_id, usuario_id, mensagem, tipo)
    VALUES (p_ticket_id, p_usuario_id, 'Ticket fechado pelo sistema.', 'suporte');
END$$
DELIMITER ;

-- =============================================================================
-- TRIGGERS PARA AUDITORIA
-- =============================================================================

-- Trigger para log de atualizações em modelos
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

-- Trigger para log de novos jobs
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
-- ATUALIZAR USUÁRIO ADMIN COM INFORMAÇÕES COMPLETAS
-- =============================================================================

UPDATE usuarios SET 
    telefone = '(61) 3333-3333',
    empresa = 'Stars Models Agency'
WHERE id = 1;

-- =============================================================================
-- VERIFICAR E MOSTRAR RESUMO DO BANCO
-- =============================================================================

SELECT 
    'Banco Stars Models criado com sucesso!' as mensagem,
    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
    (SELECT COUNT(*) FROM modelos) as total_modelos,
    (SELECT COUNT(*) FROM jobs) as total_jobs,
    (SELECT COUNT(*) FROM noticias) as total_noticias,
    (SELECT COUNT(*) FROM faq) as total_faq,
    (SELECT COUNT(*) FROM tickets_suporte) as total_tickets;

-- Mostrar todas as tabelas criadas
SHOW TABLES;