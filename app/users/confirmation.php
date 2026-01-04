<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../../views/header.php';

if (!isset($_GET['booking_id'])) {
    redirect('index.php');
}

$bookingId = (int)$_GET['booking_id'];

try {
    $booking    = getBooking($db, $bookingId);
    $activities = getBookingActivities($db, $bookingId);
} catch (Exception $e) {
    redirect('index.php');
}
?>

<h1>Booking Confirmed</h1>

<p>Booking ID: <?= $booking['id'] ?></p>
<p>Room: <?= $booking['room_rank']; ?></p>
<p>Check-in: <?= $booking['start_date']; ?> 15:00</p>
<p>Check-out: <?= $booking['end_date']; ?> 11:00</p>

<h3>Selected Activities</h3>

<?php if (empty($activities)): ?>
    <p>No additional activities selected.</p>
<?php else: ?>
    <ul>
        <?php foreach ($activities as $activity): ?>
            <li>
                <?= $activity['name']; ?>
                — $<?= (int)$activity['price']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Total Cost</h3>
<p>$<?= (int)$booking['total_cost']; ?></p>

<p>Thank you for your booking! Your booking has been recorded.</p>


<?php require_once __DIR__ . '/../../views/footer.php'; ?>