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

require_once __DIR__ . '/../config/database.php';


/*
|--------------------------------------------------------------------------
| ONLY POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: news.php');

    exit;
}


$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$status = $_POST['status'] ?? '';


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (
    !$id ||
    !in_array(
        $status,
        ['draft', 'published'],
        true
    )
) {

    header('Location: news.php');

    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        UPDATE news
        SET status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | SUCCESS MESSAGE
    |--------------------------------------------------------------------------
    */

    if ($status === 'published') {

        header(
            'Location: news.php?success=published'
        );

    } else {

        header(
            'Location: news.php?success=draft'
        );

    }

    exit;


} catch (PDOException $e) {

    header(
        'Location: news.php?error=status'
    );

    exit;
}
