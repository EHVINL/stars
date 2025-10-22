<?php
session_start();

// =============================================
// CONFIGURAÇÕES DO BANCO DE DADOS - XAMPP
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'stars_models');
define('DB_USER', 'root');      // Padrão do XAMPP
define('DB_PASS', '');          // Senha vazia no XAMPP padrão

// =============================================
// CONEXÃO COM O BANCO DE DADOS
// =============================================
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    // Erro detalhado (remove em produção)
    die("🚨 ERRO NA CONEXÃO COM O BANCO: " . $e->getMessage() . 
        "<br>Verifique:<br>" .
        "✓ Banco 'stars_models' existe?<br>" .
        "✓ Usuário: " . DB_USER . "<br>" .
        "✓ Servidor MySQL está rodando?");
}

// =============================================
// FUNÇÕES DE AUTENTICAÇÃO
// =============================================

/**
 * Verifica se usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica se usuário é administrador
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Verifica se usuário é modelo
 */
function isModelo() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'modelo';
}

/**
 * Verifica se usuário é cliente
 */
function isCliente() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'cliente';
}

/**
 * Retorna nome do usuário logado
 */
function getUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Visitante';
}

/**
 * Retorna ID do usuário logado
 */
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Retorna email do usuário logado
 */
function getUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

/**
 * Retorna tipo do usuário logado
 */
function getUserType() {
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

// =============================================
// FUNÇÕES ÚTEIS DO SISTEMA
// =============================================

/**
 * Redireciona para uma página com mensagem opcional
 */
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['flash_message'] = $message;
    }
    header("Location: $url");
    exit;
}

/**
 * Exibe mensagem flash e remove da sessão
 */
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Formata data para formato brasileiro
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return '-';
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Sanitiza dados para prevenir SQL Injection e XSS
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Gera senha hash
 */
function generateHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica senha
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Verifica se email é válido
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// =============================================
// FUNÇÕES ESPECÍFICAS DO SISTEMA
// =============================================

/**
 * Obtém estatísticas para dashboard
 */
function getDashboardStats($pdo) {
    $stats = [];
    
    try {
        // Total de usuários
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $stats['total_usuarios'] = $stmt->fetch()['total'];
        
        // Total de modelos ativos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'ativo'");
        $stats['total_modelos'] = $stmt->fetch()['total'];
        
        // Total de jobs abertos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'aberto'");
        $stats['total_jobs'] = $stmt->fetch()['total'];
        
        // Novas mensagens de contato
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM contatos WHERE status = 'novo'");
        $stats['novos_contatos'] = $stmt->fetch()['total'];
        
        // Modelos pendentes
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM modelos WHERE status = 'pendente'");
        $stats['modelos_pendentes'] = $stmt->fetch()['total'];
        
    } catch (PDOException $e) {
        // Em caso de erro, retorna valores zerados
        $stats = [
            'total_usuarios' => 0,
            'total_modelos' => 0,
            'total_jobs' => 0,
            'novos_contatos' => 0,
            'modelos_pendentes' => 0
        ];
    }
    
    return $stats;
}

/**
 * Obtém dados do modelo logado
 */
function getModeloData($pdo, $usuario_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM modelos WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Verifica se usuário tem perfil de modelo completo
 */
function hasModeloProfile($pdo, $usuario_id) {
    $modelo = getModeloData($pdo, $usuario_id);
    return $modelo && !empty($modelo['tipo_profissao']);
}

// =============================================
// CONFIGURAÇÕES DO SITE
// =============================================
define('SITE_NAME', 'Stars Models Agency');
define('SITE_URL', 'http://localhost/stars_models');
define('SITE_EMAIL', 'contato@starsmodels.com');
define('UPLOAD_PATH', 'uploads/');

// =============================================
// INICIALIZAÇÕES
// =============================================

// Timezone do Brasil
date_default_timezone_set('America/Sao_Paulo');

// Verifica se precisa fazer instalação
function needsInstallation($pdo) {
    try {
        $pdo->query("SELECT 1 FROM usuarios LIMIT 1");
        return false;
    } catch (PDOException $e) {
        return true;
    }
}

// Se precisar de instalação, redireciona
if (needsInstallation($pdo) && basename($_SERVER['PHP_SELF']) !== 'install.php') {
    // header('Location: install.php');
    // exit;
    // (Comentei pra não bugar, mas se precisar de instalação automática, descomenta)
}

?>