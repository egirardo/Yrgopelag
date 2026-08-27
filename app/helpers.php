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
