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
| LOAD BOOKINGS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            client_name,
            client_email,
            booking_date,
            booking_time,
            service,
            therapist,
            duration,
            price,
            status,
            created_at
        FROM bookings
        ORDER BY booking_date ASC, booking_time ASC
    ");

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $bookings = [];

    $error = 'Could not load bookings. Please try again.';
}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalBookings = count($bookings);

$pendingBookings = count(
    array_filter($bookings, function ($booking) {
        return $booking['status'] === 'pending';
    })
);

$confirmedBookings = count(
    array_filter($bookings, function ($booking) {
        return $booking['status'] === 'confirmed';
    })
);

$cancelledBookings = count(
    array_filter($bookings, function ($booking) {
        return $booking['status'] === 'cancelled';
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

    <title>Admin Dashboard - Psychology Clinic</title>

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
        }

        .logout-btn {
            text-decoration: none;

            color: white;

            background: #57b87e;

            padding: 9px 18px;

            border-radius: 9px;

            font-size: 14px;
            font-weight: 500;

            transition: 0.2s ease;
        }

        .logout-btn:hover {
            background: #489f6c;
        }


        /* =========================================================
           MAIN
        ========================================================= */

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


        /* =========================================================
           STATISTICS
        ========================================================= */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 20px;

            margin-bottom: 30px;
        }

        .stat-card {
            background: white;

            padding: 25px;

            border-radius: 16px;

            box-shadow:
                0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .stat-card span {
            display: block;

            color: #6b777f;

            font-size: 14px;

            margin-bottom: 8px;
        }

        .stat-card strong {
            font-size: 30px;

            color: #243746;
        }


        /* =========================================================
           BOOKINGS CARD
        ========================================================= */

        .bookings-card {
            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .bookings-card h2 {
            margin-bottom: 20px;
        }

        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 1100px;
        }

        th {
            background: #f3f7f5;

            color: #243746;

            padding: 14px;

            text-align: left;

            font-size: 13px;
        }

        td {
            padding: 14px;

            border-bottom:
                1px solid #edf1ef;

            color: #444;

            font-size: 13px;

            vertical-align: middle;
        }

        tbody tr {
            transition: 0.2s ease;
        }

        tbody tr:hover {
            background: #fafdfb;
        }


        /* =========================================================
           STATUS
        ========================================================= */

        .status {
            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }


        /* =========================================================
           ACTION BUTTONS
        ========================================================= */

        .action-buttons {
            display: flex;

            align-items: center;

            gap: 7px;
        }

        .action-buttons form {
            margin: 0;
        }

        .cancel-btn,
        .delete-btn {
            border: none;

            padding: 7px 11px;

            border-radius: 7px;

            color: white;

            cursor: pointer;

            font-family: 'Poppins', sans-serif;

            font-size: 12px;

            transition: 0.2s ease;
        }

        .cancel-btn {
            background: #e67e22;
        }

        .cancel-btn:hover {
            background: #d35400;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .cancel-btn:disabled,
        .delete-btn:disabled {
            opacity: 0.6;

            cursor: not-allowed;
        }


        /* =========================================================
           EMPTY / ERROR
        ========================================================= */

        .empty-message {
            text-align: center;

            padding: 50px 20px;

            color: #777;
        }

        .error-message {
            background: #f8d7da;

            color: #721c24;

            padding: 15px;

            border-radius: 10px;

            margin-bottom: 20px;
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
           MOBILE
        ========================================================= */

        @media (max-width: 900px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 600px) {

            .admin-nav {
                padding: 0 18px;
            }

            .admin-logo {
                font-size: 16px;
            }

            .admin-container {
                padding: 30px 15px 50px;
            }

            .admin-header h1 {
                font-size: 28px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .bookings-card {
                padding: 18px;
            }

            .success-popup {
                left: 15px;
                right: 15px;

                top: 15px;

                min-width: 0;

                width: auto;
            }

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

    background: #ffffff;

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


/* ICON */

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

.confirm-icon.cancel-icon {
    background: #fff3e8;

    color: #e67e22;
}

.confirm-icon.delete-icon {
    background: #fdeceb;

    color: #e74c3c;
}


/* TEXT */

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


/* BUTTONS */

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

.confirm-yes.cancel-confirm {
    background: #e67e22;

    color: white;
}

.confirm-yes.cancel-confirm:hover {
    background: #d35400;
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

    <a
        href="login.php?logout=1"
        class="logout-btn"
    >
        Logout
    </a>

</nav>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="admin-container">


    <!-- HEADER -->

    <div class="admin-header">

        <h1>
            Admin Dashboard
        </h1>

        <p>
            Manage and monitor all clinic reservations.
        </p>

    </div>


    <!-- ERROR -->

    <?php if (isset($error)): ?>

        <div class="error-message">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="stats-grid">


        <div class="stat-card">

            <span>
                Total Bookings
            </span>

            <strong>
                <?= $totalBookings ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Pending
            </span>

            <strong>
                <?= $pendingBookings ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Confirmed
            </span>

            <strong>
                <?= $confirmedBookings ?>
            </strong>

        </div>


        <div class="stat-card">

            <span>
                Cancelled
            </span>

            <strong>
                <?= $cancelledBookings ?>
            </strong>

        </div>


    </div>


    <!-- =====================================================
         BOOKINGS
    ====================================================== -->

    <div class="bookings-card">

        <h2>
            All Reservations
        </h2>


        <?php if (empty($bookings)): ?>

            <div class="empty-message">

                <p>
                    There are currently no bookings.
                </p>

            </div>


        <?php else: ?>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Client
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Time
                            </th>

                            <th>
                                Service
                            </th>

                            <th>
                                Therapist
                            </th>

                            <th>
                                Duration
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($bookings as $booking): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                #<?= htmlspecialchars(
                                    $booking['id']
                                ) ?>

                            </td>


                            <!-- CLIENT -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $booking['client_name']
                                    ) ?>

                                </strong>

                            </td>


                            <!-- EMAIL -->

                            <td>

                                <?= htmlspecialchars(
                                    $booking['client_email']
                                ) ?>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?= htmlspecialchars(
                                    $booking['booking_date']
                                ) ?>

                            </td>


                            <!-- TIME -->

                            <td>

                                <?= htmlspecialchars(
                                    substr(
                                        $booking['booking_time'],
                                        0,
                                        5
                                    )
                                ) ?>

                            </td>


                            <!-- SERVICE -->

                            <td>

                                <?= htmlspecialchars(
                                    $booking['service']
                                ) ?>

                            </td>


                            <!-- THERAPIST -->

                            <td>

                                <?= htmlspecialchars(
                                    $booking['therapist']
                                ) ?>

                            </td>


                            <!-- DURATION -->

                            <td>

                                <?= htmlspecialchars(
                                    $booking['duration']
                                ) ?>

                                min

                            </td>


                            <!-- PRICE -->

                            <td>

                                €<?= htmlspecialchars(
                                    $booking['price']
                                ) ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="status <?= htmlspecialchars(
                                        $booking['status']
                                    ) ?>"
                                >

                                    <?= ucfirst(
                                        htmlspecialchars(
                                            $booking['status']
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="action-buttons">


                                    <!-- CANCEL -->

                                    <?php if (
                                        $booking['status']
                                        !== 'cancelled'
                                    ): ?>

                                        <form
                                            class="cancel-form"
                                            action="cancel_booking.php"
                                            method="POST"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= htmlspecialchars(
                                                    $booking['id']
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="cancel-btn"
                                            >
                                                Cancel
                                            </button>

                                        </form>

                                    <?php endif; ?>


                                    <!-- DELETE -->

                                    <form
                                        class="delete-form"
                                        action="delete_booking.php"
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= htmlspecialchars(
                                                $booking['id']
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-btn"
                                        >
                                            Delete
                                        </button>

                                    </form>


                                </div>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

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
     CONFIRMATION MODAL
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
            id="confirmIcon"
            class="confirm-icon"
        >
            ?
        </div>

        <h3 id="confirmTitle">
            Are you sure?
        </h3>

        <p id="confirmText">
            Please confirm this action.
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
                Confirm
            </button>

        </div>

    </div>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       ELEMENTS
    ========================================================= */

    const successPopup =
        document.getElementById('successPopup');

    const successTitle =
        document.getElementById('successTitle');

    const successMessage =
        document.getElementById('successMessage');


    const confirmOverlay =
        document.getElementById('confirmOverlay');

    const confirmIcon =
        document.getElementById('confirmIcon');

    const confirmTitle =
        document.getElementById('confirmTitle');

    const confirmText =
        document.getElementById('confirmText');

    const confirmNo =
        document.getElementById('confirmNo');

    const confirmYes =
        document.getElementById('confirmYes');


    let selectedForm = null;

    let selectedAction = null;

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

    function openConfirmModal(form, action) {

        selectedForm = form;

        selectedAction = action;


        if (action === 'cancel') {

            confirmIcon.textContent = '!';
            confirmIcon.className =
                'confirm-icon cancel-icon';

            confirmTitle.textContent =
                'Cancel this booking?';

            confirmText.textContent =
                'Are you sure you want to cancel this appointment? The time slot will become available again.';

            confirmYes.textContent =
                'Yes, cancel booking';

            confirmYes.className =
                'confirm-yes cancel-confirm';

        }


        if (action === 'delete') {

            confirmIcon.textContent = '×';
            confirmIcon.className =
                'confirm-icon delete-icon';

            confirmTitle.textContent =
                'Delete this booking?';

            confirmText.textContent =
                'This action will permanently remove the booking. This cannot be undone.';

            confirmYes.textContent =
                'Yes, delete booking';

            confirmYes.className =
                'confirm-yes delete-confirm';

        }


        confirmOverlay.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    /* =========================================================
       CLOSE CONFIRMATION MODAL
    ========================================================= */

    function closeConfirmModal() {

        confirmOverlay.classList.remove('show');

        document.body.style.overflow = '';

        selectedForm = null;

        selectedAction = null;

    }


    /* =========================================================
       CANCEL FORMS
    ========================================================= */

    document
        .querySelectorAll('.cancel-form')
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    event.preventDefault();

                    openConfirmModal(
                        form,
                        'cancel'
                    );

                }
            );

        });


    /* =========================================================
       DELETE FORMS
    ========================================================= */

    document
        .querySelectorAll('.delete-form')
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    event.preventDefault();

                    openConfirmModal(
                        form,
                        'delete'
                    );

                }
            );

        });


    /* =========================================================
       NO / CANCEL
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

            if (event.target === confirmOverlay) {

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
       CONFIRM ACTION
    ========================================================= */

    confirmYes.addEventListener(
        'click',
        async function () {

            if (!selectedForm) {
                return;
            }


            const form =
                selectedForm;

            const action =
                selectedAction;


            confirmYes.disabled = true;

            confirmNo.disabled = true;


            if (action === 'cancel') {

                confirmYes.textContent =
                    'Cancelling...';

            } else {

                confirmYes.textContent =
                    'Deleting...';

            }


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


                const result =
                    await response.json();


                if (result.success) {

                    closeConfirmModal();


                    if (action === 'cancel') {

                        showSuccess(
                            'Booking cancelled',
                            'The appointment has been cancelled successfully.'
                        );

                    } else {

                        showSuccess(
                            'Booking deleted',
                            'The booking has been permanently removed.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh dashboard
                    |--------------------------------------------------------------------------
                    */

                    setTimeout(function () {

                        window.location.reload();

                    }, 1000);


                } else {

                    closeConfirmModal();

                    alert(
                        result.message ||
                        'The action could not be completed.'
                    );

                }


            } catch (error) {

                console.error(
                    'Action error:',
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


</body>

</html>