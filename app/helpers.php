<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

function redirect(string $path)
{
    header("Location: $path");
    exit;
}
