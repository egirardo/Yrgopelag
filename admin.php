<?php

declare(strict_types=1);

require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/admin/auth.php';
require_once __DIR__ . '/app/admin/database.php';

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
            <?php require __DIR__ . '/views/admin/bookings_list.php'; ?>
            <?php break;
        case 'occupancy': ?>
            <?php require __DIR__ . '/views/admin/occupancy_stats.php'; ?>
            <?php break;
        case 'dashboard':
        default: ?>
            <div class="alert alert-info">
                <h5>Welcome to the Admin Dashboard</h5>
                <p>Use the tabs above to navigate between Bookings and Occupancy Stats.</p>
            </div>
    <?php endswitch; ?>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>
