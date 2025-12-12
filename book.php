<?php require __DIR__ . '/app/autoload.php'; ?>
<?php require __DIR__ . '/views/header.php'; ?>

<article>
    <h1>Login</h1>

    <form action="app/users/login.php" method="post">
        <?php if ($_SESSION['error']) : ?>
            <p class="error"><?= $_SESSION['error']; ?></p>
        <?php endif; ?>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input class="form-control" type="username" name="username" id="username" placeholder="Sbargle" required>
            <small class="form-text">Please provide your first name.</small>
        </div>

        <div class="mb-3">
            <!-- transferCode rather than password -->
            <label for="transferCode" class="form-label">Transfer Code</label>
            <input class="form-control" type="text" name="transferCode" id="transferCode" required>
            <small class="form-text">Please provide your centralbank transfer code.</small>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</article>

<?php require __DIR__ . '/views/footer.php'; ?>