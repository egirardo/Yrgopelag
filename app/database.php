<?php

declare(strict_types=1);

// Room functions
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

function getRoomPrice(PDO $db, int $roomId): int
{
    $stmt = $db->prepare("SELECT price FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);

    $price = $stmt->fetchColumn();

    if ($price === false) {
        throw new Exception('Invalid room');
    }

    return (int)$price;
}

// Booking functions
function loadBookings(PDO $db): array
{
    $stmt = $db->prepare("
        SELECT start_date, end_date 
        FROM bookings 
        WHERE room_id = ?
    ");

    $roomId = (int)$_GET['room_id'];
    $stmt->execute([$roomId]);

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

    $stmt->execute([$roomId, $monthEnd, $monthStart]);

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

function bookingConflicts(
    PDO $db,
    int $roomId,
    string $startDate,
    string $endDate
): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM bookings
        WHERE room_id = ?
        AND NOT (end_date < ? OR start_date > ?)
    ");

    $stmt->execute([$roomId, $startDate, $endDate]);

    return (int)$stmt->fetchColumn() > 0;
}

function saveBooking(
    PDO $db,
    int $roomId,
    string $startDate,
    string $endDate,
    int $userId,
    int $totalCost
): int {
    $stmt = $db->prepare("
        INSERT INTO bookings (room_id, user_id, start_date, end_date, total_cost, created_at)
        VALUES (?, ?, ?, ?, ?, DATETIME('now'))
    ");

    $stmt->execute([$roomId, $userId, $startDate, $endDate, $totalCost]);

    return (int)$db->lastInsertId();
}

function getBooking(PDO $db, int $bookingId): array
{
    $stmt = $db->prepare("
        SELECT b.*, r.rank AS room_rank
        FROM bookings b
        JOIN rooms r ON r.id = b.room_id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);

    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    return $booking;
}

// User functions
function getOrCreateGuest(PDO $db, string $guestName): int
{
    $stmt = $db->prepare("SELECT id FROM users WHERE firstName = ?");
    $stmt->execute([$guestName]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
        return (int)$userId;
    }

    $stmt = $db->prepare("INSERT INTO users (firstName) VALUES (?)");
    $stmt->execute([$guestName]);

    return (int)$db->lastInsertId();
}

// Activity/Feature functions
function getActivityTotalByIds(PDO $db, array $featureIds): int
{
    if (empty($featureIds)) {
        return 0;
    }

    $featureIds = array_map('intval', $featureIds);
    $placeholders = implode(',', array_fill(0, count($featureIds), '?'));

    $stmt = $db->prepare("
        SELECT SUM(price)
        FROM features
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($featureIds);

    $total = $stmt->fetchColumn();

    return (int)($total ?? 0);
}

function saveBookingActivities(
    PDO $db,
    int $bookingId,
    array $featureIds
): void {
    if (empty($featureIds)) {
        return;
    }

    $stmt = $db->prepare("
        INSERT INTO addOn_bookings (booking_id, feature_id)
        VALUES (?, ?)
    ");

    foreach ($featureIds as $featureId) {
        $stmt->execute([$bookingId, (int)$featureId]);
    }
}

function getBookingActivities(PDO $db, int $bookingId): array
{
    $stmt = $db->prepare("
        SELECT f.feature AS name, f.price
        FROM addOn_bookings ab
        JOIN features f ON f.id = ab.feature_id
        WHERE ab.booking_id = ?
    ");
    $stmt->execute([$bookingId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
