<?php
// AdoPET/helpers.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Define uma mensagem flash que será exibida na próxima página.
 *
 * @param string $message A mensagem a ser exibida.
 * @param string $type O tipo da mensagem (ex: 'success', 'danger', 'warning').
 */
function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

/**
 * Gera um token CSRF e o armazena na sessão.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Gera e exibe o campo input hidden com o token CSRF.
 */
function csrf_input_field() {
    generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}


/**
 * Valida o token CSRF enviado via POST com o armazenado na sessão.
 */
function validate_csrf_token() {
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        session_destroy();
        die("Erro de validação CSRF: Ação não autorizada.");
    }
    
    unset($_SESSION['csrf_token']);
}