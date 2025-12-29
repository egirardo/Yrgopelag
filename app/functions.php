<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

function redirect(string $path)
{
    header("Location: $path");
    exit;
}

function getAllRooms(PDO $db): array
{
    $stmt = $db->query("SELECT * FROM rooms");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loadRoom(PDO $db): ?array
{
    if (!isset($_GET['room_id'])) {
        return null;
    }

    $roomId = (int)$_GET['room_id'];

    $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function loadBookings(PDO $db): array
{
    $stmt = $db->prepare("
    SELECT start_date, end_date 
    FROM bookings 
    WHERE room_id = ?
    ");

    $roomId = (int)$_GET['room_id'];

    $stmt->execute([$roomId]);

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBookedDaysForMonth(
    PDO $db,
    int $roomId,
    int $year,
    int $month
): array {
    $monthStart = sprintf('%04d-%02d-01', $year, $month);
    $monthEnd   = date('Y-m-t', strtotime($monthStart));

    $stmt = $db->prepare("
        SELECT start_date, end_date
        FROM bookings
        WHERE room_id = ?
        AND start_date <= ?
        AND end_date >= ?
    ");

    $stmt->execute([
        $roomId,
        $monthEnd,
        $monthStart
    ]);

    $bookedDays = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $booking) {
        $start = max(
            strtotime($booking['start_date']),
            strtotime($monthStart)
        );
        $end = min(
            strtotime($booking['end_date']),
            strtotime($monthEnd)
        );

        while ($start <= $end) {
            $bookedDays[] = (int)date('j', $start);
            $start = strtotime('+1 day', $start);
        }
    }

    return array_values(array_unique($bookedDays));
}
