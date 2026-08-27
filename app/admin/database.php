<?php

declare(strict_types=1);

// Admin-specific database helpers (occupancy stats, upcoming/past
// bookings). These build on the general booking queries in
// app/database.php.

/**
 * Estimate the occupancy rate for a set of bookings over a date range.
 *
 * Occupancy is expressed as a percentage of "room-nights": the number of
 * nights actually booked (clamped to the given range) divided by the
 * total number of nights that were available across every room that
 * appears in $bookings, over that same range.
 *
 * Note: since this only receives an already-fetched list of bookings (not
 * a full room list), the denominator is based on the distinct rooms
 * represented in $bookings. Rooms with zero bookings in the period won't
 * be counted; use getOccupancyByRoom() if you need per-room figures
 * against every room in the hotel.
 *
 * @param array<int, array<string, mixed>> $bookings
 */
function calculateOccupancyRate(array $bookings, string $startDate, string $endDate): float
{
    $rangeStart = new DateTime($startDate);
    $rangeEnd = new DateTime($endDate);

    if ($rangeEnd < $rangeStart) {
        return 0.0;
    }

    $totalDays = (int)$rangeStart->diff($rangeEnd)->days + 1;

    $roomIds = [];
    $bookedNights = 0;

    foreach ($bookings as $booking) {
        $roomIds[(int)$booking['room_id']] = true;

        $overlapStart = max((string)$booking['start_date'], $startDate);
        $overlapEnd = min((string)$booking['end_date'], $endDate);

        if ($overlapStart <= $overlapEnd) {
            $bookedNights += (int)(new DateTime($overlapStart))->diff(new DateTime($overlapEnd))->days + 1;
        }
    }

    $roomCount = count($roomIds);

    if ($roomCount === 0 || $totalDays === 0) {
        return 0.0;
    }

    $totalRoomNights = $roomCount * $totalDays;

    return round(($bookedNights / $totalRoomNights) * 100, 2);
}

/**
 * Fetch bookings starting within the next $daysAhead days (inclusive of
 * today), joined with room and guest details.
 *
 * @return array<int, array<string, mixed>>
 */
function getUpcomingBookings(PDO $db, int $daysAhead = 7): array
{
    $today = date('Y-m-d');
    $future = date('Y-m-d', strtotime(sprintf('+%d days', $daysAhead)));

    $stmt = $db->prepare("
        SELECT b.*, r.rank AS room_rank, r.price AS room_price, u.firstName AS guest_name
        FROM bookings b
        JOIN rooms r ON r.id = b.room_id
        JOIN users u ON u.id = b.user_id
        WHERE b.start_date >= ? AND b.start_date <= ?
        ORDER BY b.start_date ASC
    ");

    $stmt->execute([$today, $future]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch the most recently completed bookings (end_date already in the
 * past), joined with room and guest details.
 *
 * @return array<int, array<string, mixed>>
 */
function getPastBookings(PDO $db, int $limit = 5): array
{
    $today = date('Y-m-d');

    $stmt = $db->prepare("
        SELECT b.*, r.rank AS room_rank, r.price AS room_price, u.firstName AS guest_name
        FROM bookings b
        JOIN rooms r ON r.id = b.room_id
        JOIN users u ON u.id = b.user_id
        WHERE b.end_date < ?
        ORDER BY b.end_date DESC
        LIMIT ?
    ");

    $stmt->bindValue(1, $today, PDO::PARAM_STR);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch booked/available night counts for every room.
 *
 * If $startDate and $endDate are both given, "booked" is the number of
 * nights (clamped to the range) that room had at least one booking, and
 * "available" is the remaining nights in that range. Without a range,
 * "booked" falls back to the room's total lifetime booking count and
 * "available" is null (not meaningful without a range).
 *
 * @return array<int, array{room_id: int, room_rank: mixed, booked: int, available: ?int}>
 */
function getOccupancyByRoom(PDO $db, ?string $startDate = null, ?string $endDate = null): array
{
    $rooms = getAllRooms($db);
    $useRange = $startDate !== null && $endDate !== null;

    $totalDays = 0;
    if ($useRange) {
        $rangeStart = new DateTime($startDate);
        $rangeEnd = new DateTime($endDate);
        $totalDays = $rangeEnd >= $rangeStart
            ? (int)$rangeStart->diff($rangeEnd)->days + 1
            : 0;
    }

    $result = [];

    foreach ($rooms as $room) {
        $roomId = (int)$room['id'];

        if ($useRange) {
            $bookedNights = 0;

            foreach (getBookingsByRoom($db, $roomId) as $booking) {
                $overlapStart = max((string)$booking['start_date'], $startDate);
                $overlapEnd = min((string)$booking['end_date'], $endDate);

                if ($overlapStart <= $overlapEnd) {
                    $bookedNights += (int)(new DateTime($overlapStart))->diff(new DateTime($overlapEnd))->days + 1;
                }
            }

            $result[] = [
                'room_id' => $roomId,
                'room_rank' => $room['rank'],
                'booked' => $bookedNights,
                'available' => max(0, $totalDays - $bookedNights),
            ];
        } else {
            $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ?");
            $stmt->execute([$roomId]);

            $result[] = [
                'room_id' => $roomId,
                'room_rank' => $room['rank'],
                'booked' => (int)$stmt->fetchColumn(),
                'available' => null,
            ];
        }
    }

    return $result;
}
