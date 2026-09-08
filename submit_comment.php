<?php

require_once __DIR__ . '/config/database.php';


/*
|--------------------------------------------------------------------------
| ONLY POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: news.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$newsId = filter_input(
    INPUT_POST,
    'news_id',
    FILTER_VALIDATE_INT
);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$comment = trim($_POST['comment'] ?? '');


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (!$newsId) {

    header('Location: news.php');
    exit;
}

if ($name === '' || $email === '' || $comment === '') {

    header(
        'Location: news.php?comment=error'
    );

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    header(
        'Location: news.php?comment=invalid_email'
    );

    exit;
}


if (mb_strlen($name) > 100) {

    header(
        'Location: news.php?comment=error'
    );

    exit;
}


if (mb_strlen($comment) > 2000) {

    header(
        'Location: news.php?comment=error'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK NEWS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT id
        FROM news
        WHERE id = :id
        AND status = 'published'
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $newsId
    ]);

    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$news) {

        header(
            'Location: news.php?comment=error'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT COMMENT
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO news_comments
        (
            news_id,
            name,
            email,
            comment,
            status
        )
        VALUES
        (
            :news_id,
            :name,
            :email,
            :comment,
            'pending'
        )
    ");

    $stmt->execute([
        ':news_id' => $newsId,
        ':name' => $name,
        ':email' => $email,
        ':comment' => $comment
    ]);


    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    header(
        'Location: news.php?comment=submitted'
    );

    exit;


} catch (PDOException $e) {

    header(
        'Location: news.php?comment=error'
    );

    exit;
}
