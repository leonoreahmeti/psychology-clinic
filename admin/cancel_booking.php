<?php

session_start();

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| SECURITY - vetëm admini mundet me anulu
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/database.php';

/*
|--------------------------------------------------------------------------
| GET BOOKING ID
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID.'
    ]);

    exit;
}

try {

    /*
    |--------------------------------------------------------------------------
    | Kontrollo nëse booking ekziston
    |--------------------------------------------------------------------------
    */

    $checkStmt = $pdo->prepare("
        SELECT id, status
        FROM bookings
        WHERE id = :id
        LIMIT 1
    ");

    $checkStmt->execute([
        ':id' => $id
    ]);

    $booking = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Booking not found.'
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Nëse është veç e anuluar
    |--------------------------------------------------------------------------
    */

    if ($booking['status'] === 'cancelled') {

        echo json_encode([
            'success' => false,
            'message' => 'This booking is already cancelled.'
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL BOOKING
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE bookings
        SET status = 'cancelled'
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully.'
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not cancel the booking.'
    ]);
}