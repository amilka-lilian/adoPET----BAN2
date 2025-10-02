<?php
// AdoPET/cadastro.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Cadastre-se na AdoPET';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirm_senha = $_POST['confirm_senha'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';
    $documento = $_POST['documento'] ?? null;
    $descricao = $_POST['descricao'] ?? null;

    // Endereço
    $rua = $_POST['rua'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    // Telefone
    $ddd = $_POST['ddd'] ?? '';
    $numero_telefone = $_POST['numero_telefone'] ?? '';

    $tipo_usuario_id = ($tipo_usuario === 'ONG') ? 1 : 2;

    if (empty($nome) || empty($email) || empty($senha) || empty($confirm_senha) || empty($tipo_usuario)) {
        set_flash_message('Todos os campos obrigatórios devem ser preenchidos.', 'danger');
    } elseif ($senha !== $confirm_senha) {
        set_flash_message('As senhas não coincidem.', 'danger');
    } elseif (strlen($senha) < 6) {
        set_flash_message('A senha deve ter no mínimo 6 caracteres.', 'danger');
    } else {
        $conn = get_db_connection();
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            set_flash_message('Este email já está cadastrado.', 'danger');
        } else {
            $hashed_senha = password_hash($senha, PASSWORD_DEFAULT);

            // Inserir endereço
            $id_endereco = null;
            $end_stmt = $conn->prepare("INSERT INTO enderecos (rua, numero, bairro, cep, cidade, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $end_stmt->bind_param("ssssss", $rua, $numero, $bairro, $cep, $cidade, $estado);
            if ($end_stmt->execute()) {
                $id_endereco = $conn->insert_id;
            }
            $end_stmt->close();

            // Inserir usuário
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, id_tipo_usuario, documento, id_endereco, descricao) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisis", $nome, $email, $hashed_senha, $tipo_usuario_id, $documento, $id_endereco, $descricao);

            if ($stmt->execute()) {
                $id_usuario = $conn->insert_id;

                // Inserir telefone
                if (!empty($ddd) && !empty($numero_telefone)) {
                    $tel_stmt = $conn->prepare("INSERT INTO telefones (id_usuario, ddd, numero) VALUES (?, ?, ?)");
                    $tel_stmt->bind_param("iss", $id_usuario, $ddd, $numero_telefone);
                    $tel_stmt->execute();
                    $tel_stmt->close();
                }

                set_flash_message('Cadastro realizado com sucesso! Faça login para continuar.', 'success');
                header("Location: login.php");
                exit();
            } else {
                set_flash_message('Erro ao cadastrar: ' . $stmt->error, 'danger');
            }
            $stmt->close();
        }
        $conn->close();
    }

    $_SESSION['form_data'] = $_POST;
    header("Location: cadastro.php");
    exit();
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

include 'templates/header.php';
?>

<section class="form-section">
    <h2>Cadastro de Usuário</h2>
    <form method="POST" action="cadastro.php" onsubmit="return validarCadastro()">
        <label for="nome">Nome/Nome da ONG:</label>
        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($form_data['nome'] ?? ''); ?>">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required minlength="6">
        <small class="help-text">Mínimo de 6 caracteres.</small>

        <label for="confirm_senha">Confirmar Senha:</label>
        <input type="password" id="confirm_senha" name="confirm_senha" required>

        <label for="tipo_usuario">Tipo de Usuário:</label>
        <select id="tipo_usuario" name="tipo_usuario" onchange="toggleCamposCadastro()" required>
            <option value="">Selecione...</option>
            <option value="Pessoa Fisica" <?php echo (($form_data['tipo_usuario'] ?? '') == 'Pessoa Fisica') ? 'selected' : ''; ?>>Pessoa Física</option>
            <option value="ONG" <?php echo (($form_data['tipo_usuario'] ?? '') == 'ONG') ? 'selected' : ''; ?>>ONG</option>
        </select>

        <div id="campos_pf" class="form-group hidden">
            <label for="documento_pf">CPF:</label>
            <input type="text" id="documento_pf" name="documento" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00" value="<?php echo htmlspecialchars($form_data['documento'] ?? ''); ?>">
        </div>

        <div id="campos_ong" class="form-group hidden">
            <label for="documento_ong">CNPJ (Opcional):</label>
            <input type="text" id="documento_ong" name="documento_ong_field" pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}" placeholder="00.000.000/0000-00" value="<?php echo htmlspecialchars($form_data['documento'] ?? ''); ?>">
            <label for="descricao_ong">Descrição da ONG:</label>
            <textarea id="descricao_ong" name="descricao" rows="4"><?php echo htmlspecialchars($form_data['descricao'] ?? ''); ?></textarea>
        </div>

        <h3>Endereço</h3>
        <label for="rua">Rua:</label>
        <input type="text" id="rua" name="rua" required value="<?php echo htmlspecialchars($form_data['rua'] ?? ''); ?>">

        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" required value="<?php echo htmlspecialchars($form_data['numero'] ?? ''); ?>">

        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" required value="<?php echo htmlspecialchars($form_data['bairro'] ?? ''); ?>">

        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required value="<?php echo htmlspecialchars($form_data['cep'] ?? ''); ?>">

        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required value="<?php echo htmlspecialchars($form_data['cidade'] ?? ''); ?>">

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" maxlength="2" required value="<?php echo htmlspecialchars($form_data['estado'] ?? ''); ?>">

        <h3>Telefone</h3>
        <label for="ddd">DDD:</label>
        <input type="text" id="ddd" name="ddd" maxlength="3"  placeholder="Ex: (047)" required value="<?php echo htmlspecialchars($form_data['ddd'] ?? ''); ?>">
        <small class="help-text">Somente número</small>

        <label for="numero_telefone">Número:</label>
        <input type="text" id="numero_telefone" name="numero_telefone" placeholder="Ex: 98888-7777" required value="<?php echo htmlspecialchars($form_data['numero_telefone'] ?? ''); ?>">
        <small class="help-text">Somente número</small>

        <button type="submit" class="btn-primary">Cadastrar</button>
    </form>
</section>


<script src="https://unpkg.com/imask"></script>
<script>
    // Máscaras
    var cpfInput = document.getElementById('documento_pf');
    IMask(cpfInput, { mask: '000.000.000-00' });

    var cnpjInput = document.getElementById('documento_ong');
    IMask(cnpjInput, { mask: '00.000.000/0000-00' });

    var dddInput = document.getElementById('ddd');
    IMask(dddInput, { mask: '000' });

    var numeroInput = document.getElementById('numero_telefone');
    IMask(numeroInput, {
        mask: [
            { mask: '0000-0000' },
            { mask: '00000-0000' }
        ]
    });

    function toggleCamposCadastro() {
        var tipoUsuario = document.getElementById('tipo_usuario').value;
        var camposPf = document.getElementById('campos_pf');
        var camposOng = document.getElementById('campos_ong');
        var documentoPfInput = document.getElementById('documento_pf');
        var documentoOngInput = document.querySelector('#campos_ong input[name="documento_ong_field"]');
        var descricaoOngTextarea = document.getElementById('descricao_ong');

        documentoPfInput.name = 'documento';
        documentoOngInput.name = 'documento_ong_field';

        camposPf.classList.add('hidden');
        camposOng.classList.add('hidden');
        documentoPfInput.removeAttribute('required');
        documentoOngInput.removeAttribute('required');
        descricaoOngTextarea.removeAttribute('required');

        if (tipoUsuario === 'Pessoa Fisica') {
            camposPf.classList.remove('hidden');
            documentoPfInput.setAttribute('required', 'true');
            documentoOngInput.value = '';
        } else if (tipoUsuario === 'ONG') {
            camposOng.classList.remove('hidden');
            documentoOngInput.name = 'documento';
            descricaoOngTextarea.setAttribute('required', 'true');
            documentoPfInput.value = '';
        }
    }

    function validarCadastro() {
        var senha = document.getElementById('senha').value;
        var confirmSenha = document.getElementById('confirm_senha').value;
        var tipoUsuario = document.getElementById('tipo_usuario').value;

        if (senha !== confirmSenha) {
            alert('As senhas não coincidem!');
            return false;
        }
        if (senha.length < 6) {
            alert('A senha deve ter no mínimo 6 caracteres.');
            return false;
        }
        if (tipoUsuario === '') {
            alert('Por favor, selecione o tipo de usuário.');
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', toggleCamposCadastro);
</script>

<?php include 'templates/footer.php'; ?>

