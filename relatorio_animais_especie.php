<?php
// AdoPET/relatorio_animais_especie.php
require_once 'db.php';
session_start();
$page_title = 'Relatório geral de animais por responsável';
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

<div class="relatorio-page-wrapper">

    <?php include 'templates/header.php'; ?>

    <main>
        <?php
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = ['message' => 'Por favor, faça login para acessar esta página.', 'type' => 'danger'];
            header('Location: login.php');
            exit();
        }

        $conn = get_db_connection();

        $sql = "SELECT
                    e.nome AS nome_especie,
                    u.nome AS nome_responsavel,
                    tu.descricao AS tipo_responsavel,
                    COUNT(a.id) AS total_animais
                FROM
                    animais AS a
                INNER JOIN
                    especies AS e ON a.id_especie = e.id
                INNER JOIN
                    usuarios AS u ON a.id_usuario = u.id
                INNER JOIN
                    tipo_usuario AS tu ON u.id_tipo_usuario = tu.id
                GROUP BY
                    e.nome, u.nome, tu.descricao
                ORDER BY
                    e.nome, u.nome";

        $result = $conn->query($sql);
        ?>

        <section class="container" style="padding-top: 40px; padding-bottom: 40px;">
            <h2>Relatório geral de animais por responsável</h2>
            <p style="margin-bottom: 20px;">Este relatório mostra a quantidade de animais por espécie cadastrados por cada responsável (ONGs e Pessoas Físicas).</p>

            <table border="1" style="width:90%; border-collapse: collapse; margin: auto;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; text-align: left;">Espécie</th>
                        <th style="padding: 10px; text-align: left;">Nome do Responsável</th>
                        <th style="padding: 10px; text-align: left;">Tipo</th>
                        <th style="padding: 10px; text-align: right;">Total de Animais</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['nome_especie']); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['nome_responsavel']); ?></td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($row['tipo_responsavel']); ?></td>

                                <td style="padding: 8px; text-align: right;"><?php echo $row['total_animais']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="padding: 8px; text-align: center;">Nenhum animal foi encontrado no banco de dados.</td>
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

</div>

</body>
</html>