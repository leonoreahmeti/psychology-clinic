<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

try {

    $stmt = $pdo->prepare("
        SELECT booking_date, booking_time
        FROM bookings
        WHERE status != 'cancelled'
        ORDER BY booking_date, booking_time
    ");

    $stmt->execute();

    $bookings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bookings[] = [
            'date' => $row['booking_date'],
            'time' => substr($row['booking_time'], 0, 5)
        ];
    }

    echo json_encode($bookings);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Could not load bookings.'
    ]);
}
?>