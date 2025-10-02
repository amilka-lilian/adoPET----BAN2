<?php
// AdoPET/index.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Seu Novo Melhor Amigo Espera Por Você!';
include 'templates/header.php';

$conn = get_db_connection();
$animais_destaque = [];
$stats = ['vidas_salvas' => 0, 'familias_felizes' => 0, 'parceiros' => 0];

try {
    $sql_animais = 
    $query = "SELECT a.*, u.nome as nome_doador, e.nome as nome_especie FROM animais a
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    JOIN especies e ON a.id_especie = e.id_especie
    WHERE a.status = 'Ativo'";
    $result_animais = $conn->query($sql_animais);

    if ($result_animais) {
        $animais_destaque = $result_animais->fetch_all(MYSQLI_ASSOC);
        if (empty($animais_destaque)) {
            echo "<p>Nenhum animal encontrado com status 'Ativo'.</p>"; 
        }
    } else {
        echo "<p>Erro na consulta de animais: " . $conn->error . "</p>"; 
    }

    $result_vidas = $conn->query("SELECT COUNT(id) as total FROM animais WHERE status = 'Adotado'");
    if ($result_vidas) $stats['vidas_salvas'] = $result_vidas->fetch_assoc()['total'];

    $result_familias = $conn->query("SELECT COUNT(DISTINCT id_interessado) as total FROM interesses_adocao WHERE status = 'Adotado'");
    if ($result_familias) $stats['familias_felizes'] = $result_familias->fetch_assoc()['total'];

    $result_parceiros = $conn->query("SELECT COUNT(id) as total FROM usuarios WHERE tipo_usuario = 'ONG'");
    if ($result_parceiros) $stats['parceiros'] = $result_parceiros->fetch_assoc()['total'];

} catch (Exception $e) {
    $_SESSION['flash_message'] = ['message' => 'Não foi possível carregar os dados da página inicial.', 'type' => 'danger'];
}
$conn->close();
?>

<section class="hero hero-home">
    <div class="hero-content">
        <h1>Encontre o Amor de Quatro Patas na AdoPET!</h1>
        <p>Milhares de animais resgatados esperam por um lar cheio de carinho e segurança. Dê uma segunda chance, adote!</p>
        <a href="animais.php" class="btn-primary">Ver Animais Disponíveis</a>
    </div>
</section>

<section class="container section-padding how-it-works">
    <h2 class="section-heading">Adotar é Simples!</h2>
    <p class="section-subheading">Em apenas 3 passos, você pode mudar uma vida para sempre.</p>
    <div class="steps-grid">
        <div class="step-item">
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <div class="step-number">1</div>
            <h3>Explore e Encontre</h3>
            <p>Navegue pelos perfis de cães e gatos. Use nossos filtros para achar o pet com a sua vibe!</p>
        </div>
        <div class="step-item">
            <div class="step-icon"><i class="fas fa-heart"></i></div>
            <div class="step-number">2</div>
            <h3>Manifeste Interesse</h3>
            <p>Achou seu futuro amigo? Preencha um formulário simples para o doador conhecer um pouco sobre você.</p>
        </div>
        <div class="step-item">
            <div class="step-icon"><i class="fas fa-home"></i></div>
            <div class="step-number">3</div>
            <h3>Prepare o Lar</h3>
            <p>Após a aprovação, combine a entrega e prepare sua casa para receber o mais novo membro da família!</p>
        </div>
    </div>
</section>

<section class="bg-light-purple section-padding">
    <div class="container">
        <h2 class="section-heading">Nossos Anjinhos em Destaque</h2>
        <p class="section-subheading">Essas fofuras estão ansiosas por um sofá pra chamar de seu. Veja se não rola um match!</p>
        <div class="galeria-animais">
            <?php if (!empty($animais_destaque)): ?>
            <?php foreach ($animais_destaque as $animal): ?>
            <div class="animal-card">
                    <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">
                    <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                    <p><?php echo htmlspecialchars($animal['nome_especie']); ?> - <?php echo htmlspecialchars($animal['idade']); ?></p>
                    <p>Doador(a): <?php echo htmlspecialchars($animal['nome_doador']); ?></p>
                    <div class="card-actions">
                        <a href="animal_detalhes.php?id=<?php echo $animal['id_animais']; ?>" class="btn-primary btn-small">Ver Detalhes</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
        <p class="info-message text-center">Nenhum animal em destaque no momento. <a href="animais.php">Veja todos os animais disponíveis!</a></p>
        <?php endif; ?>
        </div>
                <div class="text-center mt-5">
            <a href="animais.php" class="btn-primary btn-small">Ver Todos os Animais</a>
        </div>
    </div>
</section>

<section class="stats-section section-padding">
    <div class="container">
        <h2 class="section-heading light-text">Nossa Causa em Números</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-paw"></i></div>
                <h3 class="stat-number" data-target="<?php echo $stats['vidas_salvas']; ?>">0</h3>
                <p class="stat-label">Vidas Salvas</p>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <h3 class="stat-number" data-target="<?php echo $stats['familias_felizes']; ?>">0</h3>
                <p class="stat-label">Famílias Felizes</p>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-hands-helping"></i></div>
                <h3 class="stat-number" data-target="<?php echo $stats['parceiros']; ?>">0</h3>
                <p class="stat-label">Parceiros da Causa</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = Math.ceil(target / speed);

                    if (count < target) {
                        counter.innerText = count + inc;
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        observer.observe(counter);
    });
});
</script>

<?php include 'templates/footer.php'; ?>