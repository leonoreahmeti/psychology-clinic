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
| LOAD MESSAGES
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            email,
            message,
            status,
            created_at
        FROM contact_messages
        ORDER BY created_at DESC
    ");

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $messages = [];

    $error = 'Could not load messages.';

}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalMessages = count($messages);

$unreadMessages = count(
    array_filter($messages, function ($message) {
        return $message['status'] === 'unread';
    })
);

$readMessages = count(
    array_filter($messages, function ($message) {
        return $message['status'] === 'read';
    })
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages - Admin</title>

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

        .logout-btn {
            text-decoration: none;

            color: white;

            background: #489f6c;

            padding: 9px 16px;

            border-radius: 8px;

            font-size: 13px;
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
           MESSAGES
        ===================================================== */

        .messages-card {
            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);
        }

        .messages-card h2 {
            margin-bottom: 20px;
        }

        .message-list {
            display: flex;

            flex-direction: column;

            gap: 15px;
        }


        /* =====================================================
           MESSAGE
        ===================================================== */

        .message-item {
            border: 1px solid #edf1ef;

            border-radius: 14px;

            padding: 20px;

            transition: 0.2s ease;
        }

        .message-item:hover {
            background: #fafdfb;
        }

        .message-item.unread {
            border-left:
                5px solid #6fcf97;

            background: #f9fffb;
        }


        .message-top {
            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 20px;

            margin-bottom: 12px;
        }


        .sender-info strong {
            display: block;

            font-size: 16px;

            margin-bottom: 3px;
        }

        .sender-info a {
            color: #57a978;

            text-decoration: none;

            font-size: 13px;
        }


        .message-date {
            color: #8a959b;

            font-size: 12px;

            white-space: nowrap;
        }


        .message-content {
            color: #4d5a61;

            font-size: 14px;

            line-height: 1.7;

            white-space: pre-wrap;

            margin-bottom: 15px;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .message-bottom {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;
        }

        .status {
            display: inline-block;

            padding: 5px 11px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 600;
        }

        .status.unread {
            background: #e9f7ef;

            color: #2e8b57;
        }

        .status.read {
            background: #eef1f2;

            color: #68757c;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .actions {
            display: flex;

            gap: 8px;
        }

        .actions form {
            margin: 0;
        }

        .action-btn {
            border: none;

            padding: 7px 12px;

            border-radius: 7px;

            font-size: 12px;

            cursor: pointer;

            color: white;

            font-family: 'Poppins', sans-serif;
        }

        .read-btn {
            background: #6fcf97;
        }

        .read-btn:hover {
            background: #57b87e;
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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .message-top {
                flex-direction: column;
            }

            .message-date {
                white-space: normal;
            }

            .message-bottom {
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

        <a
            href="messages.php"
            class="active"
        >
            Messages
        </a>

        <a href="login.php?logout=1">
            Logout
        </a>

    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="admin-container">


    <div class="admin-header">

        <h1>
            Messages
        </h1>

        <p>
            Manage messages received from your website.
        </p>

    </div>


    <?php if (isset($error)): ?>

        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- =================================================
         STATS
    ================================================== -->

    <div class="stats-grid">

        <div class="stat-card">

            <span>
                Total Messages
            </span>

            <strong>
                <?= $totalMessages ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Unread
            </span>

            <strong>
                <?= $unreadMessages ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Read
            </span>

            <strong>
                <?= $readMessages ?>
            </strong>

        </div>

    </div>


    <!-- =================================================
         MESSAGE LIST
    ================================================== -->

    <div class="messages-card">

        <h2>
            All Messages
        </h2>


        <?php if (empty($messages)): ?>

            <div class="empty-message">

                <p>
                    There are currently no messages.
                </p>

            </div>

        <?php else: ?>


            <div class="message-list">


                <?php foreach ($messages as $message): ?>


                    <div
                        class="message-item <?= $message['status'] === 'unread' ? 'unread' : '' ?>"
                    >


                        <div class="message-top">


                            <div class="sender-info">

                                <strong>
                                    <?= htmlspecialchars(
                                        $message['name']
                                    ) ?>
                                </strong>

                                <a
                                    href="mailto:<?= htmlspecialchars(
                                        $message['email']
                                    ) ?>"
                                >
                                    <?= htmlspecialchars(
                                        $message['email']
                                    ) ?>
                                </a>

                            </div>


                            <div class="message-date">

                                <?= htmlspecialchars(
                                    $message['created_at']
                                ) ?>

                            </div>


                        </div>


                        <div class="message-content">

                            <?= htmlspecialchars(
                                $message['message']
                            ) ?>

                        </div>


                        <div class="message-bottom">


                            <span
                                class="status <?= htmlspecialchars(
                                    $message['status']
                                ) ?>"
                            >

                                <?= ucfirst(
                                    htmlspecialchars(
                                        $message['status']
                                    )
                                ) ?>

                            </span>


                            <div class="actions">


                                <?php if (
                                    $message['status'] === 'unread'
                                ): ?>

                                    <form
                                        action="mark_message.php"
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= htmlspecialchars(
                                                $message['id']
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="action-btn read-btn"
                                        >
                                            Mark as Read
                                        </button>

                                    </form>

                                <?php endif; ?>


                                <form
                                    action="delete_message.php"
                                    method="POST"
                                    onsubmit="return confirm('Delete this message?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= htmlspecialchars(
                                            $message['id']
                                        ) ?>"
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


            </div>


        <?php endif; ?>


    </div>


</main>


</body>

</html>
