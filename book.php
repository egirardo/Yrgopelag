<?php require_once __DIR__ . '/app/autoload.php'; ?>
<?php require_once __DIR__ . '/views/header.php'; ?>

<?php

$room = loadRoom($db);

if (!$room) {
    die('Invalid room');
}

if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger" role="alert">';
    echo htmlspecialchars($_GET['error']);
    echo '</div>';
}

$roomId = (int)$room['id'];

$year  = 2026;
$month = 1;

$bookedDays = getBookedDaysForMonth(
    $db,
    $roomId,
    $year,
    $month
);

$daysInMonth = (int)date('t');
?>

<article class="booking-dates" data-bs-theme="dark">

    <h1>Book Your <?= $room['rank'] ?> Room Now!</h1>
    <div class="calendar-datepicker-container">
        <div class="calendar-key-container">
            <div class="key-container">
                <h5>Key:</h5>
                <div class="key-day">
                    <p>Booked:</p>
                    <div class="day booked"></div>
                </div>
                <div class="key-day">
                    <p>Available:</p>
                    <div class="day"></div>
                </div>
            </div>
            <div class="calendar-container">
                <h4 class="month">January</h4>
                <p class="month">Availability</p>
                <section class="calendar">
                    <?php

                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $class = in_array($i, $bookedDays, true) ? 'day booked' : 'day';
                        echo "<div class=\"$class\">$i</div>";
                    }
                    ?>

                </section>
            </div>
        </div>
        <div class="date-picker">
            <form method="POST" action="./app/users/process_booking.php" id="selection" data-room-price="<?= (int)$room['price']; ?>">
                <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">
                <div class="selections">
                    <fieldset class="room-dates">
                        <label for="start_date" class="form-label mt-4">Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $admin['start-date']; ?>" required>

                        <label for="end_date" class="form-label mt-4">End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $admin['end-date']; ?>" required>
                    </fieldset>
                    <fieldset class="addOns">
                        <legend class="form-label mt-4 top">Additional Actvities:</legend>

                        <?php foreach ($features as $feature) : ?>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="activities[]"
                                    value="<?= (int)$feature['id'] ?>"
                                    id="feature-<?= (int)$feature['id'] ?>" data-price="<?= (int)$feature['price'] ?>">
                                <label class="form-check-label" for="feature-<?= (int)$feature['id'] ?>">
                                    <?= $feature['feature'] ?> — $<?= (int)$feature['price'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                    <fieldset class="user-info">

                        <div>
                            <label class="col-form-label mt-4" for="user">Username:</label>
                            <input type="text" class="form-control" placeholder="First Name" id="user" name="user" required>
                        </div>
                        <div>
                            <label class="col-form-label mt-4" for="transferCode">Transfer Code:</label>
                            <input type="text" class="form-control" placeholder="Transfer Code" id="transferCode" name="transfer_code" required>
                        </div>
                        <div>
                            <label class="col-form-label mt-4" for="totalCost">Total:</label>
                            <input type="text" class="form-control" placeholder="Total Cost" id="totalCost" name="total_cost" readonly>
                        </div>
                    </fieldset>
                </div>
                <input type="submit" value="Complete Booking" class="btn-secondary">
                <!-- need to fix styling on calendar and arrow icons for form -->
            </form>
        </div>
    </div>
</article>

<button class="btn btn-primary tc-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#transferCodeService" aria-controls="transferCodeService">transferCode Service</button>
<div class="offcanvas offcanvas-start" tabindex="-1" id="transferCodeService" aria-labelledby="transferCodeServiceLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="transferCodeServiceLabel">Get your transferCode</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            Enter your username, API key, and desired amount below, then hit submit to generate a transferCode from Yrgopelag Central Bank.
        </div>

        <form action="" method="POST" id="tc-offcanvas">
            <div class="mb-3">
                <label for="user" class="form-label">Username</label>
                <input class="form-control" type="text" name="user" id="user" placeholder="Sbargle" required>
                <small class="form-text">Please provide your first name.</small>
            </div>

            <div class="mb-3">
                <label for="api_key" class="form-label">API Key</label>
                <input class="form-control" type="text" name="api_key" id="api_key" required>
                <small class="form-text">Please provide your API Key.</small>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input class="form-control" type="number" name="amount" id="amount" required>
                <small class="form-text">Please confirm the amount you wish to withdraw.</small>
            </div>

            <button type="submit" class="btn btn-primary">Get transferCode</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>