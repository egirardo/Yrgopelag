<?php require __DIR__ . '/app/autoload.php'; ?>
<?php require __DIR__ . '/views/header.php'; ?>

<article class="booking-dates" data-bs-theme="dark">

    <h1>Book your stay now!</h1>
    <div class="calendar-datepicker-container">
        <div class="calendar-key">
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
                for ($i = 1; $i <= 31; $i++) :
                ?>
                    <?php if (in_array($i, $admin['booked'])) : ?>
                        <div class="day booked"><?= $i; ?></div>
                    <?php else : ?>
                        <div class="day"><?= $i; ?></div>
                    <?php endif; ?>
                <?php endfor; ?>
            </section>
        </div>
        <div class="date-picker">
            <form method="POST" action="process_booking.php">
                <!-- make process_booking.php file -->
                <label for="room_picker">Choose a room:</label>
                <select name="room_picker" id="room_picker" required>
                    <option value="">--Please choose an option--</option>
                    <option value="budget">Budget - $<?= $admin['room_prices']['budget']; ?></option>
                    <option value="standard">Standard - $<?= $admin['room_prices']['standard']; ?></option>
                    <option value="luxury">Luxury - $<?= $admin['room_prices']['luxury']; ?></option>
                </select>

                <label for=" event_date">Start Date:</label>
                <input type="date" id="start_date" name="event_date" value="<?= $admin['today']; ?>" required>

                <label for="event_date">End Date:</label>
                <input type="date" id="end_date" name="event_date" value="<?= $admin['today']; ?>" required>
                <input type="submit" value="Book Now">
            </form>
        </div>
        <!-- once user hits submit on the date-picker section, a checkout section appears at the bottom summarizing their choices and asking for their username, transfercode, and amount. features can also be added here. -->

    </div>
    </div>

    <form action="app/users/book.php" method="POST">
        <label class="form-label mt-4">Withdraw</label>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input class="form-control" type="username" name="username" id="username" placeholder="Sbargle" required>
            <small class="form-text">Please provide your username (first name).</small>
        </div>

        <div class="mb-3">
            <label for="transferCode" class="form-label">Transfer Code</label>
            <input class="form-control" type="text" name="transferCode" id="transferCode" required>
            <small class="form-text">Please provide your centralbank transfer code.</small>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
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

        <form action="app/users/book.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input class="form-control" type="username" name="username" id="username" placeholder="Sbargle" required>
                <small class="form-text">Please provide your first name.</small>
            </div>

            <div class="mb-3">
                <label for="apiKey" class="form-label">API Key</label>
                <input class="form-control" type="text" name="apiKey" id="apiKey" required>
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

<?php require __DIR__ . '/views/footer.php'; ?>