<?php
// admin/actions/save_job.php
require_once '../../includes/config.php';

if (!isAdmin()) {
    $_SESSION['flash_message'] = 'Acesso negado.';
    header('Location: ../jobs.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'Método não permitido.';
    header('Location: ../jobs.php');
    exit;
}

// Coletar dados
$dados = [
    'titulo' => sanitize($_POST['titulo'] ?? ''),
    'descricao' => sanitize($_POST['descricao'] ?? ''),
    'tipo_modelo' => sanitize($_POST['tipo_modelo'] ?? ''),
    'localizacao' => sanitize($_POST['localizacao'] ?? ''),
    'remuneracao' => $_POST['remuneracao'] ? (float)$_POST['remuneracao'] : null,
    'data_evento' => !empty($_POST['data_evento']) ? $_POST['data_evento'] : null,
    'cliente_id' => $_POST['cliente_id'] ? (int)$_POST['cliente_id'] : null,
    'vagas' => $_POST['vagas'] ? (int)$_POST['vagas'] : 1,
    'requisitos' => sanitize($_POST['requisitos'] ?? ''),
    'beneficios' => sanitize($_POST['beneficios'] ?? ''),
    'status' => sanitize($_POST['status'] ?? 'aberto')
];

$action = isset($_POST['id']) ? 'edit' : 'add';
$job_id = isset($_POST['id']) ? (int)$_POST['id'] : null;

try {
    if ($action === 'add') {
        // Criar nova vaga
        $stmt = $pdo->prepare("
            INSERT INTO jobs 
            (titulo, descricao, tipo_modelo, localizacao, remuneracao, data_evento, cliente_id, vagas, requisitos, beneficios, status, data_publicacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $dados['titulo'],
            $dados['descricao'],
            $dados['tipo_modelo'],
            $dados['localizacao'],
            $dados['remuneracao'],
            $dados['data_evento'],
            $dados['cliente_id'],
            $dados['vagas'],
            $dados['requisitos'],
            $dados['beneficios'],
            $dados['status']
        ]);

        $_SESSION['flash_message'] = 'Vaga criada com sucesso!';

    } else {
        // Editar vaga existente
        $stmt = $pdo->prepare("
            UPDATE jobs 
            SET titulo = ?, descricao = ?, tipo_modelo = ?, localizacao = ?, remuneracao = ?, 
                data_evento = ?, cliente_id = ?, vagas = ?, requisitos = ?, beneficios = ?, status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $dados['titulo'],
            $dados['descricao'],
            $dados['tipo_modelo'],
            $dados['localizacao'],
            $dados['remuneracao'],
            $dados['data_evento'],
            $dados['cliente_id'],
            $dados['vagas'],
            $dados['requisitos'],
            $dados['beneficios'],
            $dados['status'],
            $job_id
        ]);

        $_SESSION['flash_message'] = 'Vaga atualizada com sucesso!';
    }

    header('Location: ../jobs.php');
    exit;

} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Erro: ' . $e->getMessage();
    $_SESSION['form_data'] = $dados;
    
    if ($action === 'add') {
        header('Location: ../job_form.php?action=add');
    } else {
        header("Location: ../job_form.php?action=edit&id={$job_id}");
    }
    exit;
}
?>