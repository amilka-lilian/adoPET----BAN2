<?php
// AdoPET/editar_perfil.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Editar Perfil';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para editar seu perfil.', 'warning');
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = ($_SESSION['user_type'] == 'ONG') ? ($_POST['descricao'] ?? null) : null;

    // Endereço
    $rua = $_POST['rua'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Telefone
    $ddd = $_POST['ddd'] ?? '';
    $numero_tel = $_POST['numero_telefone'] ?? '';

    // Atualizar usuário
    if ($_SESSION['user_type'] == 'ONG') {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, descricao = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssi", $nome, $descricao, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nome, $user_id);
    }
    $stmt->execute();
    $stmt->close();

    // Atualizar endereço
    $stmt_end = $conn->prepare("UPDATE enderecos SET rua = ?, numero = ?, bairro = ?, cep = ?, cidade = ?, estado = ? WHERE id_endereco = (SELECT id_endereco FROM usuarios WHERE id_usuario = ?)");
    $stmt_end->bind_param("ssssssi", $rua, $numero, $bairro, $cep, $cidade, $estado, $user_id);
    $stmt_end->execute();
    $stmt_end->close();

    // Atualizar telefone
    $stmt_tel = $conn->prepare("UPDATE telefones SET ddd = ?, numero = ? WHERE id_usuario = ?");
    $stmt_tel->bind_param("ssi", $ddd, $numero_tel, $user_id);
    $stmt_tel->execute();
    $stmt_tel->close();

    $_SESSION['user_name'] = $nome;
    set_flash_message('Perfil atualizado com sucesso!', 'success');
    header('Location: dashboard.php');
    exit();
}

// Buscar dados
$stmt = $conn->prepare("SELECT u.*, e.* FROM usuarios u LEFT JOIN enderecos e ON u.id_endereco = e.id_endereco WHERE u.id_usuario = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Buscar telefone
$telefone = ['ddd' => '', 'numero' => ''];
$stmt_tel = $conn->prepare("SELECT ddd, numero FROM telefones WHERE id_usuario = ?");
$stmt_tel->bind_param("i", $user_id);
$stmt_tel->execute();
$result_tel = $stmt_tel->get_result();
if ($tel = $result_tel->fetch_assoc()) {
    $telefone = $tel;
}
$stmt_tel->close();
$conn->close();
?>

<section class="form-section">
    <h2>Editar Perfil</h2>
    <form method="POST" action="editar_perfil.php">
        <label for="nome">Nome/Nome da ONG:</label>
        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($usuario['nome']); ?>">

        <label for="email">E-mail (não editável):</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>

        <h3>Endereço</h3>
        <label for="rua">Rua:</label>
        <input type="text" id="rua" name="rua" required value="<?php echo htmlspecialchars($usuario['rua'] ?? ''); ?>">

        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" required value="<?php echo htmlspecialchars($usuario['numero'] ?? ''); ?>">

        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" required value="<?php echo htmlspecialchars($usuario['bairro'] ?? ''); ?>">

        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required value="<?php echo htmlspecialchars($usuario['cep'] ?? ''); ?>">

        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>">

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" maxlength="2" required value="<?php echo htmlspecialchars($usuario['estado'] ?? ''); ?>">

        <h3>Telefone</h3>
        <label for="ddd">DDD:</label>
        <input type="text" id="ddd" name="ddd" maxlength="3" required value="<?php echo htmlspecialchars($telefone['ddd']); ?>">

        <label for="numero_telefone">Número:</label>
        <input type="text" id="numero_telefone" name="numero_telefone" required value="<?php echo htmlspecialchars($telefone['numero']); ?>">

        <?php if ($_SESSION['user_type'] == 'ONG'): ?>
            <label for="descricao">Descrição da ONG:</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($usuario['descricao'] ?? ''); ?></textarea>
        <?php endif; ?>

        <button type="submit" class="btn-primary">Atualizar Perfil</button>
    </form>
</section>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(document.getElementById('cep'), { mask: '00000-000' });
    IMask(document.getElementById('ddd'), { mask: '000' });
    IMask(document.getElementById('numero_telefone'), {
        mask: [
            { mask: '0000-0000' },
            { mask: '00000-0000' }
        ]
    });
</script>

<?php include 'templates/footer.php'; ?>
