<?php
// AdoPET/editar_animal.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Editar Animal';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para editar um animal.', 'warning');
    header('Location: login.php');
    exit();
}

$animal_id = $_GET['id'] ?? 0;
if (!$animal_id) {
    header('Location: dashboard.php');
    exit();
}

$conn = get_db_connection();
$user_id = $_SESSION['user_id'];

// Buscar animal
$stmt = $conn->prepare("SELECT * FROM animais WHERE id_animais = ? AND id_usuario = ?");
$stmt->bind_param("ii", $animal_id, $user_id);
$stmt->execute();
$animal = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$animal) {
    set_flash_message('Animal não encontrado ou você não tem permissão para editá-lo.', 'danger');
    header('Location: dashboard.php');
    exit();
}

// Buscar espécies
$especies = [];
$stmt_especie = $conn->prepare("SELECT id_especie, nome FROM especies");
$stmt_especie->execute();
$result_especie = $stmt_especie->get_result();
while ($row = $result_especie->fetch_assoc()) {
    $especies[] = $row;
}
$stmt_especie->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $id_especie = $_POST['especie'];
    $raca = $_POST['raca'] ?: null;
    $idade = $_POST['idade'] ?: null;
    $genero = $_POST['genero'];
    $porte = $_POST['porte'];
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $descricao = $_POST['descricao'];
    $status = $_POST['status']; 

    $foto_url = $animal['foto_url'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            if ($foto_url !== 'default_animal.jpg' && file_exists('uploads/' . $foto_url)) {
                unlink('uploads/' . $foto_url);
            }
            $filename = uniqid('animal_', true) . '.' . $file_extension;
            $upload_path = 'uploads/' . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $foto_url = $filename;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE animais SET nome = ?, id_especie = ?, raca = ?, idade = ?, genero = ?, porte = ?, castrado = ?, vacinado = ?, vermifugado = ?, status = ?, descricao = ?, foto_url = ? WHERE id_animais = ?");
    $stmt->bind_param("sissssssssssi", $nome, $id_especie, $raca, $idade, $genero, $porte, $castrado, $vacinado, $vermifugado, $status, $descricao, $foto_url, $animal_id);

    if ($stmt->execute()) {
        set_flash_message('Animal atualizado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        set_flash_message('Erro ao atualizar animal: ' . $stmt->error, 'danger');
    }
    $stmt->close();
}
$conn->close();
?>

<section class="form-section">
    <h2>Editar Animal: <?php echo htmlspecialchars($animal['nome']); ?></h2>
    <form method="POST" action="editar_animal.php?id=<?php echo $animal['id_animais']; ?>" enctype="multipart/form-data">
        <label for="nome">Nome do Animal:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($animal['nome']); ?>" required>

        <label for="especie">Espécie:</label>
        <select name="especie" id="especie" required>
            <?php foreach ($especies as $esp): ?>
                <option value="<?php echo $esp['id_especie']; ?>" <?php if ($animal['id_especie'] == $esp['id_especie']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($esp['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="raca">Raça:</label>
        <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($animal['raca'] ?? ''); ?>">

        <label for="idade">Idade:</label>
        <select name="idade" id="idade" required>
            <option value="Filhote" <?php if ($animal['idade'] == 'Filhote') echo 'selected'; ?>>Filhote</option>
            <option value="Adulto" <?php if ($animal['idade'] == 'Adulto') echo 'selected'; ?>>Adulto</option>
            <option value="Idoso" <?php if ($animal['idade'] == 'Idoso') echo 'selected'; ?>>Idoso</option>
        </select>

        <label for="genero">Gênero:</label>
        <select name="genero" id="genero" required>
            <option value="Macho" <?php if ($animal['genero'] == 'Macho') echo 'selected'; ?>>Macho</option>
            <option value="Fêmea" <?php if ($animal['genero'] == 'Fêmea') echo 'selected'; ?>>Fêmea</option>
        </select>

        <label for="porte">Porte:</label>
        <select name="porte" id="porte" required>
            <option value="Pequeno" <?php if ($animal['porte'] == 'Pequeno') echo 'selected'; ?>>Pequeno</option>
            <option value="Medio" <?php if ($animal['porte'] == 'Medio') echo 'selected'; ?>>Médio</option>
            <option value="Grande" <?php if ($animal['porte'] == 'Grande') echo 'selected'; ?>>Grande</option>
        </select>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Ativo" <?php if ($animal['status'] == 'Ativo') echo 'selected'; ?>>Ativo</option>
            <option value="Inativo" <?php if ($animal['status'] == 'Inativo') echo 'selected'; ?>>Inativo</option>
            <option value="Adotado" <?php if ($animal['status'] == 'Adotado') echo 'selected'; ?>>Adotado</option>
        </select>

        <div class="checkbox-group">
            <label><input type="checkbox" name="castrado" <?php if ($animal['castrado']) echo 'checked'; ?>> Castrado</label>
            <label><input type="checkbox" name="vacinado" <?php if ($animal['vacinado']) echo 'checked'; ?>> Vacinado</label>
            <label><input type="checkbox" name="vermifugado" <?php if ($animal['vermifugado']) echo 'checked'; ?>> Vermifugado</label>
        </div>

        <label for="descricao">Descrição e Personalidade:</label>
        <textarea id="descricao" name="descricao" rows="6" required><?php echo htmlspecialchars($animal['descricao']); ?></textarea>

        <label for="foto">Alterar Foto do Animal:</label>
        <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto atual" style="max-width: 150px; display: block; margin-bottom: 10px;">
        <input type="file" id="foto" name="foto" accept="image/*">
        <small>Deixe em branco para manter a foto atual.</small>

        <button type="submit" class="btn-primary">Atualizar Animal</button>
    </form>
</section>

<?php include 'templates/footer.php'; ?>