<?php
// AdoPET/excluir_usuario.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash_message('Método inválido para exclusão.', 'danger');
    header("Location: dashboard_ou_perfil.php"); 
    exit();
}

$id_usuario_a_excluir = $_POST['id_usuario'] ?? null;

if (empty($id_usuario_a_excluir) ) {
    set_flash_message('ID de usuário inválido para exclusão.', 'danger');
    header("Location: perfil.php");
    exit();
}

$conn = get_db_connection();
$conn->begin_transaction();

try {
    $stmt_tel = $conn->prepare("DELETE FROM telefones WHERE id_usuario = ?");
    $stmt_tel->bind_param("i", $id_usuario_a_excluir);
    $stmt_tel->execute();
    $stmt_tel->close();
    $id_endereco = null;
    $stmt_end_id = $conn->prepare("SELECT id_endereco FROM usuarios WHERE id_usuario = ?");
    $stmt_end_id->bind_param("i", $id_usuario_a_excluir);
    $stmt_end_id->execute();
    $result = $stmt_end_id->get_result();

    if ($row = $result->fetch_assoc()) {
        $id_endereco = $row['id_endereco'];
    }

    $stmt_end_id->close();
    $stmt_user = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt_user->bind_param("i", $id_usuario_a_excluir);
    $stmt_user->execute();
    $stmt_user->close();

    if (!empty($id_endereco)) {
        $stmt_end = $conn->prepare("DELETE FROM enderecos WHERE id_endereco = ?");
        $stmt_end->bind_param("i", $id_endereco);
        $stmt_end->execute();
        $stmt_end->close();
    }

    $conn->commit();
        set_flash_message('Usuário excluído com sucesso.', 'success');
        header("Location: lista_de_usuarios.php");

    exit();

} catch (Exception $e) {
    $conn->rollback(); 
    set_flash_message('Erro ao excluir usuário: ' . $e->getMessage(), 'danger');
    header("Location: perfil.php");
    exit();
} finally {
    $conn->close();
}