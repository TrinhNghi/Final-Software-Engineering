<?php
require_once('db.php'); 

$error = '';
$message = '';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = 'Invalid email address';
        error_log("activate.php: Invalid email format: $email");
    } else if (strlen($token) != 32) {
        $error = 'Invalid token format!';
        error_log("activate.php: Invalid token format: $token (length: " . strlen($token) . ")");
    } else {
        // Check database
        $result = active_account($email, $token);
        if ($result['code'] == 0) {
            $message = 'Your account has been activated. Login now!';
            error_log("activate.php: Account activated successfully for $email");
        } else {
            $error = $result['error'];
            error_log("activate.php: Activation failed for $email: {$result['error']}");
        }
    }
} else {
    $error = 'Invalid activation URL!';
    error_log("activate.php: Missing email or token in URL");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Account Activation</title>
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

        .activation-box {
            background-color: white;
            backdrop-filter: blur(10px);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: rgb(77, 38, 38) 0px 20px 30px -10px;
            padding: 30px 20px;
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
    <div class="activation-box p-4">
        <?php if (!empty($error)) { ?>
            <h3 class="text-center text-danger mb-4">Activation Failed</h3>
            <p class="text-dark text-center"><?= htmlspecialchars($error); ?></p>
            <p class="text-dark text-center">Click <a href="login.php" class="text-primary">here</a> to login.</p>
            <div class="text-center">
                <a class="btn btn-success px-5" href="login.php">Login</a>
            </div>
        <?php } else { ?>
            <h3 class="text-center text-success mb-4">Activation Successful</h3>
            <p class="text-dark text-center">Congratulations! <?= htmlspecialchars($message); ?></p>
            <p class="text-dark text-center">Click <a href="login.php" class="text-primary">here</a> to login and manage your account information.</p>
            <div class="text-center">
                <a class="btn btn-success px-5" href="login.php">Login</a>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
