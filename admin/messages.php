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

        /* =========================================================
   SUCCESS POPUP
========================================================= */

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


/* =========================================================
   CONFIRMATION MODAL
========================================================= */

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
}

.confirm-icon.delete-icon {
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

.confirm-yes.delete-confirm {
    background: #e74c3c;

    color: white;
}

.confirm-yes.delete-confirm:hover {
    background: #c0392b;
}

.confirm-actions button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}


/* MOBILE */

@media (max-width: 500px) {

    .confirm-modal {
        padding: 25px 20px;

        border-radius: 18px;
    }

    .confirm-modal h3 {
        font-size: 19px;
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
                                        action="mark_message_read.php"
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
<!-- =========================================================
     SUCCESS POPUP
========================================================= -->

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


<!-- =========================================================
     DELETE CONFIRMATION MODAL
========================================================= -->

<div
    id="confirmOverlay"
    class="confirm-overlay"
>

    <div
        class="confirm-modal"
        role="dialog"
        aria-modal="true"
    >

        <div
            class="confirm-icon delete-icon"
        >
            ×
        </div>

        <h3>
            Delete this message?
        </h3>

        <p>
            This action will permanently remove this message.
            This cannot be undone.
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
                class="confirm-yes delete-confirm"
            >
                Yes, delete message
            </button>

        </div>

    </div>

</div>


</body>

</html>
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
            'Yes, delete message';

    }


    /*
    |--------------------------------------------------------------------------
    | DELETE FORMS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('form[action="delete_message.php"]')
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
                    'Message deleted',
                    'The message has been permanently removed.'
                );


                setTimeout(function () {

                    window.location.reload();

                }, 1000);


            } catch (error) {

                console.error(
                    'Delete message error:',
                    error
                );


                closeConfirmModal();


                alert(
                    'Something went wrong. Please try again.'
                );

            }

        }
    );


});

</script>

