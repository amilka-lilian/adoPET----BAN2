<?php
// AdoPET/contato.php
$page_title = 'Entre em Contato';
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'templates/header.php';
?>

<section class="container page-section">
    <div class="contato-header">
        <h1>Estamos aqui para ajudar!</h1>
        <p>Tem alguma dúvida, sugestão ou precisa de suporte? Preencha o formulário ou utilize um dos nossos canais de atendimento abaixo.</p>
    </div>

    <div class="contato-container">
        <div class="contato-form-wrapper">
            <h3>Envie sua Mensagem</h3>
            <form action="#" method="POST" class="contact-form">
                <label for="nome">Seu Nome:</label>
                <input type="text" id="nome" name="nome" required>
                
                <label for="email">Seu E-mail:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="assunto">Assunto:</label>
                <input type="text" id="assunto" name="assunto" required>

                <label for="mensagem">Sua Mensagem:</label>
                <textarea id="mensagem" name="mensagem" rows="6" required></textarea>
                
                <button type="submit" class="btn-primary">Enviar Mensagem</button>
            </form>
        </div>

        <div class="contato-info-wrapper">
            <h3>Nossos Contatos</h3>
            <div class="contact-method">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div class="contact-text">
                    <strong>E-mail</strong>
                    <a href="mailto:contato@adopet.com">contato@adopet.com</a>
                </div>
            </div>
            <div class="contact-method">
                <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="contact-text">
                    <strong>Telefone</strong>
                    <span>(47) 3481-7900</span>
                </div>
            </div>
            <div class="contact-method">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="contact-text">
                    <strong>Endereço</strong>
                    <span>R. Paulo Malschitzki, 200 - Joinville/SC</span>
                </div>
            </div>

            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3576.15177218318!2d-48.87413688496781!3d-26.32115498338573!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94deafc94976cbfd%3A0x69591a26b34f71d5!2sR.%20Paulo%20Malschitzki%2C%20200%20-%20Zona%20Industrial%20Norte%2C%20Joinville%20-%20SC%2C%2089219-710!5e0!3m2!1spt-BR!2sbr!4v1670000000000!5m2!1spt-BR!2sbr" 
                    width="100%" 
                    height="250" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>