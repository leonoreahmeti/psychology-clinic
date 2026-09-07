<?php

session_start();

/*
|--------------------------------------------------------------------------
| ADMIN SECURITY
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {
    header('Location: login.php');
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

    header('Location: messages.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE MESSAGE
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | CHECK MESSAGE
    |--------------------------------------------------------------------------
    */

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM contact_messages
        WHERE id = :id
        LIMIT 1
    ");

    $checkStmt->execute([
        ':id' => $id
    ]);

    $message = $checkStmt->fetch(PDO::FETCH_ASSOC);


    if (!$message) {

        header('Location: messages.php');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM contact_messages
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    header(
        'Location: messages.php?deleted=1'
    );

    exit;


} catch (PDOException $e) {

    header(
        'Location: messages.php?error=1'
    );

    exit;
}
