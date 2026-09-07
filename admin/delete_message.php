<?php

session_start();

header('Content-Type: application/json');

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

require_once __DIR__ . '/../config/database.php';

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

try {

    $stmt = $pdo->prepare("
        DELETE FROM contact_messages
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Message deleted successfully.'
    ]);

    exit;

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not delete the message.'
    ]);

    exit;
}
