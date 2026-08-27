<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

function redirect(string $path)
{
    // Validate that the path is safe - reject absolute URLs and protocol-relative URLs
    // Allow only relative paths (starting with /) or paths without protocol
    if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $path) || preg_match('~^//~', $path)) {
        // Reject absolute URLs and protocol-relative URLs
        $path = '/index.php';
    }

    // Ensure we only redirect to relative paths
    if (!str_starts_with($path, '/') && !str_starts_with($path, '.')) {
        $path = '/' . $path;
    }

    header("Location: $path");
    exit;
}

/**
 * Validate and sanitize guest name
 *
 * @param string $name
 * @return string Sanitized name
 * @throws Exception If validation fails
 */
function validateGuestName(string $name): string
{
    $name = trim($name);

    if (empty($name)) {
        throw new Exception('Guest name cannot be empty');
    }

    if (strlen($name) > 100) {
        throw new Exception('Guest name is too long (max 100 characters)');
    }

    // Only allow alphanumeric, spaces, hyphens, and apostrophes
    if (!preg_match("/^[a-zA-Z0-9\s\-']+$/", $name)) {
        throw new Exception('Guest name contains invalid characters');
    }

    return $name;
}

/**
 * Validate transfer code
 *
 * @param string $code
 * @return string Validated code
 * @throws Exception If validation fails
 */
function validateTransferCodeFormat(string $code): string
{
    $code = trim($code);

    if (empty($code)) {
        throw new Exception('Transfer code cannot be empty');
    }

    if (strlen($code) > 100) {
        throw new Exception('Transfer code is too long (max 100 characters)');
    }

    // Only allow alphanumeric and common separators
    if (!preg_match("/^[a-zA-Z0-9\-_]+$/", $code)) {
        throw new Exception('Transfer code contains invalid characters');
    }

    return $code;
}

/**
 * Validate date format (YYYY-MM-DD)
 *
 * @param string $date
 * @return string Validated date
 * @throws Exception If validation fails
 */
function validateDateFormat(string $date): string
{
    $date = trim($date);

    if (empty($date)) {
        throw new Exception('Date cannot be empty');
    }

    // Check format YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        throw new Exception('Date must be in YYYY-MM-DD format');
    }

    // Validate it's a real date
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        throw new Exception('Invalid date');
    }

    return $date;
}

/**
 * Generate a CSRF token and store in session
 *
 * @return string The CSRF token
 */
function generateCSRFToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token against the session token
 *
 * @param string $token The token to validate
 * @return bool True if token is valid, false otherwise
 */
function validateCSRFToken(string $token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}
