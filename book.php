<?php require_once __DIR__ . '/app/autoload.php'; ?>

<?php

$room = loadRoom($db);

if (!$room) {
    die('Invalid room');
}

?>
<?php require_once __DIR__ . '/views/header.php'; ?>

<?php
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger" role="alert">';
    echo htmlspecialchars($_GET['error']);
    echo '</div>';
}

$roomId = (int)$room['id'];

// Restore previously submitted form data after a validation error (Quick-Win #4).
// process_booking.php stashes it in the session as one-time "flash" data.
$formData = $_SESSION['booking_form_data'] ?? null;
unset($_SESSION['booking_form_data']);

$prefillStartDate  = $formData['start_date'] ?? $admin['start-date'];
$prefillEndDate    = $formData['end_date'] ?? $admin['end-date'];
$prefillActivities = $formData['activities'] ?? [];
$prefillUser       = $formData['user'] ?? '';

// The room's booking window (admin.php) is a fixed demo period. The "min" date
// used to block past-date selection should be today's date, but must never be
// later than the start of that booking window - otherwise the entire (fixed)
// demo calendar would become unselectable if the server clock has moved past it.
$minSelectableDate = min(date('Y-m-d'), $admin['start-date']);

// Quick-Win #7: the availability grid must reflect whichever month the
// start_date field is actually showing, rather than a hardcoded month.
// Fall back to the current month if the prefilled start date is missing/invalid.
$year  = (int)date('Y');
$month = (int)date('n');

if (!empty($prefillStartDate)) {
    try {
        $validatedStartDate = validateDateFormat($prefillStartDate);
        $year  = (int)date('Y', strtotime($validatedStartDate));
        $month = (int)date('n', strtotime($validatedStartDate));
    } catch (Exception $e) {
        // Invalid start date - fall back to the current month.
    }
}

$bookedDays = getBookedDaysForMonth(
    $db,
    $roomId,
    $year,
    $month
);

$daysInMonth = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
$monthName   = date('F', mktime(0, 0, 0, $month, 1, $year));
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
                <div class="calendar-month-nav">
                    <button type="button" class="calendar-nav-btn" id="prev-month" aria-label="Previous month">‹</button>
                    <h4 class="month" id="calendar-month-title"><?= htmlspecialchars($monthName) ?></h4>
                    <button type="button" class="calendar-nav-btn" id="next-month" aria-label="Next month">›</button>
                </div>
                <p class="month">Availability</p>
                <section class="calendar" id="booking-calendar" data-room-id="<?= $roomId ?>" data-year="<?= $year ?>" data-month="<?= $month ?>">
                    <div class="weekday-headers">
                        <div class="weekday">Sun</div>
                        <div class="weekday">Mon</div>
                        <div class="weekday">Tue</div>
                        <div class="weekday">Wed</div>
                        <div class="weekday">Thu</div>
                        <div class="weekday">Fri</div>
                        <div class="weekday">Sat</div>
                    </div>
                    <?php
                    // Get first day of month (0=Sunday, 1=Monday, etc.)
                    $firstDayOfWeek = (int)date('w', mktime(0, 0, 0, $month, 1, $year));

                    // Add empty cells for days before the 1st
                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                        echo "<div class=\"day placeholder\"></div>";
                    }

                    // Add all days of the month
                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $date  = sprintf('%04d-%02d-%02d', $year, $month, $i);
                        $class = in_array($i, $bookedDays, true) ? 'day booked' : 'day';
                        echo "<div class=\"$class\" data-date=\"$date\">$i</div>";
                    }
                    ?>

                </section>
                <p class="calendar-feedback" id="calendar-feedback"></p>
            </div>
        </div>
        <div class="date-picker">
            <form method="POST" action="./app/users/process_booking.php" id="selection" data-room-price="<?= (int)$room['price']; ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">
                <div id="form-error-container" class="hidden mb-3">
                    <div class="alert alert-danger" role="alert" id="form-error-message"></div>
                </div>
                <div class="selections">
                    <fieldset class="room-dates">
                        <label for="start_date" class="form-label mt-4">Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($prefillStartDate) ?>" min="<?= htmlspecialchars($minSelectableDate) ?>" required>

                        <label for="end_date" class="form-label mt-4">End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($prefillEndDate) ?>" min="<?= htmlspecialchars($minSelectableDate) ?>" required>
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
                                    id="feature-<?= (int)$feature['id'] ?>" data-price="<?= (int)$feature['price'] ?>"
                                    <?= in_array((int)$feature['id'], $prefillActivities, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="feature-<?= (int)$feature['id'] ?>">
                                    <?= $feature['feature'] ?> — $<?= (int)$feature['price'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                    <fieldset class="user-info">

                        <div>
                            <label class="col-form-label mt-4" for="user">Username:</label>
                            <input type="text" class="form-control" placeholder="First Name" id="user" name="user" value="<?= htmlspecialchars($prefillUser) ?>" required>
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
                <button type="submit" class="btn-secondary" id="booking-submit-btn">
                    <span id="booking-btn-text">Complete Booking</span>
                    <span id="booking-btn-spinner" class="spinner-border spinner-border-sm ms-2 hidden" role="status" aria-hidden="true"></span>
                </button>
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
        <div class="mb-3">
            Enter your username, API key, and desired amount below, then hit submit to generate a transferCode from Yrgopelag Central Bank.
        </div>

        <div id="tc-error-container" class="hidden mb-3">
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <span id="tc-error-message"></span>
            </div>
        </div>

        <div id="tc-success-container" class="hidden mb-3">
            <div class="alert alert-success" role="alert">
                <strong>Success!</strong> Your transferCode is:
                <div class="mt-2 mb-3">
                    <code id="tc-code-display"></code>
                </div>
                <button type="button" class="btn btn-sm btn-success" id="use-code-btn">
                    Use This Code
                </button>
                <small class="d-block mt-2 text-muted">Click "Use This Code" to automatically fill the booking form.</small>
            </div>
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
                <input class="form-control" type="number" name="amount" id="amount" min="1" required disabled>
                <small class="form-text">Auto-filled from your selected dates and activities.</small>
            </div>

            <button type="submit" class="btn btn-primary" id="tc-submit-btn">
                <span id="tc-btn-text">Get transferCode</span>
                <span id="tc-btn-spinner" class="spinner-border spinner-border-sm ms-2 hidden" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>