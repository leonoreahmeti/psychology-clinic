<?php

session_start();

/* LOGOUT */
if (isset($_GET['logout']) && $_GET['logout'] === '1') {

    $_SESSION = [];

    session_destroy();

    header('Location: login.php');
    exit;
}

/* Nëse admini është tashmë i kyçur */
if (
    isset($_SESSION['admin_logged_in']) &&
    $_SESSION['admin_logged_in'] === true
) {
    header('Location: index.php');
    exit;
}

$error = '';

/* LOGIN */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    /*
     * Për momentin kredencialet e adminit
     */
    if ($username === 'admin' && $password === 'admin123') {

        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;

        header('Location: index.php');
        exit;

    } else {

        $error = 'Invalid username or password.';
    }
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

    <title>Admin Login - Psychology Clinic</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #f3f7f5;
        }

        .login-box {
            width: 90%;
            max-width: 400px;

            background: white;

            padding: 35px;

            border-radius: 18px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;

            color: #243746;

            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;

            color: #777;

            margin-bottom: 25px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            color: #243746;

            font-weight: 600;
        }

        input {
            width: 100%;

            padding: 13px;

            margin-bottom: 18px;

            border: 1px solid #d6d6d6;

            border-radius: 9px;

            font-size: 15px;
        }

        input:focus {
            outline: none;

            border-color: #6fcf97;

            box-shadow:
                0 0 5px rgba(111, 207, 151, 0.3);
        }

        button {
            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 9px;

            background: #6fcf97;

            color: white;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s;
        }

        button:hover {
            background: #57b87e;
        }

        .error {
            background: #f8d7da;

            color: #721c24;

            padding: 10px;

            border-radius: 8px;

            margin-bottom: 18px;

            text-align: center;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h1>Admin Login</h1>

    <p class="subtitle">
        Psychology Clinic
    </p>

    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>

</html>