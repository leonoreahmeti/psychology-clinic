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
| ACTION
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    $action = $_POST['action'] ?? '';

    if ($id && in_array($action, ['approve', 'reject', 'delete'], true)) {

        try {

            if ($action === 'approve') {

                $stmt = $pdo->prepare("
                    UPDATE news_comments
                    SET status = 'approved'
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':id' => $id
                ]);

            } elseif ($action === 'reject') {

                $stmt = $pdo->prepare("
                    UPDATE news_comments
                    SET status = 'rejected'
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':id' => $id
                ]);

            } elseif ($action === 'delete') {

                $stmt = $pdo->prepare("
                    DELETE FROM news_comments
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':id' => $id
                ]);
            }

            header('Location: news_comments.php?success=' . $action);
            exit;

        } catch (PDOException $e) {

            header('Location: news_comments.php?error=action');
            exit;
        }
    }

    header('Location: news_comments.php?error=invalid');
    exit;
}


/*
|--------------------------------------------------------------------------
| LOAD COMMENTS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            nc.id,
            nc.news_id,
            nc.name,
            nc.email,
            nc.comment,
            nc.status,
            nc.created_at,
            n.title AS news_title
        FROM news_comments nc
        LEFT JOIN news n
            ON n.id = nc.news_id
        ORDER BY nc.created_at DESC
    ");

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $comments = [];

    $error = 'Could not load comments.';
}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalComments = count($comments);

$pendingComments = count(
    array_filter($comments, function ($item) {
        return $item['status'] === 'pending';
    })
);

$approvedComments = count(
    array_filter($comments, function ($item) {
        return $item['status'] === 'approved';
    })
);

$rejectedComments = count(
    array_filter($comments, function ($item) {
        return $item['status'] === 'rejected';
    })
);


/*
|--------------------------------------------------------------------------
| MESSAGES
|--------------------------------------------------------------------------
*/

$successType = $_GET['success'] ?? '';
$errorType = $_GET['error'] ?? '';

$successTitle = '';
$successMessage = '';

switch ($successType) {

    case 'approve':
        $successTitle = 'Comment approved';
        $successMessage = 'The comment is now visible on the website.';
        break;

    case 'reject':
        $successTitle = 'Comment rejected';
        $successMessage = 'The comment has been rejected.';
        break;

    case 'delete':
        $successTitle = 'Comment deleted';
        $successMessage = 'The comment has been permanently removed.';
        break;
}

if ($errorType === 'action') {
    $error = 'Could not complete the comment action.';
}

if ($errorType === 'invalid') {
    $error = 'Invalid comment request.';
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

    <title>Comments - Admin</title>

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

        .container {
            max-width: 1400px;

            margin: 0 auto;

            padding: 45px 25px 70px;
        }

        .header {
            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 36px;

            margin-bottom: 6px;
        }

        .header p {
            color: #6b777f;
        }


        /* =====================================================
           STATS
        ===================================================== */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }

        .stat {
            background: white;

            padding: 25px;

            border-radius: 16px;

            box-shadow:
                0 6px 20px rgba(0,0,0,0.06);
        }

        .stat span {
            display: block;

            color: #6b777f;

            font-size: 13px;

            margin-bottom: 8px;
        }

        .stat strong {
            font-size: 28px;
        }


        /* =====================================================
           COMMENTS CARD
        ===================================================== */

        .comments-card {
            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);
        }

        .comments-card h2 {
            margin-bottom: 20px;
        }


        /* =====================================================
           COMMENT ITEM
        ===================================================== */

        .comment-item {
            border: 1px solid #edf1ef;

            border-radius: 14px;

            padding: 20px;

            margin-bottom: 15px;

            transition: 0.2s ease;
        }

        .comment-item:hover {
            background: #fafdfb;
        }

        .comment-top {
            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 20px;

            margin-bottom: 12px;
        }

        .comment-user {
            font-weight: 600;

            color: #243746;

            font-size: 15px;
        }

        .comment-email {
            color: #8a959b;

            font-size: 12px;

            margin-top: 3px;
        }

        .comment-date {
            color: #8a959b;

            font-size: 11px;

            white-space: nowrap;
        }


        /* =====================================================
           NEWS TITLE
        ===================================================== */

        .news-title {
            background: #f3f7f5;

            padding: 9px 12px;

            border-radius: 8px;

            color: #52616b;

            font-size: 12px;

            margin-bottom: 12px;
        }

        .news-title strong {
            color: #243746;
        }


        /* =====================================================
           COMMENT TEXT
        ===================================================== */

        .comment-text {
            color: #52616b;

            font-size: 13px;

            line-height: 1.7;

            margin-bottom: 15px;

            white-space: pre-wrap;

            word-break: break-word;
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

        .status.pending {
            background: #fff4df;

            color: #a66a00;
        }

        .status.approved {
            background: #e9f7ef;

            color: #2e8b57;
        }

        .status.rejected {
            background: #fdeceb;

            color: #c0392b;
        }


        /* =====================================================
           BOTTOM
        ===================================================== */

        .comment-bottom {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }

        .actions {
            display: flex;

            gap: 7px;

            flex-wrap: wrap;
        }

        .action-btn {
            border: none;

            padding: 7px 12px;

            border-radius: 7px;

            color: white;

            font-size: 11px;

            font-weight: 600;

            cursor: pointer;

            font-family: 'Poppins', sans-serif;
        }

        .approve-btn {
            background: #6fcf97;
        }

        .approve-btn:hover {
            background: #57b87e;
        }

        .reject-btn {
            background: #e0a43c;
        }

        .reject-btn:hover {
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

        .empty {
            text-align: center;

            padding: 60px 20px;

            color: #777;
        }


        /* =====================================================
           ERROR
        ===================================================== */

        .error {
            background: #f8d7da;

            color: #721c24;

            padding: 15px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }


        /* =====================================================
           SUCCESS
        ===================================================== */

        .success {
            position: fixed;

            top: 25px;
            right: 25px;

            background: white;

            padding: 18px 22px;

            border-radius: 14px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.15);

            min-width: 320px;

            border-left: 5px solid #6fcf97;

            z-index: 9999;

            animation:
                slideIn 0.35s ease;
        }

        .success strong {
            display: block;

            font-size: 14px;

            margin-bottom: 3px;
        }

        .success span {
            color: #6b777f;

            font-size: 12px;
        }

        @keyframes slideIn {

            from {
                transform: translateX(120%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 900px) {

            .admin-nav {
                padding: 15px 18px;

                flex-wrap: wrap;

                gap: 10px;
            }

            .nav-links {
                width: 100%;

                overflow-x: auto;
            }

            .header {
                flex-direction: column;

                gap: 10px;
            }

            .stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .comment-top {
                flex-direction: column;

                gap: 8px;
            }

            .comment-date {
                white-space: normal;
            }

        }


        @media (max-width: 600px) {

            .container {
                padding: 30px 18px 50px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .comments-card {
                padding: 20px;
            }

            .comment-item {
                padding: 16px;
            }

            .comment-bottom {
                flex-direction: column;

                align-items: flex-start;
            }

            .actions {
                width: 100%;
            }

            .action-btn {
                flex: 1;
            }

            .success {
                left: 15px;
                right: 15px;

                min-width: 0;
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

        <a href="index.php">
            Bookings
        </a>

        <a href="messages.php">
            Messages
        </a>

        <a href="news.php">
            News
        </a>

        <a href="news_comments.php" class="active">
            Comments
        </a>

        <a href="login.php?logout=1">
            Logout
        </a>

    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="container">


    <div class="header">

        <div>

            <h1>
                News Comments
            </h1>

            <p>
                Review and manage comments submitted by visitors.
            </p>

        </div>

    </div>


    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATS
    ===================================================== -->

    <div class="stats">

        <div class="stat">

            <span>
                Total Comments
            </span>

            <strong>
                <?= $totalComments ?>
            </strong>

        </div>


        <div class="stat">

            <span>
                Pending
            </span>

            <strong>
                <?= $pendingComments ?>
            </strong>

        </div>


        <div class="stat">

            <span>
                Approved
            </span>

            <strong>
                <?= $approvedComments ?>
            </strong>

        </div>


        <div class="stat">

            <span>
                Rejected
            </span>

            <strong>
                <?= $rejectedComments ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         COMMENTS
    ===================================================== -->

    <div class="comments-card">

        <h2>
            All Comments
        </h2>


        <?php if (empty($comments)): ?>

            <div class="empty">

                <p>
                    There are currently no comments.
                </p>

            </div>

        <?php else: ?>


            <?php foreach ($comments as $item): ?>

                <div class="comment-item">


                    <div class="comment-top">

                        <div>

                            <div class="comment-user">

                                <?= htmlspecialchars(
                                    $item['name']
                                ) ?>

                            </div>

                            <div class="comment-email">

                                <?= htmlspecialchars(
                                    $item['email']
                                ) ?>

                            </div>

                        </div>


                        <div class="comment-date">

                            <?= date(
                                'F j, Y H:i',
                                strtotime(
                                    $item['created_at']
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="news-title">

                        <strong>
                            News:
                        </strong>

                        <?= htmlspecialchars(
                            $item['news_title'] ?? 'Deleted news'
                        ) ?>

                    </div>


                    <div class="comment-text">

                        <?= htmlspecialchars(
                            $item['comment']
                        ) ?>

                    </div>


                    <div class="comment-bottom">


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


                        <div class="actions">


                            <?php if (
                                $item['status'] !== 'approved'
                            ): ?>

                                <form
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="approve"
                                    >

                                    <button
                                        type="submit"
                                        class="action-btn approve-btn"
                                    >
                                        Approve
                                    </button>

                                </form>

                            <?php endif; ?>


                            <?php if (
                                $item['status'] !== 'rejected'
                            ): ?>

                                <form
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="reject"
                                    >

                                    <button
                                        type="submit"
                                        class="action-btn reject-btn"
                                    >
                                        Reject
                                    </button>

                                </form>

                            <?php endif; ?>


                            <form
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Are you sure you want to delete this comment?'
                                    );
                                "
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $item['id'] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
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

            <?php endforeach; ?>


        <?php endif; ?>


    </div>


</main>


<?php if ($successTitle !== ''): ?>

    <div class="success">

        <strong>
            <?= htmlspecialchars($successTitle) ?>
        </strong>

        <span>
            <?= htmlspecialchars($successMessage) ?>
        </span>

    </div>

    <script>

        setTimeout(function () {

            const popup =
                document.querySelector('.success');

            if (popup) {
                popup.remove();
            }

        }, 4000);

    </script>

<?php endif; ?>


</body>

</html>
