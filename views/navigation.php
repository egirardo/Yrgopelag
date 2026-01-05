<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php"><img src="/assets/images/logo.png" alt="logo" id="logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>" href="/about.php">About</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $currentPage === 'book.php' ? 'active' : '' ?>" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Book Now!</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="book.php?room_id=1">Budget Room</a>
                        <a class="dropdown-item" href="book.php?room_id=2">Standard Room</a>
                        <a class="dropdown-item" href="book.php?room_id=3">Luxury Room</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>