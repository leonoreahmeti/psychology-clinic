<?php

session_start();

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| ADMIN SECURITY
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

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE BOOKING
|--------------------------------------------------------------------------
*/

try {

    // Kontrollo a ekziston booking-u

    $checkStmt = $pdo->prepare("
        SELECT id
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


    // Fshije booking-un

    $stmt = $pdo->prepare("
        DELETE FROM bookings
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    echo json_encode([
        'success' => true,
        'message' => 'Booking deleted successfully.'
    ]);

    exit;


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not delete the booking.'
    ]);

    exit;
}