<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ]);

    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$clientName = trim($data['client_name'] ?? '');
$clientEmail = trim($data['client_email'] ?? '');
$bookingDate = trim($data['booking_date'] ?? '');
$bookingTime = trim($data['booking_time'] ?? '');
$service = trim($data['service'] ?? '');

if (
    !$clientName ||
    !$clientEmail ||
    !$bookingDate ||
    !$bookingTime ||
    !$service
) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields.'
    ]);

    exit;
}

if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]);

    exit;
}

try {

    // Check if this date and time are already booked
    $checkStmt = $pdo->prepare("
        SELECT id
        FROM bookings
        WHERE booking_date = :booking_date
        AND booking_time = :booking_time
        LIMIT 1
    ");

    $checkStmt->execute([
        ':booking_date' => $bookingDate,
        ':booking_time' => $bookingTime
    ]);

    if ($checkStmt->fetch()) {
        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => 'This time slot is already booked. Please choose another time.'
        ]);

        exit;
    }

    // Create booking
    $stmt = $pdo->prepare("
        INSERT INTO bookings
        (
            client_name,
            client_email,
            booking_date,
            booking_time,
            service
        )
        VALUES
        (
            :client_name,
            :client_email,
            :booking_date,
            :booking_time,
            :service
        )
    ");

    $stmt->execute([
        ':client_name' => $clientName,
        ':client_email' => $clientEmail,
        ':booking_date' => $bookingDate,
        ':booking_time' => $bookingTime,
        ':service' => $service
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully.',
        'booking_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not create booking.'
    ]);
}