<?php
require_once __DIR__ . '/../autoload.php';

if (!isset($_GET['booking_id'])) {
    redirect('../../index.php');
}

$bookingId = (int)$_GET['booking_id'];

try {
    $booking    = getBooking($db, $bookingId);
    $activities = getBookingActivities($db, $bookingId);
} catch (Exception $e) {
    redirect('../../index.php');
}

$checkIn = new DateTime($booking['start_date']);
$checkOut = new DateTime($booking['end_date']);
$nights = $checkIn->diff($checkOut)->days;
$roomTotal = (int)$booking['room_price'] * $nights;
?>
<?php require __DIR__ . '/../../views/header.php'; ?>
    <div class="confirmation-container">
        <h1>Booking Confirmed</h1>

        <p>Booking ID: <?= htmlspecialchars((string)$booking['id'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Room: <?= htmlspecialchars(ucfirst($booking['room_rank']), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Check-in: <?= htmlspecialchars($booking['start_date'], ENT_QUOTES, 'UTF-8'); ?> 15:00</p>
        <p>Check-out: <?= htmlspecialchars($booking['end_date'], ENT_QUOTES, 'UTF-8'); ?> 11:00</p>
        <p>Number of Nights: <?= htmlspecialchars((string)$nights, ENT_QUOTES, 'UTF-8'); ?> ($<?= htmlspecialchars((string)$booking['room_price'], ENT_QUOTES, 'UTF-8'); ?>/night)</p>
        <p>Cost for Room: $<?= htmlspecialchars((string)$roomTotal, ENT_QUOTES, 'UTF-8'); ?></p>

        <h3>Selected Activities</h3>

        <?php if (empty($activities)): ?>
            <p>No additional activities selected.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($activities as $activity): ?>
                    <li>
                        <?= htmlspecialchars($activity['name'], ENT_QUOTES, 'UTF-8'); ?>
                        — $<?= htmlspecialchars((string)$activity['price'], ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3>Total Cost</h3>
        <p>$<?= htmlspecialchars((string)$booking['total_cost'], ENT_QUOTES, 'UTF-8'); ?></p>

        <p>Thank you for your booking! Your booking has been recorded.</p>
    </div>

<?php require __DIR__ . '/../../views/footer.php'; ?>