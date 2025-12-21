<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container-fluid">
        <div class="logo-container"></div>
        <a class="navbar-brand" href="#"><?php echo $config['title']; ?></a>

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about.php">About</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Book Now!</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="book.php">Budget Room</a>
                    <a class="dropdown-item" href="book.php">Standard Room</a>
                    <a class="dropdown-item" href="book.php">Luxury Room</a>
                </div>
            </li>
        </ul>
    </div>

</nav>