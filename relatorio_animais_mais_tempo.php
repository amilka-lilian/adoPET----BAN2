<?php
// AdoPET/relatorio_animais_mais_tempo.php
require_once 'db.php';
session_start();
$page_title = 'Relatório: Animais sem interesse de adoção';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - AdoPET</title>
    <link rel="stylesheet" href="static/css/style.css"> 
</head>
<body>

<div class="relatorio-page-wrapper"> <?php include 'templates/header.php'; ?>

    <main> <?php
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = ['message' => 'Por favor, faça login para acessar esta página.', 'type' => 'danger'];
            header('Location: login.php');
            exit();
        }

        $conn = get_db_connection();

        $sql = "SELECT 
                    a.nome AS nome_animal, 
                    e.nome AS nome_especie, 
                    u.nome AS nome_ong, 
                    a.data_cadastro, 
                    a.raca
                FROM animais AS a
                INNER JOIN especies AS e ON a.id_especie = e.id
                INNER JOIN usuarios AS u ON a.id_usuario = u.id
                INNER JOIN tipo_usuario AS tu ON u.id_tipo_usuario = tu.id
                WHERE tu.descricao = 'ONG'
                AND a.id NOT IN (SELECT id_animal FROM interesses_adocao)
                ORDER BY a.data_cadastro ASC
                LIMIT 3";

        $result = $conn->query($sql);
        ?>

        <section class="container" style="padding-top: 40px; padding-bottom: 40px;">
            <h2>Relatório: Animais que estão esperando há mais tempo por uma família</h2>
            <p style="margin-bottom: 20px;">
                Este relatório mostra os 3 animais mais antigos no sistema (cadastrados por ONGs) que ainda não receberam nenhuma manifestação de interesse.
            </p>

            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; text-align: left;">ONG</th>
                        <th style="padding: 10px; text-align: left;">Espécie</th>
                        <th style="padding: 10px; text-align: left;">Nome do Animal</th>
                        <th style="padding: 10px; text-align: left;">Raça</th>
                        <th style="padding: 10px; text-align: left;">Data de Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['nome_ong']); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['nome_especie']); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['nome_animal']); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['raca']); ?></td>
                                <td style="padding: 8px;">
                                    <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_cadastro']))); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 8px; text-align: center;">
                                Todos os animais cadastrados por ONGs já receberam ao menos um interesse!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php
    $conn->close();
    include 'templates/footer.php';
    ?>

</div> </body>
</html>