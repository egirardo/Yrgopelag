<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('UTC');

mb_internal_encoding('UTF-8');


require_once __DIR__ . '/functions.php';

$config = require_once __DIR__ . '/config.php';

$db = new PDO($config['database_path']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$admin = require_once __DIR__ . '/admin.php';



$featuresStmt = $db->query("SELECT * FROM features");
$features = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);
