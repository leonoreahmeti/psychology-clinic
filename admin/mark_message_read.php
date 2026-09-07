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
| MARK MESSAGE AS READ
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        UPDATE contact_messages
        SET status = 'read'
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | BACK TO MESSAGES
    |--------------------------------------------------------------------------
    */

    header('Location: messages.php');
    exit;


} catch (PDOException $e) {

    header('Location: messages.php');
    exit;
}
