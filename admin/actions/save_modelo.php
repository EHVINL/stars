<?php
// admin/actions/save_modelo.php
require_once '../../includes/config.php';

if (!isAdmin()) {
    $_SESSION['flash_message'] = 'Acesso negado. Apenas administradores podem gerenciar modelos.';
    header('Location: ../modelos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'Método não permitido.';
    header('Location: ../modelos.php');
    exit;
}

// Coletar e sanitizar dados
$dados = [
    'nome' => sanitize($_POST['nome'] ?? ''),
    'email' => sanitize($_POST['email'] ?? ''),
    'telefone' => sanitize($_POST['telefone'] ?? ''),
    'tipo_profissao' => sanitize($_POST['tipo_profissao'] ?? ''),
    'altura' => $_POST['altura'] ? (float)$_POST['altura'] : null,
    'idade' => $_POST['idade'] ? (int)$_POST['idade'] : null,
    'peso' => $_POST['peso'] ? (float)$_POST['peso'] : null,
    'calcado' => sanitize($_POST['calcado'] ?? ''),
    'busto' => $_POST['busto'] ? (float)$_POST['busto'] : null,
    'cintura' => $_POST['cintura'] ? (float)$_POST['cintura'] : null,
    'quadril' => $_POST['quadril'] ? (float)$_POST['quadril'] : null,
    'experiencia' => sanitize($_POST['experiencia'] ?? ''),
    'formacao' => sanitize($_POST['formacao'] ?? ''),
    'habilidades' => sanitize($_POST['habilidades'] ?? ''),
    'cidade' => sanitize($_POST['cidade'] ?? ''),
    'estado' => sanitize($_POST['estado'] ?? ''),
    'status' => sanitize($_POST['status'] ?? 'ativo')
];

$action = isset($_POST['id']) ? 'edit' : 'add';
$modelo_id = isset($_POST['id']) ? (int)$_POST['id'] : null;

try {
    $pdo->beginTransaction();

    if ($action === 'add') {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$dados['email']]);
        
        if ($stmt->fetch()) {
            throw new Exception('Este email já está cadastrado no sistema.');
        }

        // 1. Criar usuário
        $senha_temporaria = password_hash('modelo123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, tipo, telefone, created_at) 
            VALUES (?, ?, ?, 'modelo', ?, NOW())
        ");
        $stmt->execute([
            $dados['nome'],
            $dados['email'], 
            $senha_temporaria,
            $dados['telefone']
        ]);
        
        $usuario_id = $pdo->lastInsertId();

        // 2. Criar perfil de modelo
        $stmt = $pdo->prepare("
            INSERT INTO modelos 
            (usuario_id, altura, busto, quadril, tipo_profissao, experiencia, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $usuario_id,
            $dados['altura'],
            $dados['busto'],
            $dados['quadril'], 
            $dados['tipo_profissao'],
            $dados['experiencia'],
            $dados['status']
        ]);

        $_SESSION['flash_message'] = 'Modelo cadastrado com sucesso! Senha temporária: modelo123';

    } else {
        // Edição de modelo existente
        $modelo_id = $_POST['id'];

        // 1. Buscar usuario_id do modelo
        $stmt = $pdo->prepare("SELECT usuario_id FROM modelos WHERE id = ?");
        $stmt->execute([$modelo_id]);
        $modelo = $stmt->fetch();
        
        if (!$modelo) {
            throw new Exception('Modelo não encontrado.');
        }

        $usuario_id = $modelo['usuario_id'];

        // 2. Atualizar dados do usuário
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome = ?, email = ?, telefone = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $dados['telefone'], 
            $usuario_id
        ]);

        // 3. Atualizar dados do modelo
        $stmt = $pdo->prepare("
            UPDATE modelos 
            SET altura = ?, busto = ?, quadril = ?, tipo_profissao = ?, 
                experiencia = ?, status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([
            $dados['altura'],
            $dados['busto'],
            $dados['quadril'],
            $dados['tipo_profissao'],
            $dados['experiencia'], 
            $dados['status'],
            $modelo_id
        ]);

        $_SESSION['flash_message'] = 'Modelo atualizado com sucesso!';
    }

    $pdo->commit();
    
    header('Location: ../modelos.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_message'] = 'Erro: ' . $e->getMessage();
    $_SESSION['form_data'] = $dados;
    
    if ($action === 'add') {
        header('Location: ../modelo_form.php?action=add');
    } else {
        header("Location: ../modelo_form.php?action=edit&id={$modelo_id}");
    }
    exit;
}
?>