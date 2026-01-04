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

function bookingConflicts(
    PDO $db,
    int $roomId,
    string $startDate,
    string $endDate
): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM bookings
        WHERE room_id = ?
        AND NOT (
            end_date < ?
            OR start_date > ?
        )
    ");

    $stmt->execute([
        $roomId,
        $startDate,
        $endDate
    ]);

    return (int)$stmt->fetchColumn() > 0;
}

function getOrCreateGuest(PDO $db, string $guestName): int
{
    // Check if guest exists
    $stmt = $db->prepare("SELECT id FROM users WHERE firstName = ?");
    $stmt->execute([$guestName]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
        return (int)$userId;
    }

    // Create new guest
    $stmt = $db->prepare("INSERT INTO users (firstName) VALUES (?)");
    $stmt->execute([$guestName]);

    return (int)$db->lastInsertId();
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

    $stmt->execute([
        $roomId,
        $userId,
        $startDate,
        $endDate,
        $totalCost
    ]);

    return (int)$db->lastInsertId();
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
        $stmt->execute([
            $bookingId,
            (int)$featureId
        ]);
    }
}

function centralbankPost(string $endpoint, array $payload): array
{
    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'ignore_errors' => true
        ]
    ]);

    $url = CENTRALBANK_BASE . $endpoint;

    $response = file_get_contents($url, false, $context);


    if ($response === false) {
        throw new Exception('Centralbank request failed');
    }

    if (empty($response)) {
        throw new Exception('Centralbank returned empty response');
    }

    $data = json_decode($response, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response: ' . json_last_error_msg());
    }

    if ($data === null) {
        $data = [];
    }

    if (isset($data['error'])) {
        throw new Exception($data['error']);
    }

    return $data;
}

function validateTransferCode(string $transferCode, int $totalCost): void
{
    centralbankPost('/transferCode', [
        'transferCode' => $transferCode,
        'totalCost'    => $totalCost
    ]);
}


function postReceipt(
    string $guestName,
    string $arrival,
    string $departure,
    array $features = [],
    int $stars = 3 //change star rating based on true rating later
): void {
    centralbankPost('/receipt', [
        'user'           => HOTEL_USER,
        'api_key'        => HOTEL_API_KEY,
        'guest_name'     => $guestName,
        'arrival_date'   => $arrival,
        'departure_date' => $departure,
        'features_used'  => $features,
        'star_rating'    => $stars
    ]);
}

function depositTransferCode(string $transferCode): void
{
    centralbankPost('/deposit', [
        'user'         => HOTEL_USER,
        'transferCode' => $transferCode
    ]);
}


// pricing functions

function getRoomPrice(PDO $db, int $roomId): int
{
    $stmt = $db->prepare("
        SELECT price 
        FROM rooms 
        WHERE id = ?
    ");
    $stmt->execute([$roomId]);

    $price = $stmt->fetchColumn();

    if ($price === false) {
        throw new Exception('Invalid room');
    }

    return (int)$price;
}

function calculateNights(string $start, string $end): int
{
    $startDate = new DateTime($start);
    $endDate   = new DateTime($end);

    $nights = (int)$startDate->diff($endDate)->days;

    if ($nights <= 0) {
        throw new Exception('Invalid date range');
    }

    return $nights;
}

function getActivityTotalByIds(PDO $db, array $featureIds): int
{
    if (empty($featureIds)) {
        return 0;
    }

    // Ensure integers
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

function calculateBookingCost(
    PDO $db,
    int $roomId,
    string $startDate,
    string $endDate,
    array $featureIds = []
): int {
    $roomPrice = getRoomPrice($db, $roomId);
    $nights    = calculateNights($startDate, $endDate);

    $total = $roomPrice * $nights;

    $total += getActivityTotalByIds($db, $featureIds);

    return $total;
}




// confirmation
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


function getBookingActivities(PDO $db, int $bookingId): array
{
    $stmt = $db->prepare("
        SELECT f.feature, f.price
        FROM addOn_bookings ab
        JOIN features f ON f.id = ab.feature_id
        WHERE ab.booking_id = ?
    ");
    $stmt->execute([$bookingId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
