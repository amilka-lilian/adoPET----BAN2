<?php
// AdoPET/templates/footer.php
?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> AdoPET. Todos os direitos reservados. Feito com &#x2764; em Joinville, SC.</p>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>