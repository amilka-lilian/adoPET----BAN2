<?php
// AdoPET/logout.php
session_start();
header('Content-Type: text/html; charset=utf-8');
$_SESSION['flash_message'] = ['message' => 'Você foi desconectado(a).', 'type' => 'info'];

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: index.php");
exit();
?>