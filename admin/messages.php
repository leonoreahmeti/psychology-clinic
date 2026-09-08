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

    $error = 'Could not load messages. Please try again.';

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

        /* =========================================================
           RESET
        ========================================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }


        /* =========================================================
           BODY
        ========================================================= */

        body {
            background: #f3f7f5;
            color: #243746;
        }


        /* =========================================================
           NAVBAR
        ========================================================= */

        .admin-nav {
            min-height: 70px;

            background: #6fcf97;

            color: white;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 40px;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.08);
        }


        .admin-logo {
            font-size: 20px;

            font-weight: 700;

            white-space: nowrap;
        }


        .nav-links {
            display: flex;

            align-items: center;

            gap: 8px;
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
            background: #489f6c !important;
        }


        .logout-btn:hover {
            background: #3d8d60 !important;
        }


        /* =========================================================
           MAIN CONTAINER
        ========================================================= */

        .admin-container {
            max-width: 1400px;

            margin: 0 auto;

            padding: 45px 25px 70px;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .admin-header {
            margin-bottom: 30px;
        }


        .admin-header h1 {
            font-size: 36px;

            margin-bottom: 6px;

            color: #243746;
        }


        .admin-header p {
            color: #6b777f;

            font-size: 14px;
        }


        /* =========================================================
           ERROR MESSAGE
        ========================================================= */

        .error-message {
            background: #f8d7da;

            color: #721c24;

            padding: 15px 18px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* =========================================================
           STATISTICS
        ========================================================= */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 20px;

            margin-bottom: 30px;
        }


        .stat-card {
            background: white;

            padding: 25px;

            border-radius: 16px;

            box-shadow:
                0 6px 20px rgba(0, 0, 0, 0.06);

            transition: 0.2s ease;
        }


        .stat-card:hover {
            transform: translateY(-2px);

            box-shadow:
                0 9px 25px rgba(0, 0, 0, 0.08);
        }


        .stat-card span {
            display: block;

            color: #6b777f;

            font-size: 14px;

            margin-bottom: 8px;
        }


        .stat-card strong {
            display: block;

            font-size: 30px;

            color: #243746;
        }


        /* =========================================================
           MESSAGES CARD
        ========================================================= */

        .messages-card {
            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.06);
        }


        .messages-card h2 {
            margin-bottom: 20px;

            color: #243746;

            font-size: 22px;
        }


        .message-list {
            display: flex;

            flex-direction: column;

            gap: 15px;
        }


        /* =========================================================
           MESSAGE ITEM
        ========================================================= */

        .message-item {
            border: 1px solid #edf1ef;

            border-radius: 14px;

            padding: 20px;

            background: white;

            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }


        .message-item:hover {
            background: #fafdfb;

            border-color: #dfe9e4;
        }


        .message-item.unread {
            border-left:
                5px solid #6fcf97;

            background: #f9fffb;
        }


        /* =========================================================
           MESSAGE TOP
        ========================================================= */

        .message-top {
            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 20px;

            margin-bottom: 12px;
        }


        .sender-info {
            min-width: 0;
        }


        .sender-info strong {
            display: block;

            font-size: 16px;

            color: #243746;

            margin-bottom: 3px;

            word-break: break-word;
        }


        .sender-info a {
            color: #57a978;

            text-decoration: none;

            font-size: 13px;

            word-break: break-word;
        }


        .sender-info a:hover {
            text-decoration: underline;
        }


        .message-date {
            color: #8a959b;

            font-size: 12px;

            white-space: nowrap;
        }


        /* =========================================================
           MESSAGE CONTENT
        ========================================================= */

        .message-content {
            color: #4d5a61;

            font-size: 14px;

            line-height: 1.7;

            white-space: pre-wrap;

            word-break: break-word;

            margin-bottom: 15px;
        }


        /* =========================================================
           MESSAGE BOTTOM
        ========================================================= */

        .message-bottom {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;
        }


        /* =========================================================
           STATUS
        ========================================================= */

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


        /* =========================================================
           ACTIONS
        ========================================================= */

        .actions {
            display: flex;

            align-items: center;

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

            transition: 0.2s ease;
        }


        .action-btn:hover {
            transform: translateY(-1px);
        }


        .action-btn:active {
            transform: translateY(0);
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


        /* =========================================================
           EMPTY MESSAGE
        ========================================================= */

        .empty-message {
            text-align: center;

            padding: 60px 20px;

            color: #777;
        }


        .empty-message p {
            font-size: 14px;
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

            transform:
                translateX(120%);

            opacity: 0;

            pointer-events: none;

            transition:
                all 0.35s ease;
        }


        .success-popup.show {
            transform:
                translateX(0);

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
           CONFIRMATION OVERLAY
        ========================================================= */

        .confirm-overlay {
            position: fixed;

            inset: 0;

            background:
                rgba(25, 40, 35, 0.45);

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


        /* =========================================================
           CONFIRMATION MODAL
        ========================================================= */

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


        /* =========================================================
           CONFIRMATION ICON
        ========================================================= */

        .confirm-icon {
            width: 68px;

            height: 68px;

            margin:
                0 auto 18px;

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


        /* =========================================================
           CONFIRMATION TEXT
        ========================================================= */

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


        /* =========================================================
           CONFIRMATION BUTTONS
        ========================================================= */

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


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 900px) {

            .admin-nav {
                padding:
                    15px 18px;

                flex-wrap: wrap;

                gap: 10px;
            }


            .admin-logo {
                font-size: 17px;
            }


            .nav-links {
                width: 100%;

                overflow-x: auto;

                padding-bottom: 2px;
            }


            .nav-links a {
                white-space: nowrap;
            }


            .stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 700px) {

            .admin-container {
                padding:
                    30px 15px 50px;
            }


            .admin-header h1 {
                font-size: 30px;
            }


            .stats-grid {
                grid-template-columns: 1fr;
            }


            .messages-card {
                padding: 18px;
            }


            .message-top {
                flex-direction: column;

                gap: 8px;
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


            .actions form {
                flex: 1;
            }


            .action-btn {
                width: 100%;
            }

        }


        @media (max-width: 500px) {

            .confirm-modal {
                padding:
                    25px 20px;

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


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="admin-nav">

    <div class="admin-logo">
        Psychology Clinic — Admin
    </div>


    <div class="nav-links">

        <a href="index.php">Bookings</a>
<a href="messages.php" class="active">Messages</a>
<a href="news.php">News</a>
<a href="comments.php">Comments</a>
<a href="login.php?logout=1">Logout</a>


    </div>

</nav>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="admin-container">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="admin-header">

        <h1>
            Messages
        </h1>

        <p>
            Manage messages received from your website.
        </p>

    </div>


    <!-- =====================================================
         ERROR
    ====================================================== -->

    <?php if (isset($error)): ?>

        <div class="error-message">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="stats-grid">


        <!-- TOTAL -->

        <div class="stat-card">

            <span>
                Total Messages
            </span>

            <strong>
                <?= $totalMessages ?>
            </strong>

        </div>


        <!-- UNREAD -->

        <div class="stat-card">

            <span>
                Unread
            </span>

            <strong>
                <?= $unreadMessages ?>
            </strong>

        </div>


        <!-- READ -->

        <div class="stat-card">

            <span>
                Read
            </span>

            <strong>
                <?= $readMessages ?>
            </strong>

        </div>


    </div>


    <!-- =====================================================
         ALL MESSAGES
    ====================================================== -->

    <div class="messages-card">

        <h2>
            All Messages
        </h2>


        <?php if (empty($messages)): ?>


            <!-- EMPTY -->

            <div class="empty-message">

                <p>
                    There are currently no messages.
                </p>

            </div>


        <?php else: ?>


            <div class="message-list">


                <?php foreach ($messages as $message): ?>


                    <!-- =================================================
                         MESSAGE ITEM
                    ================================================== -->

                    <div
                        class="message-item <?= $message['status'] === 'unread' ? 'unread' : '' ?>"
                    >


                        <!-- =================================================
                             TOP
                        ================================================== -->

                        <div class="message-top">


                            <div class="sender-info">

                                <strong>
                                    <?= htmlspecialchars(
                                        $message['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>


                                <a
                                    href="mailto:<?= htmlspecialchars(
                                        $message['email'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    <?= htmlspecialchars(
                                        $message['email'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </a>

                            </div>


                            <div class="message-date">

                                <?= htmlspecialchars(
                                    $message['created_at'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>


                        </div>


                        <!-- =================================================
                             MESSAGE
                        ================================================== -->

                        <div class="message-content">

                            <?= htmlspecialchars(
                                $message['message'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>


                        <!-- =================================================
                             BOTTOM
                        ================================================== -->

                        <div class="message-bottom">


                            <!-- STATUS -->

                            <span
                                class="status <?= htmlspecialchars(
                                    $message['status'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                                <?= ucfirst(
                                    htmlspecialchars(
                                        $message['status'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ) ?>

                            </span>


                            <!-- ACTIONS -->

                            <div class="actions">


                                <!-- =========================================
                                     MARK AS READ
                                ========================================== -->

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
                                                $message['id'],
                                                ENT_QUOTES,
                                                'UTF-8'
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


                                <!-- =========================================
                                     DELETE
                                ========================================== -->

                                <form
                                    action="delete_message.php"
                                    method="POST"
                                    class="delete-form"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= htmlspecialchars(
                                            $message['id'],
                                            ENT_QUOTES,
                                            'UTF-8'
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
    role="status"
    aria-live="polite"
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
    aria-hidden="true"
>


    <div
        class="confirm-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirmTitle"
    >


        <!-- ICON -->

        <div
            class="confirm-icon delete-icon"
        >
            ×
        </div>


        <!-- TITLE -->

        <h3 id="confirmTitle">
            Delete this message?
        </h3>


        <!-- TEXT -->

        <p id="confirmText">
            This action will permanently remove this message.
            This cannot be undone.
        </p>


        <!-- BUTTONS -->

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


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       ELEMENTS
    ========================================================= */

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


    /*
    |--------------------------------------------------------------------------
    | SELECTED FORM
    |--------------------------------------------------------------------------
    */

    let selectedForm = null;


    /*
    |--------------------------------------------------------------------------
    | POPUP TIMER
    |--------------------------------------------------------------------------
    */

    let popupTimeout = null;


    /* =========================================================
       SUCCESS POPUP
    ========================================================= */

    function showSuccess(title, message) {

        successTitle.textContent = title;

        successMessage.textContent = message;

        successPopup.classList.add('show');


        clearTimeout(popupTimeout);


        popupTimeout = setTimeout(function () {

            successPopup.classList.remove('show');

        }, 4000);

    }


    /* =========================================================
       OPEN CONFIRMATION MODAL
    ========================================================= */

    function openConfirmModal(form) {

        selectedForm = form;


        confirmOverlay.classList.add('show');

        confirmOverlay.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.style.overflow = 'hidden';


        /*
        |--------------------------------------------------------------------------
        | Focus confirm button
        |--------------------------------------------------------------------------
        */

        setTimeout(function () {

            confirmYes.focus();

        }, 100);

    }


    /* =========================================================
       CLOSE CONFIRMATION MODAL
    ========================================================= */

    function closeConfirmModal() {

        confirmOverlay.classList.remove('show');

        confirmOverlay.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.style.overflow = '';


        selectedForm = null;


        /*
        |--------------------------------------------------------------------------
        | Reset buttons
        |--------------------------------------------------------------------------
        */

        confirmYes.disabled = false;

        confirmNo.disabled = false;

        confirmYes.textContent =
            'Yes, delete message';

    }


    /* =========================================================
       DELETE FORMS
    ========================================================= */

    document
        .querySelectorAll('.delete-form')
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    /*
                    |--------------------------------------------------------------------------
                    | Stop normal form submit
                    |--------------------------------------------------------------------------
                    */

                    event.preventDefault();


                    /*
                    |--------------------------------------------------------------------------
                    | Open confirmation modal
                    |--------------------------------------------------------------------------
                    */

                    openConfirmModal(form);

                }
            );

        });


    /* =========================================================
       NO / GO BACK
    ========================================================= */

    confirmNo.addEventListener(
        'click',
        function () {

            closeConfirmModal();

        }
    );


    /* =========================================================
       CLICK OUTSIDE MODAL
    ========================================================= */

    confirmOverlay.addEventListener(
        'click',
        function (event) {

            if (
                event.target === confirmOverlay
            ) {

                closeConfirmModal();

            }

        }
    );


    /* =========================================================
       ESC KEY
    ========================================================= */

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


    /* =========================================================
       CONFIRM DELETE
    ========================================================= */

    confirmYes.addEventListener(
        'click',
        async function () {


            /*
            |--------------------------------------------------------------------------
            | Check selected form
            |--------------------------------------------------------------------------
            */

            if (!selectedForm) {

                return;

            }


            const form =
                selectedForm;


            /*
            |--------------------------------------------------------------------------
            | Disable buttons
            |--------------------------------------------------------------------------
            */

            confirmYes.disabled = true;

            confirmNo.disabled = true;


            confirmYes.textContent =
                'Deleting...';


            try {


                /*
                |--------------------------------------------------------------------------
                | Form data
                |--------------------------------------------------------------------------
                */

                const formData =
                    new FormData(form);


                /*
                |--------------------------------------------------------------------------
                | Send DELETE request
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        form.action,
                        {
                            method: 'POST',

                            body: formData,

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Check HTTP response
                |--------------------------------------------------------------------------
                */

                if (!response.ok) {

                    throw new Error(
                        'Delete request failed.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Try to read JSON
                |--------------------------------------------------------------------------
                */

                let result = null;


                try {

                    result =
                        await response.json();

                } catch (jsonError) {

                    /*
                    |--------------------------------------------------------------------------
                    | If delete_message.php does not return JSON,
                    | a successful HTTP response is still considered success.
                    |--------------------------------------------------------------------------
                    */

                    result = {
                        success: true
                    };

                }


                /*
                |--------------------------------------------------------------------------
                | Check backend result
                |--------------------------------------------------------------------------
                */

                if (
                    result &&
                    result.success === false
                ) {

                    throw new Error(
                        result.message ||
                        'The message could not be deleted.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Close modal
                |--------------------------------------------------------------------------
                */

                closeConfirmModal();


                /*
                |--------------------------------------------------------------------------
                | Show success popup
                |--------------------------------------------------------------------------
                */

                showSuccess(
                    'Message deleted',
                    'The message has been permanently removed.'
                );


                /*
                |--------------------------------------------------------------------------
                | Reload page
                |--------------------------------------------------------------------------
                */

                setTimeout(function () {

                    window.location.reload();

                }, 1000);


            } catch (error) {


                /*
                |--------------------------------------------------------------------------
                | Console error
                |--------------------------------------------------------------------------
                */

                console.error(
                    'Delete message error:',
                    error
                );


                /*
                |--------------------------------------------------------------------------
                | Close modal
                |--------------------------------------------------------------------------
                */

                closeConfirmModal();


                /*
                |--------------------------------------------------------------------------
                | Error message
                |--------------------------------------------------------------------------
                */

                alert(
                    error.message ||
                    'Something went wrong. Please try again.'
                );

            }

        }
    );


});

</script>


</body>

</html>
