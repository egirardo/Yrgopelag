<?php

require_once __DIR__ . "/../autoload.php";


try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php');
    }

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        throw new Exception('CSRF token validation failed');
    }

    // Validate input
    if (!isset($_POST['room_id']) || !isset($_POST['start_date']) || !isset($_POST['end_date']) ||
        !isset($_POST['transfer_code']) || !isset($_POST['user'])) {
        throw new Exception('Missing required fields');
    }

    $roomId       = (int)$_POST['room_id'];
    $startDate    = validateDateFormat($_POST['start_date']);
    $endDate      = validateDateFormat($_POST['end_date']);
    $transferCode = validateTransferCodeFormat($_POST['transfer_code']);
    $guestName    = validateGuestName($_POST['user']);

    // Quick-Win #2: reject non-positive-night stays server-side too,
    // since client-side checks can always be bypassed.
    validateDateRange($startDate, $endDate);

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

    // Deposit 
    depositTransferCode($transferCode);

    // Save booking locally 
    $db->beginTransaction();

    if (bookingConflicts($db, $roomId, $startDate, $endDate)) {
        throw new Exception('Dates already booked');
    }

    $userId = getOrCreateGuest($db, $guestName);

    $bookingId = saveBooking($db, $roomId, $startDate, $endDate, $userId, $totalCost);

    saveBookingActivities($db, $bookingId, $activities);

    $db->commit();

    header('Location: confirmation.php?booking_id=' . $bookingId);
    exit;
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    // Quick-Win #4: preserve dates, selected activities, and the guest name
    // so the user doesn't have to re-enter everything after a failed booking.
    // Deliberately excludes transfer_code/total_cost (sensitive/derived values).
    $_SESSION['booking_form_data'] = [
        'start_date' => is_string($_POST['start_date'] ?? null) ? $_POST['start_date'] : '',
        'end_date'   => is_string($_POST['end_date'] ?? null) ? $_POST['end_date'] : '',
        'activities' => array_map('intval', $_POST['activities'] ?? []),
        'user'       => is_string($_POST['user'] ?? null) ? $_POST['user'] : '',
    ];

    $redirectRoomId = $_POST['room_id'] ?? ($roomId ?? '');

    header('Location: ../../book.php?room_id=' . urlencode((string)$redirectRoomId) . '&error=' . urlencode($e->getMessage()));
    exit;
}
