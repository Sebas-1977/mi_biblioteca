<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['mensaje'] ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>