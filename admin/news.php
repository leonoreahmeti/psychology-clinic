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
| LOAD NEWS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            n.id,
            n.title,
            n.content,
            n.image,
            n.status,
            n.created_at,
            n.updated_at,
            COUNT(nc.id) AS comment_count
        FROM news n
        LEFT JOIN news_comments nc
            ON nc.news_id = n.id
        GROUP BY
            n.id,
            n.title,
            n.content,
            n.image,
            n.status,
            n.created_at,
            n.updated_at
        ORDER BY n.created_at DESC
    ");

    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $news = [];

    $error = 'Could not load news posts.';

}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalNews = count($news);

$publishedNews = count(
    array_filter($news, function ($item) {
        return $item['status'] === 'published';
    })
);

$draftNews = count(
    array_filter($news, function ($item) {
        return $item['status'] === 'draft';
    })
);


?>
<?php

$successType = $_GET['success'] ?? '';
$errorType = $_GET['error'] ?? '';

$successTitle = '';
$successMessage = '';

switch ($successType) {

    case 'created':
        $successTitle = 'Post created';
        $successMessage = 'The news post has been created successfully.';
        break;

    case 'updated':
        $successTitle = 'Post updated';
        $successMessage = 'The news post has been updated successfully.';
        break;

    case 'deleted':
        $successTitle = 'Post deleted';
        $successMessage = 'The news post has been permanently removed.';
        break;

    case 'published':
        $successTitle = 'Post published';
        $successMessage = 'The news post is now visible on the website.';
        break;

    case 'draft':
        $successTitle = 'Post moved to draft';
        $successMessage = 'The news post is now saved as a draft.';
        break;
}

if ($errorType === 'delete') {
    $error = 'Could not delete the news post.';
}

if ($errorType === 'status') {
    $error = 'Could not change the post status.';
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

    <title>News - Admin</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f3f7f5;
            color: #243746;
        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .admin-nav {
            min-height: 70px;

            background: #6fcf97;

            color: white;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;

            box-shadow:
                0 2px 10px rgba(0,0,0,0.08);
        }

        .admin-logo {
            font-size: 20px;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;

            padding: 8px 14px;

            border-radius: 8px;

            font-size: 13px;

            transition: 0.2s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: #57b87e;
        }


        /* =====================================================
           CONTAINER
        ===================================================== */

        .admin-container {
            max-width: 1400px;

            margin: 0 auto;

            padding: 45px 25px 70px;
        }

        .admin-header {
            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 30px;
        }

        .admin-header h1 {
            font-size: 36px;

            margin-bottom: 6px;
        }

        .admin-header p {
            color: #6b777f;
        }


        /* =====================================================
           CREATE BUTTON
        ===================================================== */

        .create-btn {
            display: inline-flex;

            align-items: center;

            gap: 7px;

            background: #6fcf97;

            color: white;

            text-decoration: none;

            padding: 12px 18px;

            border-radius: 10px;

            font-size: 13px;

            font-weight: 600;

            transition: 0.2s ease;
        }

        .create-btn:hover {
            background: #57b87e;
            transform: translateY(-1px);
        }


        /* =====================================================
           STATS
        ===================================================== */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }

        .stat-card {
            background: white;

            padding: 25px;

            border-radius: 16px;

            box-shadow:
                0 6px 20px rgba(0,0,0,0.06);
        }

        .stat-card span {
            display: block;

            color: #6b777f;

            font-size: 14px;

            margin-bottom: 8px;
        }

        .stat-card strong {
            font-size: 30px;
        }


        /* =====================================================
           NEWS CARD
        ===================================================== */

        .news-card {
            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);
        }

        .news-card h2 {
            margin-bottom: 20px;
        }

        .news-list {
            display: flex;

            flex-direction: column;

            gap: 15px;
        }


        /* =====================================================
           NEWS ITEM
        ===================================================== */

        .news-item {
            border: 1px solid #edf1ef;

            border-radius: 14px;

            padding: 20px;

            display: flex;

            gap: 20px;

            transition: 0.2s ease;
        }

        .news-item:hover {
            background: #fafdfb;
        }


        /* =====================================================
           IMAGE
        ===================================================== */

        .news-image {
            width: 180px;

            height: 130px;

            flex-shrink: 0;

            border-radius: 12px;

            overflow: hidden;

            background: #edf4f0;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #8a959b;

            font-size: 12px;
        }

        .news-image img {
            width: 100%;

            height: 100%;

            object-fit: cover;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .news-content {
            flex: 1;

            min-width: 0;
        }

        .news-top {
            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;

            margin-bottom: 8px;
        }

        .news-title {
            font-size: 18px;

            font-weight: 600;

            color: #243746;
        }

        .news-date {
            color: #8a959b;

            font-size: 11px;

            white-space: nowrap;
        }

        .news-text {
            color: #5e6b72;

            font-size: 13px;

            line-height: 1.7;

            display: -webkit-box;

            -webkit-line-clamp: 3;

            -webkit-box-orient: vertical;

            overflow: hidden;

            margin-bottom: 15px;
        }


        /* =====================================================
           META
        ===================================================== */

        .news-meta {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }

        .meta-left {
            display: flex;

            align-items: center;

            gap: 10px;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status {
            display: inline-block;

            padding: 5px 11px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 600;
        }

        .status.published {
            background: #e9f7ef;

            color: #2e8b57;
        }

        .status.draft {
            background: #fff4df;

            color: #a66a00;
        }


        /* =====================================================
           COMMENTS
        ===================================================== */

        .comments-count {
            color: #68757c;

            font-size: 11px;
        }


        /* =====================================================
           ACTIONS
        ===================================================== */

        .actions {
            display: flex;

            gap: 7px;

            flex-wrap: wrap;
        }

        .action-btn {
            border: none;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 7px 11px;

            border-radius: 7px;

            font-size: 11px;

            cursor: pointer;

            color: white;

            font-family: 'Poppins', sans-serif;
        }

        .edit-btn {
            background: #5b9bd5;
        }

        .edit-btn:hover {
            background: #4387c3;
        }

        .publish-btn {
            background: #6fcf97;
        }

        .publish-btn:hover {
            background: #57b87e;
        }

        .draft-btn {
            background: #e0a43c;
        }

        .draft-btn:hover {
            background: #c78d29;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .delete-btn:hover {
            background: #c0392b;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-message {
            text-align: center;

            padding: 60px 20px;

            color: #777;
        }


        /* =====================================================
           ERROR
        ===================================================== */

        .error-message {
            background: #f8d7da;

            color: #721c24;

            padding: 15px;

            border-radius: 10px;

            margin-bottom: 20px;
        }


        /* =====================================================
           SUCCESS POPUP
        ===================================================== */

        .success-popup {
            position: fixed;

            top: 25px;
            right: 25px;

            background: white;

            padding: 18px 22px;

            border-radius: 14px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.15);

            display: flex;

            align-items: center;

            gap: 12px;

            min-width: 320px;

            border-left:
                5px solid #6fcf97;

            z-index: 9999;

            transform: translateX(120%);

            opacity: 0;

            transition: all 0.35s ease;
        }

        .success-popup.show {
            transform: translateX(0);
            opacity: 1;
        }

        .success-icon {
            width: 34px;
            height: 34px;

            flex-shrink: 0;

            border-radius: 50%;

            background: #e9f7ef;

            color: #2e8b57;

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 700;

            font-size: 18px;
        }

        .success-popup strong {
            display: block;

            color: #243746;

            font-size: 14px;

            margin-bottom: 3px;
        }

        .success-popup span {
            color: #6b777f;

            font-size: 12px;
        }


        /* =====================================================
           CONFIRM MODAL
        ===================================================== */

        .confirm-overlay {
            position: fixed;

            inset: 0;

            background: rgba(25, 40, 35, 0.45);

            backdrop-filter: blur(5px);

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 20px;

            z-index: 10000;

            opacity: 0;

            visibility: hidden;

            transition:
                opacity 0.25s ease,
                visibility 0.25s ease;
        }

        .confirm-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .confirm-modal {
            width: 100%;

            max-width: 430px;

            background: white;

            border-radius: 22px;

            padding: 32px;

            text-align: center;

            box-shadow:
                0 25px 70px rgba(0, 0, 0, 0.20);

            transform:
                translateY(20px)
                scale(0.96);

            transition:
                transform 0.25s ease;
        }

        .confirm-overlay.show .confirm-modal {
            transform:
                translateY(0)
                scale(1);
        }

        .confirm-icon {
            width: 68px;
            height: 68px;

            margin: 0 auto 18px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 30px;

            font-weight: 600;

            background: #fdeceb;

            color: #e74c3c;
        }

        .confirm-modal h3 {
            font-size: 21px;

            color: #243746;

            margin-bottom: 10px;
        }

        .confirm-modal p {
            color: #6b777f;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 25px;
        }

        .confirm-actions {
            display: flex;

            gap: 10px;
        }

        .confirm-actions button {
            flex: 1;

            border: none;

            padding: 12px 18px;

            border-radius: 10px;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .confirm-no {
            background: #f1f4f3;

            color: #52616b;
        }

        .confirm-no:hover {
            background: #e5eae8;
        }

        .confirm-yes {
            background: #e74c3c;

            color: white;
        }

        .confirm-yes:hover {
            background: #c0392b;
        }

        .confirm-actions button:disabled {
            opacity: 0.6;

            cursor: not-allowed;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 800px) {

            .admin-nav {
                padding: 15px 18px;

                flex-wrap: wrap;

                gap: 10px;
            }

            .nav-links {
                width: 100%;

                overflow-x: auto;
            }

            .admin-header {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .news-item {
                flex-direction: column;
            }

            .news-image {
                width: 100%;

                height: 200px;
            }

            .news-top {
                flex-direction: column;
            }

            .news-date {
                white-space: normal;
            }

            .news-meta {
                flex-direction: column;

                align-items: flex-start;
            }

            .actions {
                width: 100%;
            }

            .action-btn {
                flex: 1;
            }

        }


        @media (max-width: 500px) {

            .confirm-modal {
                padding: 25px 20px;

                border-radius: 18px;
            }

            .confirm-actions {
                flex-direction: column;
            }

            .success-popup {
                left: 15px;
                right: 15px;

                top: 15px;

                min-width: 0;

                width: auto;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="admin-nav">

    <div class="admin-logo">
        Psychology Clinic — Admin
    </div>

    <div class="nav-links">

     <a href="index.php">Bookings</a>
<a href="messages.php">Messages</a>
<a href="news.php" class="active">News</a>
<a href="news_comments.php">Comments</a>
<a href="login.php?logout=1">Logout</a>

    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="admin-container">


    <div class="admin-header">

        <div>

            <h1>
                News & Updates
            </h1>

            <p>
                Create and manage news posts for your website.
            </p>

        </div>


        <a
            href="create_news.php"
            class="create-btn"
        >
            + Create New Post
        </a>

    </div>


    <?php if (isset($error)): ?>

        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATS
    ===================================================== -->

    <div class="stats-grid">

        <div class="stat-card">

            <span>
                Total Posts
            </span>

            <strong>
                <?= $totalNews ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Published
            </span>

            <strong>
                <?= $publishedNews ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Drafts
            </span>

            <strong>
                <?= $draftNews ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         NEWS LIST
    ===================================================== -->

    <div class="news-card">

        <h2>
            All Posts
        </h2>


        <?php if (empty($news)): ?>

            <div class="empty-message">

                <p>
                    There are currently no news posts.
                </p>

            </div>

        <?php else: ?>


            <div class="news-list">


                <?php foreach ($news as $item): ?>


                    <div class="news-item">


                        <!-- IMAGE -->

                        <div class="news-image">

                            <?php if (
                                !empty($item['image'])
                            ): ?>

                                <img
                                    src="<?= htmlspecialchars($item['image']) ?>"
                                    alt="<?= htmlspecialchars($item['title']) ?>"
                                >

                            <?php else: ?>

                                No image

                            <?php endif; ?>

                        </div>


                        <!-- CONTENT -->

                        <div class="news-content">


                            <div class="news-top">

                                <div class="news-title">

                                    <?= htmlspecialchars(
                                        $item['title']
                                    ) ?>

                                </div>


                                <div class="news-date">

                                    <?= htmlspecialchars(
                                        $item['created_at']
                                    ) ?>

                                </div>

                            </div>


                            <div class="news-text">

                                <?= htmlspecialchars(
                                    $item['content']
                                ) ?>

                            </div>


                            <div class="news-meta">


                                <div class="meta-left">


                                    <span
                                        class="status <?= htmlspecialchars(
                                            $item['status']
                                        ) ?>"
                                    >

                                        <?= ucfirst(
                                            htmlspecialchars(
                                                $item['status']
                                            )
                                        ) ?>

                                    </span>


                                    <span class="comments-count">

                                        💬
                                        <?= (int) $item['comment_count'] ?>
                                        comments

                                    </span>


                                </div>


                                <div class="actions">


                                    <!-- EDIT -->

                                    <a
                                        href="edit_news.php?id=<?= (int) $item['id'] ?>"
                                        class="action-btn edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <!-- STATUS -->

                                    <?php if (
                                        $item['status'] === 'published'
                                    ): ?>

                                        <form
                                            action="toggle_news_status.php"
                                            method="POST"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $item['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="draft"
                                            >

                                            <button
                                                type="submit"
                                                class="action-btn draft-btn"
                                            >
                                                Make Draft
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <form
                                            action="toggle_news_status.php"
                                            method="POST"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $item['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="published"
                                            >

                                            <button
                                                type="submit"
                                                class="action-btn publish-btn"
                                            >
                                                Publish
                                            </button>

                                        </form>

                                    <?php endif; ?>


                                    <!-- DELETE -->

                                    <form
                                        action="delete_news.php"
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $item['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="action-btn delete-btn"
                                        >
                                            Delete
                                        </button>

                                    </form>


                                </div>

                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>


</main>


<!-- =====================================================
     SUCCESS POPUP
===================================================== -->

<div
    id="successPopup"
    class="success-popup"
>

    <div class="success-icon">
        ✓
    </div>

    <div>

        <strong id="successTitle">
            Success
        </strong>

        <span id="successMessage">
            Action completed successfully.
        </span>

    </div>

</div>


<!-- =====================================================
     DELETE MODAL
===================================================== -->

<div
    id="confirmOverlay"
    class="confirm-overlay"
>

    <div
        class="confirm-modal"
        role="dialog"
        aria-modal="true"
    >

        <div class="confirm-icon">
            ×
        </div>

        <h3>
            Delete this post?
        </h3>

        <p>
            This action will permanently remove this news post
            and its associated data. This cannot be undone.
        </p>

        <div class="confirm-actions">

            <button
                type="button"
                id="confirmNo"
                class="confirm-no"
            >
                No, go back
            </button>

            <button
                type="button"
                id="confirmYes"
                class="confirm-yes"
            >
                Yes, delete post
            </button>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const confirmOverlay =
        document.getElementById('confirmOverlay');

    const confirmNo =
        document.getElementById('confirmNo');

    const confirmYes =
        document.getElementById('confirmYes');

    const successPopup =
        document.getElementById('successPopup');

    const successTitle =
        document.getElementById('successTitle');

    const successMessage =
        document.getElementById('successMessage');


    let selectedForm = null;

    let popupTimeout = null;


    /*
    |--------------------------------------------------------------------------
    | SUCCESS POPUP
    |--------------------------------------------------------------------------
    */

    function showSuccess(title, message) {

        successTitle.textContent = title;

        successMessage.textContent = message;

        successPopup.classList.add('show');

        clearTimeout(popupTimeout);

        popupTimeout = setTimeout(function () {

            successPopup.classList.remove('show');

        }, 4000);

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN MODAL
    |--------------------------------------------------------------------------
    */

    function openConfirmModal(form) {

        selectedForm = form;

        confirmOverlay.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeConfirmModal() {

        confirmOverlay.classList.remove('show');

        document.body.style.overflow = '';

        selectedForm = null;

        confirmYes.disabled = false;

        confirmNo.disabled = false;

        confirmYes.textContent =
            'Yes, delete post';

    }


    /*
    |--------------------------------------------------------------------------
    | DELETE FORMS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('form[action="delete_news.php"]')
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    event.preventDefault();

                    openConfirmModal(form);

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | NO
    |--------------------------------------------------------------------------
    */

    confirmNo.addEventListener(
        'click',
        function () {

            closeConfirmModal();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CLICK OUTSIDE
    |--------------------------------------------------------------------------
    */

    confirmOverlay.addEventListener(
        'click',
        function (event) {

            if (event.target === confirmOverlay) {

                closeConfirmModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                confirmOverlay.classList.contains('show')
            ) {

                closeConfirmModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CONFIRM DELETE
    |--------------------------------------------------------------------------
    */

    confirmYes.addEventListener(
        'click',
        async function () {

            if (!selectedForm) {
                return;
            }


            const form = selectedForm;


            confirmYes.disabled = true;

            confirmNo.disabled = true;

            confirmYes.textContent =
                'Deleting...';


            try {

                const formData =
                    new FormData(form);


                const response =
                    await fetch(
                        form.action,
                        {
                            method: 'POST',
                            body: formData
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Delete request failed.'
                    );

                }


                closeConfirmModal();


                showSuccess(
                    'Post deleted',
                    'The news post has been permanently removed.'
                );


                setTimeout(function () {

                    window.location.reload();

                }, 1000);


            } catch (error) {

                console.error(
                    'Delete news error:',
                    error
                );


                closeConfirmModal();


                alert(
                    'Something went wrong. Please try again.'
                );

            }

        }
    

    );

<?php if ($successTitle !== ''): ?>

showSuccess(
    <?= json_encode($successTitle) ?>,
    <?= json_encode($successMessage) ?>
);

<?php endif; ?>
});

</script>


</body>

</html>
