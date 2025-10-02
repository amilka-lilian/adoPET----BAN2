<?php
// AdoPET/adicionar_animal.php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once 'db.php';
$page_title = 'Adicionar Novo Animal';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para adicionar um animal.', 'warning');
    header('Location: login.php');
    exit();
}

$conn = get_db_connection();

$especies = [];
$stmt_especie = $conn->prepare("SELECT id_especie, nome FROM especies");
$stmt_especie->execute();
$result_especie = $stmt_especie->get_result();
while ($row = $result_especie->fetch_assoc()) {
    $especies[] = $row;
}
$stmt_especie->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foto_url = 'default_animal.jpg';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp_name = $_FILES['foto']['tmp_name'];
        $foto_name = basename($_FILES['foto']['name']);
        $file_extension = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid('animal_', true) . '.' . $file_extension;
            $upload_path = 'uploads/' . $filename;

            if (move_uploaded_file($foto_tmp_name, $upload_path)) {
                $foto_url = $filename;
            } else {
                set_flash_message('Erro ao mover o arquivo para a pasta "uploads".', 'danger');
            }
        } else {
            set_flash_message('Tipo de arquivo não permitido. Use: png, jpg, jpeg, gif.', 'danger');
        }
    }

    $nome = $_POST['nome'];
    $id_especie = $_POST['especie'];
    $raca = $_POST['raca'] ?: 'Não definida';
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];
    $porte = $_POST['porte'];
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $descricao = $_POST['descricao'];
    $id_usuario = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO animais (nome, id_especie, raca, idade, genero, porte, castrado, vacinado, vermifugado, descricao, foto_url, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssiiisss", $nome, $id_especie, $raca, $idade, $genero, $porte, $castrado, $vacinado, $vermifugado, $descricao, $foto_url, $id_usuario);

    if ($stmt->execute()) {
        set_flash_message('Animal cadastrado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        error_log('Erro no DB: ' . $stmt->error);
        set_flash_message('Erro ao cadastrar animal: ' . $stmt->error, 'danger');
    }
}

include 'templates/header.php';
?>

<section class="form-section">
    <h2>Adicionar Novo Animal</h2>
    <form method="POST" action="adicionar_animal.php" enctype="multipart/form-data">
        <label for="nome">Nome do Animal:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="especie">Espécie:</label>
        <select name="especie" id="especie" required>
            <option value="">Selecione</option>
            <?php foreach ($especies as $esp): ?>
                <option value="<?php echo $esp['id_especie']; ?>"><?php echo htmlspecialchars($esp['nome']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="raca">Raça:</label>
        <input type="text" id="raca" name="raca" placeholder="Ex: SRD">

        <label for="idade">Idade:</label>
        <select name="idade" id="idade" required>
            <option value="">Selecione</option>
            <option value="Filhote">Filhote</option>
            <option value="Adulto">Adulto</option>
            <option value="Idoso">Idoso</option>
        </select>

        <label for="genero">Gênero:</label>
        <select name="genero" id="genero" required>
            <option value="">Selecione</option>
            <option value="Macho">Macho</option>
            <option value="Fêmea">Fêmea</option>
        </select>

        <label for="porte">Porte:</label>
        <select name="porte" id="porte" required>
            <option value="">Selecione</option>
            <option value="Pequeno">Pequeno</option>
            <option value="Medio">Médio</option>
            <option value="Grande">Grande</option>
        </select>

        <div class="checkbox-group">
            <label><input type="checkbox" name="castrado"> Castrado</label>
            <label><input type="checkbox" name="vacinado"> Vacinado</label>
            <label><input type="checkbox" name="vermifugado"> Vermifugado</label>
        </div>

        <label for="descricao">Descrição e Personalidade:</label>
        <textarea id="descricao" name="descricao" rows="6" required></textarea>

        <label for="foto">Foto do Animal:</label>
        <input type="file" id="foto" name="foto" accept="image/*">
        <small>Tipos permitidos: PNG, JPG, JPEG, GIF.</small>

        <button type="submit" class="btn-primary">Cadastrar Animal</button>
    </form>
</section>

<?php include 'templates/footer.php'; ?>