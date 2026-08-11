</div><!-- /container -->
    </main>

    <script src="/js/tabla-ajax.js"></script>
    
    <footer class="footer">
        <p class="footer__text">Biblioteca Personal &copy; <?= date('Y') ?></p>
    </footer>
    
    <!-- JS Específico enviado por la vista -->
    <?= $script ?? '' ?>

</body>
</html>