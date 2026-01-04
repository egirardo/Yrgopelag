<?php

require_once __DIR__ . "/../autoload.php";


try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php');
    }

    $roomId       = (int)$_POST['room_id'];
    $startDate    = $_POST['start_date'];
    $endDate      = $_POST['end_date'];
    $transferCode = $_POST['transfer_code'];
    $guestName    = trim($_POST['user']);

    $activities = $_POST['activities'] ?? [];

    $totalCost = calculateBookingCost($db, $roomId, $startDate, $endDate, $activities);

    // Validate transfer code !
    validateTransferCode($transferCode, $totalCost);

    // Post receipt (analytics)
    try {
        postReceipt(
            guestName: $guestName,
            arrival: $startDate,
            departure: $endDate,
            features: [],
            stars: 5
        );
    } catch (Exception $e) {
        error_log('Receipt posting failed: ' . $e->getMessage());
    }

    // Deposit (this consumes the code) !
    depositTransferCode($transferCode);

    // Save booking locally (DB transaction) !
    $db->beginTransaction();

    if (bookingConflicts($db, $roomId, $startDate, $endDate)) {
        throw new Exception('Dates already booked');
    }

    $userId = getOrCreateGuest($db, $guestName);

    $bookingId = saveBooking($db, $roomId, $startDate, $endDate, $userId, $totalCost);

    saveBookingActivities($db, $bookingId, $activities);

    $db->commit();

    redirect('./confirmation.php?booking_id=' . $bookingId);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    redirect('/../../book.php?room_id=' . $roomId . '&error=' . urlencode($e->getMessage()));
}
