<?php

declare(strict_types=1);

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
