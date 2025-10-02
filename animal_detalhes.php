<?php
// AdoPET/animal_detalhes.php
require_once 'db.php';
header('Content-Type: text/html; charset=utf-8');
session_start();

$animal_id = $_GET['id'] ?? 0;
if (!$animal_id) {
    header("Location: animais.php");
    exit();
}

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT a.*, u.nome as nome_doador, u.email as email_doador, e.nome as nome_especie 
                        FROM animais a 
                        JOIN usuarios u ON a.id_usuario = u.id_usuario 
                        JOIN especies e ON a.id_especie = e.id_especie 
                        WHERE a.id_animais = ?");
$stmt->bind_param("i", $animal_id);
$stmt->execute();
$result = $stmt->get_result();
$animal = $result->fetch_assoc();
$stmt->close();

$telefone_doador = '';
$tel_stmt = $conn->prepare("SELECT CONCAT(ddd, ' ', numero) as telefone FROM telefones WHERE id_usuario = ?");
$tel_stmt->bind_param("i", $animal['id_usuario']);
$tel_stmt->execute();
$tel_result = $tel_stmt->get_result();
if ($tel = $tel_result->fetch_assoc()) {
    $telefone_doador = $tel['telefone'];
}
$tel_stmt->close();

$conn->close();

if (!$animal) {
    $_SESSION['flash_message'] = ['message' => 'Animal não encontrado.', 'type' => 'danger'];
    header("Location: animais.php");
    exit();
}

$page_title = htmlspecialchars($animal['nome']) . ' - Detalhes';
include 'templates/header.php';
?>

<section class="animal-detalhes">
    <div class="detalhes-container">
        <div class="detalhes-coluna-esquerda">
            <div class="detalhes-imagem">
                <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">
            </div>
            <div class="info-card descricao-animal">
                <h3>Sobre mim</h3>
                <p><?php echo nl2br(htmlspecialchars($animal['descricao'])); ?></p>
            </div>
        </div>
        <div class="detalhes-info">
            <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
            <p><strong>Espécie:</strong> <?php echo htmlspecialchars($animal['nome_especie']); ?> - 
               <strong>Raça:</strong> <?php echo htmlspecialchars($animal['raca'] ?: 'Não informada'); ?></p>

            <div class="info-card">
                <h3>Detalhes</h3>
                <div class="caracteristicas-grid">
                    <div class="caracteristica-item">
                        <strong>Idade</strong>
                        <p><?php echo htmlspecialchars($animal['idade']); ?></p>
                    </div>
                    <div class="caracteristica-item">
                        <strong>Gênero</strong>
                        <p><?php echo htmlspecialchars($animal['genero']); ?></p>
                    </div>
                    <div class="caracteristica-item">
                        <strong>Porte</strong>
                        <p><?php echo htmlspecialchars($animal['porte']); ?></p>
                    </div>
                </div>

                <h3 style="margin-top: 20px;">Saúde</h3>
                <div class="saude-badges">
                    <p><strong>Castrado:</strong> <?php echo $animal['castrado'] ? 'Sim' : 'Não'; ?></p>
                    <p><strong>Vacinado:</strong> <?php echo $animal['vacinado'] ? 'Sim' : 'Não'; ?></p>
                    <p><strong>Vermifugado:</strong> <?php echo $animal['vermifugado'] ? 'Sim' : 'Não'; ?></p>
                </div>
            </div>

            <div class="info-card">
                <h3><?php echo ($animal['status'] === 'Ativo') ? 'Quero Adotar!' : 'Status'; ?></h3>

                <?php if ($animal['status'] === 'Ativo'): ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <p><strong>Doador(a):</strong> <?php echo htmlspecialchars($animal['nome_doador']); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($animal['email_doador']); ?>"><?php echo htmlspecialchars($animal['email_doador']); ?></a></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($telefone_doador ?: 'Não informado'); ?></p>

                        <?php if ($_SESSION['user_type'] == 'Pessoa Fisica' && $_SESSION['user_id'] != $animal['id_usuario']): ?>
                            <div class="manifestar-interesse-form">
                                <form action="manifestar_interesse.php" method="POST">
                                    <input type="hidden" name="animal_id" value="<?php echo $animal['id_animais']; ?>">
                                    <label for="mensagem">Deixe uma mensagem para o doador:</label>
                                    <textarea id="mensagem" name="mensagem" rows="4" placeholder="Conte um pouco sobre você e por que gostaria de adotar este animal." required></textarea>
                                    <button type="submit" class="btn-primary">Enviar Interesse</button>
                                </form>
                            </div>
                        <?php elseif ($_SESSION['user_id'] == $animal['id_usuario']): ?>
                            <p class="info-message">Este é um dos seus animais. Acesse o <a href="dashboard.php">painel</a> para gerenciá-lo.</p>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>Faça <a href="login.php">login</a> para ver os dados de contato do doador e manifestar interesse.</p>
                    <?php endif; ?>
                <?php else: ?>
                   <p><strong>Status:</strong> <?php echo htmlspecialchars($animal['status']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

