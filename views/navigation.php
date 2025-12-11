<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="logo-container"></div>
        <a class="navbar-brand" href="#"><?php echo $config['title']; ?></a>

        <ul class="navbar-nav">
            <li class="nav-item">
                <?php if ($_SERVER['REQUEST_URI'] == '/index.php') : ?>
                    <a class="nav-link active" aria-current="/index.php" href="/index.php">Home</a>
                <?php else : ?>
                    <a class="nav-link" href="/index.php">Home</a>
                <?php endif; ?>
            </li>

            <li class="nav-item">
                <?php if ($_SESSION['user'] ?? 0) : ?>
                    <a class="nav-link" href="/app/users/logout.php">Logout</a>
                <?php else : ?>
                    <a class="nav-link" href="/login.php">Login</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>

</nav>