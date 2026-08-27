<?php

declare(strict_types=1);

// Admin authentication: session-based login gate for the admin panel.
//
// Depends on helpers.php (generateCSRFToken()/validateCSRFToken()) and a
// running session, both of which are set up in app/autoload.php.

/**
 * Require the current visitor to be logged in as an admin.
 *
 * If the visitor already has an authenticated admin session, this simply
 * returns and lets the caller continue rendering the admin panel.
 *
 * Otherwise, it handles a login form submission (verifying the submitted
 * password against the ADMIN_PASSWORD hash), or - if there is no valid
 * submission yet - renders the login form and halts script execution, so
 * nothing past this call ever runs for an unauthenticated visitor.
 */
function requireAdminAuth(): void
{
    if (!empty($_SESSION['admin_logged_in'])) {
        return;
    }

    $adminConfig = require __DIR__ . '/config.php';
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        $submittedToken = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '';
        $submittedPassword = is_string($_POST['admin_password']) ? $_POST['admin_password'] : '';

        if (!validateCSRFToken($submittedToken)) {
            $error = 'Your session expired. Please try again.';
        } elseif (
            empty($adminConfig['admin_password_hash']) ||
            !password_verify($submittedPassword, (string)$adminConfig['admin_password_hash'])
        ) {
            $error = 'Incorrect password.';
        } else {
            $_SESSION['admin_logged_in'] = true;
            session_regenerate_id(true);

            header('Location: /admin.php');
            exit;
        }
    }

    renderAdminLoginForm($error);
    exit;
}

/**
 * Render the standalone admin login page.
 */
function renderAdminLoginForm(?string $error): void
{
    $csrfToken = generateCSRFToken();
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body data-bs-theme="dark" class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4" style="min-width: 320px;">
        <h1 class="h4 mb-3">Admin Login</h1>
        <?php if ($error !== null): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post" action="/admin.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-3">
                <label for="admin_password" class="form-label">Password</label>
                <input type="password" class="form-control" id="admin_password" name="admin_password" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>
    </div>
</body>

</html>
    <?php
}

/**
 * Log the admin out and send them back to the login form.
 */
function logout(): void
{
    unset($_SESSION['admin_logged_in']);
    session_regenerate_id(true);

    header('Location: /admin.php');
    exit;
}
