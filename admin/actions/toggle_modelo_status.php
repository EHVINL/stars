<?php
// admin/actions/toggle_modelo_status.php
require_once '../../includes/config.php';

if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

$modelo_id = (int)$_GET['id'];
$status = $_GET['status'];

try {
    $stmt = $pdo->prepare("UPDATE modelos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $modelo_id]);
    
    echo json_encode(['success' => true, 'message' => 'Status alterado com sucesso']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar status: ' . $e->getMessage()]);
}
?>