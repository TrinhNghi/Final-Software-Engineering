<?php
require_once 'db.php';

$error = '';
$success = '';
$email = '';

if (isset($_POST['email'])) {
    $email = strtolower(trim($_POST['email']));
    if (empty($email)) {
        $error = 'Please enter your email';
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = 'This is not a valid email address';
    } else {
        $result = generate_reset_token($email);
        error_log("forgot_password: generate_reset_token result for email $email: " . json_encode($result));
        if ($result['code'] == 0) {
            if ($result['success']) {
                $success = 'A password reset link has been sent to your email. Please check your inbox and spam/junk folder.';
            } else {
                $error = 'Failed to send reset email. Please try again later or contact support.';
            }
        } else {
            $error = $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
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

        .forgot-box {
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
    <div class="forgot-box p-4">
        <h3 class="text-center text-dark mb-4">Reset Password</h3>
        <form method="post" action="" novalidate>
            <div class="form-group">
                <label for="email" class="text-dark">Email</label>
                <input name="email" id="email" type="text" class="form-control" placeholder="Email address" value="<?= htmlspecialchars($email) ?>">
            </div>
            <div class="form-group">
                <p class="text-dark">If your email exists in the database, you will receive an email containing the reset password instructions.</p>
            </div>
            <div class="form-group">
                <?php if (!empty($error)): ?>
                    <div class='alert alert-danger'><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class='alert alert-success'><?= $success ?></div>
                <?php endif; ?>
                <button class="btn btn-success w-100">Reset Password</button>
            </div>
            <div class="form-group text-center">
                <p class="text-dark">Remember your password? <a href="login.php" class="text-primary">Login</a>.</p>
            </div>
        </form>
    </div>
</div>
</body>
</html>