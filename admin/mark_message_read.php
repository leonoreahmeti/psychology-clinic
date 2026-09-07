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
| GET MESSAGE ID
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
        'message' => 'Invalid message ID.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| MARK MESSAGE AS READ
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | CHECK MESSAGE
    |--------------------------------------------------------------------------
    */

    $checkStmt = $pdo->prepare("
        SELECT id, status
        FROM contact_messages
        WHERE id = :id
        LIMIT 1
    ");

    $checkStmt->execute([
        ':id' => $id
    ]);

    $message = $checkStmt->fetch(PDO::FETCH_ASSOC);


    if (!$message) {

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Message not found.'
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | MARK AS READ
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE contact_messages
        SET status = 'read'
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    echo json_encode([
        'success' => true,
        'message' => 'Message marked as read.'
    ]);

    exit;


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not update the message.'
    ]);

    exit;
}
