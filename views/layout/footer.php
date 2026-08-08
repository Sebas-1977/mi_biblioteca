</div><!-- /container -->
    </main>

    <footer class="footer">
        <p class="footer__text">Biblioteca Personal &copy; <?= date('Y') ?></p>
    </footer>

    <!-- JS Global -->
    <script src="/js/app.js"></script>
    
    <!-- JS Específico enviado por la vista -->
    <?= $script ?? '' ?>

</body>
</html>