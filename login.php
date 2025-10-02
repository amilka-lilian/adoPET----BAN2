<?php
// AdoPET/login.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Login na AdoPET';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(); 
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['user_name'] = $usuario['nome'];
        $_SESSION['user_type'] = $usuario['tipo_usuario'];
        set_flash_message('Bem-vindo(a), ' . $usuario['nome'] . '!', 'success');
        header("Location: dashboard.php");
        exit();
    } else {
        set_flash_message('Email ou senha inválidos.', 'danger');
        header("Location: login.php");
        exit();
    }
}

include 'templates/header.php';
?>
<section class="form-section">
    <h2>Acesse sua conta</h2>
    <form method="POST" action="login.php">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit" class="btn-primary">Entrar</button>
    </form>
    <p>Ainda não tem conta? <a href="cadastro.php">Cadastre-se aqui!</a></p>
</section>

<?php include 'templates/footer.php'; ?>
