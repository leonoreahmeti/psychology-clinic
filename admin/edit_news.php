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

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: news.php');
    exit;
}

$error = '';

/*
|--------------------------------------------------------------------------
| LOAD POST
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            title,
            content,
            image,
            status
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

} catch (PDOException $e) {

    die('Could not load the post.');

}


/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    if ($title === '') {

        $error = 'Please enter a post title.';

    } elseif ($content === '') {

        $error = 'Please enter the post content.';

    } elseif (!in_array($status, ['draft', 'published'], true)) {

        $error = 'Invalid post status.';

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

    $imagePath = $news['image'];

    if (
        $error === '' &&
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

            $error =
                'There was a problem uploading the image.';

        } else {

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif'
            ];

            $fileType = mime_content_type(
                $_FILES['image']['tmp_name']
            );

            if (!in_array($fileType, $allowedTypes, true)) {

                $error =
                    'Only JPG, PNG, WEBP and GIF images are allowed.';

            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {

                $error =
                    'Image size must be less than 5MB.';

            } else {

                $uploadDir =
                    __DIR__ . '/../uploads/news/';

                if (!is_dir($uploadDir)) {

                    mkdir(
                        $uploadDir,
                        0755,
                        true
                    );
                }

                $extension = strtolower(
                    pathinfo(
                        $_FILES['image']['name'],
                        PATHINFO_EXTENSION
                    )
                );

                $fileName =
                    'news_' .
                    time() .
                    '_' .
                    bin2hex(random_bytes(5)) .
                    '.' .
                    $extension;

                $destination =
                    $uploadDir . $fileName;


                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $destination
                    )
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE OLD IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty($news['image']) &&
                        strpos(
                            $news['image'],
                            'uploads/news/'
                        ) === 0
                    ) {

                        $oldImage =
                            __DIR__ .
                            '/../' .
                            $news['image'];

                        if (
                            is_file($oldImage)
                        ) {

                            unlink($oldImage);

                        }

                    }


                    $imagePath =
                        'uploads/news/' . $fileName;

                } else {

                    $error =
                        'Could not save the uploaded image.';

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        try {

            $stmt = $pdo->prepare("
                UPDATE news
                SET
                    title = :title,
                    content = :content,
                    image = :image,
                    status = :status
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':image' => $imagePath,
                ':status' => $status,
                ':id' => $id
            ]);


            header(
                'Location: news.php?success=updated'
            );

            exit;

        } catch (PDOException $e) {

            $error =
                'Could not update the news post.';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | KEEP FORM DATA
    |--------------------------------------------------------------------------
    */

    $news['title'] = $title;
    $news['content'] = $content;
    $news['status'] = $status;
    $news['image'] = $imagePath;

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit News - Admin</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;

            margin: 0;
            padding: 0;

            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f3f7f5;

            color: #243746;
        }

        .admin-nav {
            min-height: 70px;

            background: #6fcf97;

            color: white;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;
        }

        .admin-logo {
            font-size: 20px;

            font-weight: 700;
        }

        .nav-links {
            display: flex;

            gap: 10px;
        }

        .nav-links a {
            color: white;

            text-decoration: none;

            padding: 8px 14px;

            border-radius: 8px;

            font-size: 13px;
        }

        .nav-links a:hover {
            background: #57b87e;
        }

        .container {
            max-width: 900px;

            margin: 0 auto;

            padding: 45px 25px 70px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 34px;

            margin-bottom: 6px;
        }

        .header p {
            color: #6b777f;
        }

        .form-card {
            background: white;

            border-radius: 18px;

            padding: 30px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 8px;
        }

        input,
        textarea,
        select {
            width: 100%;

            border: 1px solid #dfe7e3;

            border-radius: 10px;

            padding: 12px 14px;

            font-size: 14px;

            outline: none;

            color: #243746;

            background: white;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #6fcf97;
        }

        textarea {
            min-height: 220px;

            resize: vertical;

            line-height: 1.7;
        }

        input[type="file"] {
            padding: 10px;
        }

        .current-image {
            margin-bottom: 15px;
        }

        .current-image img {
            width: 180px;

            height: 120px;

            object-fit: cover;

            border-radius: 10px;

            display: block;

            margin-top: 8px;
        }

        .error {
            background: #f8d7da;

            color: #721c24;

            padding: 14px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }

        .actions {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }

        .btn {
            border: none;

            text-decoration: none;

            padding: 12px 20px;

            border-radius: 10px;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;
        }

        .save-btn {
            background: #6fcf97;

            color: white;
        }

        .save-btn:hover {
            background: #57b87e;
        }

        .cancel-btn {
            background: #eef2f0;

            color: #52616b;
        }

        .cancel-btn:hover {
            background: #e1e8e5;
        }

        @media (max-width: 600px) {

            .admin-nav {
                padding: 15px 18px;

                flex-direction: column;

                align-items: flex-start;

                gap: 12px;
            }

            .nav-links {
                width: 100%;

                overflow-x: auto;
            }

            .form-card {
                padding: 22px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

        }

    </style>

</head>

<body>

<nav class="admin-nav">

    <div class="admin-logo">
        Psychology Clinic — Admin
    </div>

    <div class="nav-links">

        <a href="index.php">
            Bookings
        </a>

        <a href="messages.php">
            Messages
        </a>

        <a href="news.php">
            News
        </a>

        <a href="login.php?logout=1">
            Logout
        </a>

    </div>

</nav>


<main class="container">

    <div class="header">

        <h1>
            Edit Post
        </h1>

        <p>
            Update your news post.
        </p>

    </div>


    <?php if ($error !== ''): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label for="title">
                    Post Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars(
                        $news['title']
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="content">
                    Content
                </label>

                <textarea
                    id="content"
                    name="content"
                    required
                ><?= htmlspecialchars(
                    $news['content']
                ) ?></textarea>

            </div>


            <?php if (!empty($news['image'])): ?>

                <div class="form-group current-image">

                    <label>
                        Current Image
                    </label>

                    <img
                        src="../<?= htmlspecialchars(
                            $news['image']
                        ) ?>"
                        alt="Current news image"
                    >

                </div>

            <?php endif; ?>


            <div class="form-group">

                <label for="image">
                    Replace Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                >

            </div>


            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option
                        value="draft"
                        <?= $news['status'] === 'draft'
                            ? 'selected'
                            : '' ?>
                    >
                        Draft
                    </option>

                    <option
                        value="published"
                        <?= $news['status'] === 'published'
                            ? 'selected'
                            : '' ?>
                    >
                        Published
                    </option>

                </select>

            </div>


            <div class="actions">

                <button
                    type="submit"
                    class="btn save-btn"
                >
                    Save Changes
                </button>

                <a
                    href="news.php"
                    class="btn cancel-btn"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</main>

</body>

</html>
