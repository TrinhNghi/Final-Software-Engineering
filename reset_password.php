
<?php
require_once 'db.php';
$error = '';
$success = '';
$email = '';
$pass = '';
$pass_confirm = '';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = strtolower(trim($_GET['email']));
    $token = $_GET['token'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = 'This is not a valid email address!';
    } else if (strlen($token) != 32) {
        $error = 'This is not a valid reset token!';
    } else {
        // Verify token
        $result = verify_reset_token($email, $token);
        if ($result['code'] != 0) {
            $error = $result['error'];
        }
    }
} else {
    $error = 'Invalid email address or token!';
}

if (isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pass-confirm']) && empty($error)) {
    $email = strtolower(trim($_POST['email']));
    $pass = $_POST['pass'];
    $pass_confirm = $_POST['pass-confirm'];

    if (empty($email)) {
        $error = 'Please enter your email';
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $error = 'This is not a valid email address';
    } else if (empty($pass)) {
        $error = 'Please enter your password';
    } else if (strlen($pass) < 6) {
        $error = 'Password must have at least 6 characters';
    } else if ($pass != $pass_confirm) {
        $error = 'Password does not match';
    } else if ($email !== strtolower(trim($_GET['email']))) {
        $error = 'Email does not match the reset link';
    } else {
        // Update password
        $result = update_password($email, $pass, $token);
        if ($result['code'] == 0) {
            $success = 'Password updated successfully!';
            $email = $pass = $pass_confirm = '';
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset user password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h3 class="text-center text-secondary mt-5 mb-3">Reset Password</h3>
            <form novalidate method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input readonly value="<?= htmlspecialchars($email) ?>" name="email" id="email" type="text" class="form-control" placeholder="<?= htmlspecialchars($email ?: 'Email address') ?>">
                </div>
                <div class="form-group">
                    <label for="pass">Password</label>
                    <input value="<?= htmlspecialchars($pass) ?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
                    <div class="invalid-feedback">Password is not valid.</div>
                </div>
                <div class="form-group">
                    <label for="pass2">Confirm Password</label>
                    <input value="<?= htmlspecialchars($pass_confirm) ?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass2">
                    <div class="invalid-feedback">Password is not valid.</div>
                </div>
                <div class="form-group">
                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>";
                    }
                    if (!empty($success)) {
                        echo "<div class='alert alert-success'>" . htmlspecialchars($success) . "</div>";
                    }
                    ?>
                    <button class="btn btn-success px-5" <?= $success ? 'disabled' : '' ?>>Change password</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
