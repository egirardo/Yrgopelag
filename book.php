<?php require __DIR__ . '/app/autoload.php'; ?>
<?php require __DIR__ . '/views/header.php'; ?>


<article class="booking-dates" data-bs-theme="dark">

    <h1>Book your stay now!</h1>
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
        </div>
        <div class="date-picker">
            <form method="POST" action="process_booking.php">
                <div class="selections">
                    <!-- make process_booking.php file -->
                    <fieldset class="room-dates">
                        <label for="room_picker" class="form-label mt-4 top">Choose a room:</label>
                        <select name="room_picker" class="form-select" id="room_picker" required>
                            <option value="">-Select-</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option id="<?= $room['rank']; ?>" value="<?= $room['price']; ?>"><?= $room['rank']; ?> - $<?= $room['price']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="event_date" class="form-label mt-4">Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="event_date" value="<?= $admin['start-date']; ?>" required>

                        <label for="event_date" class="form-label mt-4">End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="event_date" value="<?= $admin['end-date']; ?>" required>
                    </fieldset>
                    <fieldset class="addOns">
                        <legend class="form-label mt-4 top">Additional Actvities</legend>

                        <?php foreach ($features as $feature) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="<?= $feature['price'] ?>" id="<?= $feature['feature']; ?>">
                                <label class="form-check-label" for="<?= $feature['feature']; ?>">
                                    <?= $feature['feature'] . " - $" . $feature['price']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div id="prelim-total">
                            <h6>Total:</h6>
                        </div>
                    </fieldset>
                </div>
                <input type="submit" value="Book Now">
                <!-- need to fix styling on calendar and arrow icons for form -->
            </form>
        </div>
    </div>


    <!-- once user hits submit on the date-picker section, a checkout section appears at the bottom summarizing their choices and asking for their username, transfercode, and amount. features can also be added here. -->
    <div class="check-out hidden">
        <div class="card border-secondary mb-3" style="max-width: 20rem;">
            <div class="card-header">Order</div>
            <div class="card-body">
                <h4 class="card-title">Order Summary:</h4>
                <p class="card-text order"></p>
            </div>
        </div>
        <div class="card border-secondary mb-3" style="max-width: 20rem;">
            <div class="card-header">Checkout</div>
            <div class="card-body">
                <h4 class="card-title">Complete Your Order:</h4>
                <div>
                    <label class="col-form-label mt-4" for="user">Username</label>
                    <input type="text" class="form-control" placeholder="First Name" id="user">
                </div>
                <div>
                    <label class="col-form-label mt-4" for="transferCode">Transfer Code</label>
                    <input type="text" class="form-control" placeholder="Transfer Code" id="transferCode">
                </div>
                <div>
                    <label class="col-form-label mt-4" for="totalCost">Confirm Total:</label>
                    <input type="text" class="form-control" placeholder="Enter total shown in order panel" id="totalCost">
                </div>
            </div>
        </div>
    </div>


    <!-- below code will be changed later to take on final booking processing -->
    <!-- <form action="app/users/book.php" method="POST">
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
    </form> -->
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
            <!-- if !isset $session {show above code} else {transfercode: x amount: x}set up session variable so that when session is set then the display is instead your transfer code: x amount: x -->
        </form>
    </div>
</div>

<?php require __DIR__ . '/views/footer.php'; ?>