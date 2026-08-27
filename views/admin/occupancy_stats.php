<?php

declare(strict_types=1);

// Calculate occupancy stats for the last 30 days and next 30 days
$today = date('Y-m-d');
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
$thirtyDaysAhead = date('Y-m-d', strtotime('+30 days'));

$occupancyStats = getOccupancyByRoom($db, $thirtyDaysAgo, $thirtyDaysAhead);

?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Room</th>
                <th>Occupied Nights</th>
                <th>Available Nights</th>
                <th>Occupancy %</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($occupancyStats)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No occupancy data available</td>
                </tr>
            <?php else: ?>
                <?php foreach ($occupancyStats as $stat): ?>
                    <?php
                    $totalNights = $stat['booked'] + $stat['available'];
                    $occupancyPercent = $totalNights > 0
                        ? round(($stat['booked'] / $totalNights) * 100)
                        : 0;

                    // Color-code the occupancy percentage
                    $percentClass = match (true) {
                        $occupancyPercent >= 80 => 'text-success fw-bold',
                        $occupancyPercent >= 50 => 'text-warning fw-bold',
                        default => 'text-danger fw-bold',
                    };
                    ?>
                    <tr>
                        <td><strong>Room <?= htmlspecialchars((string)$stat['room_rank']); ?></strong></td>
                        <td><?= (int)$stat['booked']; ?></td>
                        <td><?= (int)$stat['available']; ?></td>
                        <td>
                            <span class="<?= $percentClass; ?>">
                                <?= $occupancyPercent; ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4 text-muted small">
    <p><em>Occupancy stats for the period: <?= htmlspecialchars($thirtyDaysAgo); ?> to <?= htmlspecialchars($thirtyDaysAhead); ?></em></p>
</div>
