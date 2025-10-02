<?php
// AdoPET/templates/header.php
header('Content-Type: text/html; charset=utf-8');
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdoPET - <?php echo $page_title ?? 'Bem-vindo'; ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="icon" href="static/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header class="site-header">
        <nav>
            <div class="logo"><a href="index.php">AdoPET</a></div>
            <ul>
                <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Início</a></li>
                <li><a href="animais.php" class="<?php echo ($current_page == 'animais.php') ? 'active' : ''; ?>">Animais Disponíveis</a></li>
                <li><a href="sobre.php" class="<?php echo ($current_page == 'sobre.php') ? 'active' : ''; ?>">Sobre Nós</a></li>
                <li><a href="contato.php" class="<?php echo ($current_page == 'contato.php') ? 'active' : ''; ?>">Contato</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <a href="#" class="user-name">Olá, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</a>
                        <ul class="dropdown-content">
                            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Meu Painel</a></li>
                            <li><a href="editar_perfil.php"><i class="fas fa-user-edit"></i> Editar Perfil</a></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="cadastro.php" class="btn-nav">Cadastre-se</a></li>
                    <li><a href="login.php" class="btn-nav">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <ul class="flashes">
                <li class="<?php echo $_SESSION['flash_message']['type']; ?>">
                    <?php echo $_SESSION['flash_message']['message']; ?>
                </li>
            </ul>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>