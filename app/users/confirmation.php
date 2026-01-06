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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/styles/custom.css">
    <link rel="stylesheet" href="../../assets/styles/base.css">
    <link rel="stylesheet" href="../../assets/styles/layout.css">
    <link rel="stylesheet" href="../../assets/styles/components.css">
    <link rel="stylesheet" href="../../assets/styles/rooms.css">
    <link rel="stylesheet" href="../../assets/styles/booking.css">
    <link rel="stylesheet" href="../../assets/styles/reviews.css">
</head>

<body data-bs-theme="dark">

    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../index.php"><img src="../../assets/images/logo.png" alt="logo" id="logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../about.php">About</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Book Now!</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="../../book.php?room_id=1">Budget Room</a>
                            <a class="dropdown-item" href="../../book.php?room_id=2">Standard Room</a>
                            <a class="dropdown-item" href="../../book.php?room_id=3">Luxury Room</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <h1>Booking Confirmed</h1>

    <p>Booking ID: <?= $booking['id']; ?></p>
    <p>Room: <?= ucfirst($booking['room_rank']); ?></p>
    <p>Check-in: <?= $booking['start_date']; ?> 15:00</p>
    <p>Check-out: <?= $booking['end_date']; ?> 11:00</p>
    <p>Number of Nights: <?= $nights; ?> ($<?= $booking['room_price']; ?>/night)</p>
    <p>Cost for Room: $<?= $roomTotal; ?></p>

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

    <footer></footer>

    <script src="../../assets/scripts/script.js"></script>
    <script src="../../assets/scripts/transfercode.js"></script>
    <script src="../../assets/scripts/booking-calculator.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>