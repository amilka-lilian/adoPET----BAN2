<?php
// AdoPET/sobre.php
$page_title = 'Sobre a AdoPET';
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'templates/header.php';
?>

<section class="sobre-hero">
    <div class="sobre-hero-content">
        <h1>Conectando Corações e Patas</h1>
        <p>Mais que um site de adoção, somos uma ponte para novas histórias de amor e amizade.</p>
    </div>
</section>

<section class="container page-section">
    <div class="missao-section">
        <h2>Nossa Missão</h2>
        <p>Nossa missão é simplificar o processo de adoção, tornando-o acessível, seguro e transparente para todos. Queremos facilitar a vida de ONGs, protetores independentes e pessoas que desejam doar um animal, ao mesmo tempo em que proporcionamos aos futuros tutores uma maneira fácil e intuitiva de encontrar seu novo melhor amigo. Acreditamos que toda vida tem um valor inestimável e merece uma segunda chance em um lar feliz.</p>
    </div>

    <hr class="section-divider">

    <div class="valores-section">
        <h2>Nossos Valores</h2>
        <div class="valores-container">
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-heart"></i></div>
                <h3>Amor e Respeito</h3>
                <p>A base de tudo que fazemos. Cada animal é tratado como um indivíduo único, digno de amor, respeito e cuidado incondicional.</p>
            </div>
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-hands-helping"></i></div>
                <h3>Responsabilidade</h3>
                <p>Promovemos a posse consciente e a adoção responsável, educando e oferecendo suporte para garantir o bem-estar do pet por toda a vida.</p>
            </div>
            <div class="valor-card">
                <div class="valor-icon"><i class="fas fa-search-location"></i></div>
                <h3>Transparência</h3>
                <p>Construímos uma comunidade de confiança, com processos claros e honestos que conectam doadores e adotantes de forma segura.</p>
            </div>
        </div>
    </div>
    
    <hr class="section-divider">

    <div class="sobre-cta">
        <h2>Faça Parte da Nossa História</h2>
        <p>Sua ajuda pode mudar o destino de um animal. Encontre um amigo ou ajude-nos a encontrar um lar para quem precisa.</p>
        <div class="cta-buttons">
            <a href="animais.php" class="btn-primary">Ver Animais para Adoção</a>
            <a href="dashboard.php" class="btn-secondary">Seja um Doador</a>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>