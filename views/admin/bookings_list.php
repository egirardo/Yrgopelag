<?php

declare(strict_types=1);

// Fetch bookings data
$upcomingBookings = getUpcomingBookings($db, 30);
$pastBookings = getPastBookings($db, 10);
$allBookings = array_merge($upcomingBookings, $pastBookings);

// Determine status for each booking
foreach ($allBookings as &$booking) {
    $today = date('Y-m-d');
    if ($booking['start_date'] > $today) {
        $booking['status'] = 'Upcoming';
    } elseif ($booking['end_date'] < $today) {
        $booking['status'] = 'Completed';
    } else {
        $booking['status'] = 'Current';
    }
}

?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Room</th>
                <th>Guest Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allBookings)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No bookings found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($allBookings as $booking): ?>
                    <tr>
                        <td>
                            <strong>Room <?= htmlspecialchars((string)$booking['room_rank']); ?></strong>
                        </td>
                        <td><?= htmlspecialchars((string)$booking['guest_name']); ?></td>
                        <td><?= htmlspecialchars((string)$booking['start_date']); ?></td>
                        <td><?= htmlspecialchars((string)$booking['end_date']); ?></td>
                        <td>
                            <?php
                            $statusClass = match($booking['status']) {
                                'Upcoming' => 'badge bg-info',
                                'Current' => 'badge bg-success',
                                'Completed' => 'badge bg-secondary',
                                default => 'badge bg-dark',
                            };
                            ?>
                            <span class="<?= $statusClass; ?>">
                                <?= htmlspecialchars($booking['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
