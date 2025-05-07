<?php
require_once 'db.php';

$error = '';
$success = '';
$email = '';
$pass = '';
$pass_confirm = '';
$token_valid = false;

// Log URL parameters
error_log("reset_password.php accessed with email: " . ($_GET['email'] ?? 'none') . ", token: " . ($_GET['token'] ?? 'none'));

// Handle GET request for token verification
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = strtolower(trim($_GET['email']));
    $token = $_GET['token'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = 'This is not a valid email address!';
    } else if (strlen($token) != 32) {
        $error = 'This is not a valid reset token!';
    } else {
        $result = verify_reset_token($email, $token);
        error_log("verify_reset_token result for email $email, token $token: " . json_encode($result));
        if ($result['code'] == 0) {
            $token_valid = true;
        } else {
            $error = 'This reset link is invalid or expired. <a href="forgot_password.php">Request a new one</a>.';
        }
    }
} else {
    $error = 'Invalid email address or token! <a href="forgot_password.php">Request a new one</a>.';
}

// Handle POST request for password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $pass = $_POST['pass'];
    $pass_confirm = $_POST['pass-confirm'];

    if (empty($email)) {
        $error = 'Please enter your email';
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = 'This is not a valid email address';
    } else if (empty($pass)) {
        $error = 'Please enter your password';
    } else if (strlen($pass) < 6) {
        $error = 'Password must have at least 6 characters';
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]+$/', $pass)) {
        $error = 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character (!@#$%^&*)';
    } else if ($pass != $pass_confirm) {
        $error = 'Password does not match';
    } else if ($email !== strtolower(trim($_GET['email']))) {
        $error = 'Email does not match the reset link';
    } else {
        $token = $_GET['token'];
        $result = update_password($email, $pass, $token);
        error_log("update_password result for email $email: " . json_encode($result));
        if ($result['code'] == 0) {
            $success = 'Password updated successfully! Redirecting to login...';
            $email = $pass = $pass_confirm = '';
            $token_valid = false;
        } else {
            $error = $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        .gradient-bg {
            background: #A87E62;
            background: linear-gradient(90deg, rgba(168, 126, 98, 1) 0%, rgba(217, 183, 158, 1) 41%, rgba(221, 191, 168, 1) 65%, rgba(235, 223, 204, 1) 100%);
            width: 100%;
            height: 100vh;
        }

        .reset-box {
            background-color: white;
            backdrop-filter: blur(10px);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: rgb(77, 38, 38) 0px 20px 30px -10px;
            padding: 30px 20px;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.9);
            color: #6a1000;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #6a1000;
            box-shadow: 0 0 5px rgba(106, 16, 0, 0.8);
        }

        .btn-success {
            background-color: #6a1000;
            border-color: #6a1000;
            color: white;
        }

        .btn-success:hover {
            background-color: #5a0e00;
            border-color: #5a0e00;
        }

        .text-primary {
            color: #5a0e00 !important;
        }
    </style>
</head>
<body>
<div class="container-fluid vh-100 d-flex justify-content-center align-items-center gradient-bg">
    <div class="reset-box p-4">
        <h3 class="text-center text-dark mb-4">Reset Password</h3>
        <form novalidate method="post" action="">
            <div class="form-group">
                <label for="email" class="text-dark">Email</label>
                <input readonly value="<?= htmlspecialchars($email) ?>" name="email" id="email" type="text" class="form-control" placeholder="<?= htmlspecialchars($email ?: 'Email address') ?>">
            </div>
            <div class="form-group">
                <label for="pass" class="text-dark">Password</label>
                <input value="<?= htmlspecialchars($pass) ?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
            </div>
            <div class="form-group">
                <label for="pass2" class="text-dark">Confirm Password</label>
                <input value="<?= htmlspecialchars($pass_confirm) ?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass2">
            </div>
            <div class="form-group">
                <?php if (!empty($error) && ($_SERVER['REQUEST_METHOD'] === 'POST' || !$token_valid)): ?>
                    <div class='alert alert-danger'><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class='alert alert-success'><?= htmlspecialchars($success) ?></div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>
                <?php endif; ?>
                <button class="btn btn-success w-100" <?= $success || !$token_valid ? 'disabled' : '' ?>>Change Password</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>