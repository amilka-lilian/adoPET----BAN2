<?php
// AdoPET/gerenciar_interesse.php
require_once 'helpers.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

function log_historico_adocao($conn, $id_animal, $campo, $valor_anterior, $valor_novo, $id_usuario) {
    if (!$conn) return; 

    $stmt = $conn->prepare("
        INSERT INTO historico_adocao (id_animal, campo_alterado, valor_anterior, valor_alterado, id_usuario)
        VALUES (?, ?, ?, ?, ?)
    ");

    if ($stmt) {
        $stmt->bind_param("isssi", $id_animal, $campo, $valor_anterior, $valor_novo, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}


$interesse_id = $_POST['interesse_id'] ?? null;
$action = $_POST['action'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$interesse_id || !$action) {
    set_flash_message('Requisição inválida.', 'danger');
    header('Location: dashboard.php');
    exit();
}

$conn = get_db_connection();

$stmt = $conn->prepare("
    SELECT ia.id_interesses_adocao, ia.id_animal, ia.id_interessado, ia.status AS status_anterior, a.id_usuario 
    FROM interesses_adocao ia 
    JOIN animais a ON ia.id_animal = a.id_animais 
    WHERE ia.id_interesses_adocao = ?
");
$stmt->bind_param("i", $interesse_id);
$stmt->execute();
$result = $stmt->get_result();
$interesse_info = $result->fetch_assoc();
$stmt->close();

if (!$interesse_info || $interesse_info['id_usuario'] != $user_id) {
    set_flash_message('Você não tem permissão para gerenciar este interesse.', 'danger');
    $conn->close();
    header('Location: dashboard.php');
    exit();
}

$status_anterior = $interesse_info['status_anterior'];
$animal_id = $interesse_info['id_animal'];

$new_status = '';
$message = '';

switch ($action) {
    case 'aprovar':
        $new_status = 'Aprovado';
        $message = 'Interesse aprovado!';
        break;

    case 'rejeitar':
        $new_status = 'Rejeitado';
        $message = 'Interesse rejeitado.';
        break;

    case 'marcar_adotado':
        $conn->begin_transaction();
        try {

            $stmt_animal = $conn->prepare("UPDATE animais SET disponivel = FALSE WHERE id_animais = ?");
            $stmt_animal->bind_param("i", $animal_id);
            $stmt_animal->execute();
            $stmt_animal->close();
            
            log_historico_adocao($conn, $animal_id, 'disponivel', 'TRUE', 'FALSE', $user_id);

            $stmt_interesse = $conn->prepare("UPDATE interesses_adocao SET status = 'Adotado' WHERE id_interesses_adocao = ?");
            $stmt_interesse->bind_param("i", $interesse_id);
            $stmt_interesse->execute();
            $stmt_interesse->close();
            
            log_historico_adocao($conn, $animal_id, 'status_interesse_final', $status_anterior, 'Adotado', $user_id);

            $stmt_adocao = $conn->prepare("INSERT INTO adocoes (id_animal, id_usuario, observacoes) VALUES (?, ?, ?)");
            $observacoes = 'Adoção registrada via painel';
            $stmt_adocao->bind_param("iis", $animal_id, $interesse_info['id_interessado'], $observacoes);
            $stmt_adocao->execute();
            $stmt_adocao->close();

            $conn->commit();
            set_flash_message('Animal marcado como adotado e adoção registrada com sucesso!', 'success');
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            set_flash_message('Erro ao registrar adoção: ' . $exception->getMessage(), 'danger');
        }
        $conn->close();
        header('Location: dashboard.php');
        exit();
}

if (!empty($new_status)) {
    $stmt = $conn->prepare("UPDATE interesses_adocao SET status = ? WHERE id_interesses_adocao = ?");
    $stmt->bind_param("si", $new_status, $interesse_id);
    
    if ($stmt->execute()) {
        log_historico_adocao(
            $conn, 
            $animal_id, 
            'status_interesse', 
            $status_anterior, 
            $new_status, 
            $user_id
        );
        set_flash_message($message, 'success');
    } else {
        set_flash_message('Erro ao atualizar status.', 'danger');
    }
    $stmt->close();
}

$conn->close();
header('Location: dashboard.php');
exit();