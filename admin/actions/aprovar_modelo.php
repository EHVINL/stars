<?php
// admin/actions/aprovar_modelo.php
require_once '../../includes/config.php';

if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$modelo_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("UPDATE modelos SET status = 'ativo' WHERE id = ?");
    $stmt->execute([$modelo_id]);
    
    echo json_encode(['success' => true, 'message' => 'Modelo aprovado com sucesso']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao aprovar modelo: ' . $e->getMessage()]);
}
?>