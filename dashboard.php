<?php
// AdoPET/dashboard.php
require_once 'db.php';
require_once 'helpers.php'; 
session_start();
$page_title = 'Meu Painel - AdoPET';

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Por favor, faça login para acessar esta página.', 'warning');
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = get_db_connection();

$sql_perfil = "
    SELECT u.*, tu.descricao AS tipo_usuario 
    FROM usuarios u
    JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id
    WHERE u.id = ? 
";
$stmt_perfil = $conn->prepare($sql_perfil);
$stmt_perfil->bind_param("i", $user_id);
$stmt_perfil->execute();
$perfil_usuario = $stmt_perfil->get_result()->fetch_assoc();
$stmt_perfil->close();

if (!$perfil_usuario) {
    die("Erro: Usuário não encontrado.");
}
$user_type = $perfil_usuario['tipo_usuario'];

$telefone_stmt = $conn->prepare("SELECT CONCAT(ddd, ' ', numero) as telefone FROM telefones WHERE id_usuario = ? LIMIT 1");
$telefone_stmt->bind_param("i", $user_id);
$telefone_stmt->execute();
$telefone_result = $telefone_stmt->get_result()->fetch_assoc();
$telefone = $telefone_result['telefone'] ?? 'Não informado';
$telefone_stmt->close();

$endereco = 'Não informado';
if (!empty($perfil_usuario['id_endereco'])) {
    $endereco_stmt = $conn->prepare("SELECT CONCAT(rua, ', ', numero) as endereco FROM enderecos WHERE id = ? LIMIT 1");
    $endereco_stmt->bind_param("i", $perfil_usuario['id_endereco']);
    $endereco_stmt->execute();
    $endereco_result = $endereco_stmt->get_result()->fetch_assoc();
    $endereco = $endereco_result['endereco'] ?? 'Não informado';
    $endereco_stmt->close();
}

$meus_animais = [];
$interesses_recebidos = [];
$meus_interesses_enviados = [];

if ($user_type == 'ONG' || $user_type == 'Pessoa Fisica') {
    $stmt_animais = $conn->prepare("
        SELECT a.*, e.nome AS nome_especie 
        FROM animais a
        JOIN especies e ON a.id_especie = e.id
        WHERE a.id_usuario = ? 
        ORDER BY a.data_cadastro DESC
    ");
    $stmt_animais->bind_param("i", $user_id);
    $stmt_animais->execute();
    $meus_animais = $stmt_animais->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_animais->close();

    $sql_interesses = "
        SELECT ia.*, a.nome as nome_animal, u.nome as nome_interessado, u.email as email_interessado, 
        (SELECT CONCAT(ddd, ' ', numero) FROM telefones WHERE id_usuario = u.id LIMIT 1) as telefone_interessado
        FROM interesses_adocao ia
        JOIN animais a ON ia.id_animal = a.id
        JOIN usuarios u ON ia.id_interessado = u.id
        WHERE a.id_usuario = ?
        ORDER BY ia.data_interesse DESC
    ";
    $stmt_interesses = $conn->prepare($sql_interesses);
    $stmt_interesses->bind_param("i", $user_id);
    $stmt_interesses->execute();
    $interesses_recebidos = $stmt_interesses->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_interesses->close();
}

if ($user_type == 'Pessoa Fisica') {
    $sql_enviados = "
        SELECT ia.*, a.nome as nome_animal, a.id as id_animal_fk, u.nome as nome_doador
        FROM interesses_adocao ia
        JOIN animais a ON ia.id_animal = a.id
        JOIN usuarios u ON a.id_usuario = u.id
        WHERE ia.id_interessado = ?
        ORDER BY ia.data_interesse DESC
    ";
    $stmt_enviados = $conn->prepare($sql_enviados);
    $stmt_enviados->bind_param("i", $user_id);
    $stmt_enviados->execute();
    $meus_interesses_enviados = $stmt_enviados->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_enviados->close();
}

$conn->close();
?>

<section class="dashboard">
    <h1>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <div class="profile-info-summary">
        <p><strong>Tipo de Usuário:</strong> <?php echo htmlspecialchars($user_type); ?></p>
        <p><strong>E-mail:</strong> <?php echo htmlspecialchars($perfil_usuario['email']); ?></p>
        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($telefone); ?></p>
        <p><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco); ?></p>
        <?php if ($user_type == 'ONG'): ?>
            <p><strong>Descrição da ONG:</strong> <?php echo htmlspecialchars($perfil_usuario['descricao'] ?: 'Não informada'); ?></p>
        <?php endif; ?>
        <a href="editar_perfil.php" class="btn-secondary">Editar Perfil</a>
            <form action="excluir_usuario.php" method="POST" style="display:inline-block; margin-left: 10px;" 
            onsubmit="return confirm('ATENÇÃO: Você tem certeza que deseja excluir sua conta? Esta ação é irreversível e apagará seus dados e animais cadastrados (dependendo das regras do banco).');">
            
            <input type="hidden" name="id_usuario" value="<?php echo $user_id; ?>">
            <button type="submit" class="btn-danger btn-secondary">Excluir Conta</button>
        </form>
    </div>         

    <?php if ($user_type == 'ONG' || $user_type == 'Pessoa Fisica'): ?>
        <hr>
        <h2>Meus Animais Cadastrados</h2>
        <div class="dashboard-actions">
            <a href="adicionar_animal.php" class="btn-primary">Adicionar Novo Animal</a>
        </div>
        <div class="galeria-animais">
            <?php if (!empty($meus_animais)): ?>
                <?php foreach ($meus_animais as $animal): 
                    $is_adotado = !$animal['disponivel'];
                ?>
                    <div class="animal-card <?php echo $is_adotado ? 'card-adotado' : ''; ?>">
                        <img src="<?php echo $animal['foto_url'] 
                            ? 'uploads/' . htmlspecialchars($animal['foto_url']) 
                            : 'static/img/placeholder.png'; ?>" 
                            alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">
                        <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                        <p>
                            <?php echo htmlspecialchars($animal['nome_especie']); ?> 
                            - 
                            <?php echo $is_adotado 
                                ? '<span class="status-badge status-adotado">Adotado</span>' 
                                : '<span class="status-badge status-disponivel">Disponível</span>'; 
                            ?>
                        </p>
                        <div class="card-actions">
                            <a href="animal_detalhes.php?id=<?php echo $animal['id']; ?>" class="btn-small btn-secondary">Ver</a>
                            <a href="editar_animal.php?id=<?php echo $animal['id']; ?>" class="btn-small btn-secondary">Editar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="info-message">Você ainda não cadastrou nenhum animal.</p>
            <?php endif; ?>
        </div>

        <hr>
        <h2>Interesses Recebidos</h2>
        <?php if (!empty($interesses_recebidos)): ?>
            <div class="interesses-lista">
                <?php foreach ($interesses_recebidos as $interesse): ?>
                    <div class="interesse-card interesse-status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>">
                        <h4>Interesse em "<?php echo htmlspecialchars($interesse['nome_animal']); ?>"</h4>
                        <p><strong>De:</strong> <?php echo htmlspecialchars($interesse['nome_interessado']); ?> (<?php echo htmlspecialchars($interesse['email_interessado']); ?>)</p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($interesse['telefone_interessado'] ?: 'Não informado'); ?></p>
                        <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>"><?php echo htmlspecialchars($interesse['status']); ?></span></p>
                        <p><strong>Mensagem:</strong> <?php echo nl2br(htmlspecialchars($interesse['mensagem_interessado'] ?: 'Nenhuma mensagem.')); ?></p>
                        
                        <?php if ($interesse['status'] == 'Pendente'): ?>
                        <div class="interest-actions">
                            <form action="gerenciar_interesse.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                <input type="hidden" name="action" value="aprovar">
                                <button type="submit" class="btn-small btn-approve">Aprovar</button>
                            </form>
                            <form action="gerenciar_interesse.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                <input type="hidden" name="action" value="rejeitar">
                                <button type="submit" class="btn-small btn-reject">Rejeitar</button>
                            </form>
                        </div>
                        <?php elseif ($interesse['status'] == 'Aprovado'): ?>
                            <form action="gerenciar_interesse.php" method="POST">
                                 <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                 <input type="hidden" name="action" value="marcar_adotado">
                                 <button type="submit" class="btn-small btn-adopt">Marcar como Adotado</button>
                             </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="info-message">Nenhum interesse recebido ainda.</p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($user_type == 'Pessoa Fisica'): ?>
        <hr>
        <h2>Meus Interesses Enviados</h2>
        <?php if (!empty($meus_interesses_enviados)): ?>
            <div class="interesses-lista">
                <?php foreach ($meus_interesses_enviados as $interesse): ?>
                    <div class="interesse-card interesse-status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>">
                        <h4>Interesse em "<?php echo htmlspecialchars($interesse['nome_animal']); ?>"</h4>
                        <p><strong>Doador:</strong> <?php echo htmlspecialchars($interesse['nome_doador']); ?></p>
                        <p><strong>Seu Status:</strong> <span class="status-badge status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>"><?php echo htmlspecialchars($interesse['status']); ?></span></p>
                        <p><strong>Sua Mensagem:</strong> <?php echo nl2br(htmlspecialchars($interesse['mensagem_interessado'] ?: 'Nenhuma mensagem.')); ?></p>
                        <a href="animal_detalhes.php?id=<?php echo $interesse['id_animal_fk']; ?>" class="btn-small btn-secondary">Ver Animal</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="info-message">Você ainda não manifestou interesse em nenhum animal.</p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'templates/footer.php'; ?>
