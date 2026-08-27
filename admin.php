<?php

declare(strict_types=1);

require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/admin/auth.php';

if (($_GET['action'] ?? '') === 'logout') {
    logout();
}

requireAdminAuth();

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<?php require __DIR__ . '/views/header.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <a class="btn btn-outline-secondary" href="/admin.php?action=logout">Log Out</a>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $page === 'dashboard' ? 'active' : ''; ?>" href="/admin.php?page=dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page === 'bookings' ? 'active' : ''; ?>" href="/admin.php?page=bookings">Bookings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $page === 'occupancy' ? 'active' : ''; ?>" href="/admin.php?page=occupancy">Occupancy Stats</a>
        </li>
    </ul>

    <?php switch ($page):
        case 'bookings': ?>
            <p>Bookings view coming soon.</p>
            <?php break;
        case 'occupancy': ?>
            <p>Occupancy stats view coming soon.</p>
            <?php break;
        case 'dashboard':
        default: ?>
            <p>Dashboard view coming soon.</p>
    <?php endswitch; ?>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>
