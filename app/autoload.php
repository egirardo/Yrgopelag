<?php

declare(strict_types=1);

session_start();

date_default_timezone_set('UTC');

mb_internal_encoding('UTF-8');

require __DIR__ . '/functions.php';

$config = require __DIR__ . '/config.php';

$database = new PDO($config['database_path']);

$admin = require __DIR__ . '/admin.php';


$roomsStatement = $database->query("SELECT * FROM rooms");
$rooms = $roomsStatement->fetchAll(PDO::FETCH_ASSOC);


$roomId = 1;
$bookingsStatement = $database->query("SELECT start_date, end_date FROM bookings WHERE room_id = :room_id");
$bookingsStatement->execute([':room_id' => $roomId]);
$bookings = $bookingsStatement->fetchAll(PDO::FETCH_ASSOC);

foreach ($bookings as $booking) {
    $startDate = substr($booking['start_date'], 8, 2);
    $endDate = substr($booking['end_date'], 8, 2);
    $bookingRange = range($startDate, $endDate);

    $admin['budgetBooked'] = array_merge($admin['budgetBooked'], $bookingRange);
}
foreach ($bookings as $booking) {
    $startDate = substr($booking['start_date'], 8, 2);
    $endDate = substr($booking['end_date'], 8, 2);
    $bookingRange = range($startDate, $endDate);

    $admin['standardBooked'] = array_merge($admin['standardBooked'], $bookingRange);
}
foreach ($bookings as $booking) {
    $startDate = substr($booking['start_date'], 8, 2);
    $endDate = substr($booking['end_date'], 8, 2);
    $bookingRange = range($startDate, $endDate);

    $admin['luxuryBooked'] = array_merge($admin['luxuryBooked'], $bookingRange);
}


$featuresStatement = $database->query("SELECT * FROM features");
$features = $featuresStatement->fetchAll(PDO::FETCH_ASSOC);
