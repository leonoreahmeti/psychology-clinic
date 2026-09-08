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


if (!$id) {

    header('Location: news.php');

    exit;
}


try {

    /*
    |--------------------------------------------------------------------------
    | GET IMAGE
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT image
        FROM news
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $news = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$news) {

        header('Location: news.php');

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | DELETE COMMENTS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM news_comments
        WHERE news_id = :news_id
    ");

    $stmt->execute([
        ':news_id' => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | DELETE NEWS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM news
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | DELETE IMAGE FROM SERVER
    |--------------------------------------------------------------------------
    */

    if (
        !empty($news['image']) &&
        strpos(
            $news['image'],
            'uploads/news/'
        ) === 0
    ) {

        $imageFile =
            __DIR__ .
            '/../' .
            $news['image'];

        if (is_file($imageFile)) {

            unlink($imageFile);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    header(
        'Location: news.php?success=deleted'
    );

    exit;


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    header(
        'Location: news.php?error=delete'
    );

    exit;
}
