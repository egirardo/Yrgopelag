<?php

declare(strict_types=1);

// Admin panel configuration settings.
//
// ADMIN_PASSWORD is expected to hold a password *hash* (as produced by
// password_hash()), not a plaintext password. It is verified against the
// submitted login password using password_verify().
//
// Dotenv::createImmutable() (used in app/autoload.php) only populates
// $_ENV/$_SERVER by default, not the real process environment, so a plain
// getenv() call won't see values loaded from .env. Fall back to $_ENV /
// $_SERVER so ADMIN_PASSWORD works whether it's set via .env or a real
// environment variable (e.g. set by the hosting platform).
$adminPasswordHash = getenv('ADMIN_PASSWORD');
if ($adminPasswordHash === false) {
    $adminPasswordHash = $_ENV['ADMIN_PASSWORD'] ?? $_SERVER['ADMIN_PASSWORD'] ?? '';
}

return [
    'admin_password_hash' => $adminPasswordHash,
    'items_per_page' => 10,
    'upcoming_days_ahead' => 7,
    'past_bookings_limit' => 5,
];
