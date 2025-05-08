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
            <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" id="email" type="text" class="form-control" placeholder="Email address" value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="form-group">
                    <p>If your email exists in the database, you will receive an email containing the reset password instructions.</p>
                </div>
                <div class="form-group">
                    <?php if (!empty($error)): ?>
                        <div class='alert alert-danger'><?= $error ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class='alert alert-success'><?= $success ?></div>
                    <?php endif; ?>
                    <button class="btn btn-success px-5">Reset password</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>