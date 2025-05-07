<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    require_once('db.php');
    $error = '';
    $success = '';
    $first_name = '';
    $last_name = '';
    $email = '';
    $user = '';
    $pass = '';
    $pass_confirm = '';
    $result = '';
    if (isset($_POST['first']) && isset($_POST['last']) && isset($_POST['email'])
    && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['pass-confirm']))
    {
        $first_name = $_POST['first'];
        $last_name = $_POST['last'];
        $email = $_POST['email'];
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $pass_confirm = $_POST['pass-confirm'];

        if (empty($first_name)) {
            $error = 'Please enter your first name';
        }
        else if (empty($last_name)) {
            $error = 'Please enter your last name';
        }
        else if (empty($email)) {
            $error = 'Please enter your email';
        }
        else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $error = 'This is not a valid email address';
        }
        else if (empty($user)) {
            $error = 'Please enter your username';
        }
        else if (empty($pass)) {
            $error = 'Please enter your password';
        }
        else if (strlen($pass) < 6) {
            $error = 'Password must have at least 6 characters';
        }
        else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]+$/', $pass)) {
            $error = 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character (!@#$%^&*)';
        }
        else if ($pass != $pass_confirm) {
            $error = 'Password does not match';
        }
        else {
            // register a new account
            $result = register($user, $pass, $first_name, $last_name, $email);

            if($result['code'] == 0){
                if (send_activation_email($email, $result['token'])) {
                    $success = 'Account created successfully! Please check your email (and spam/junk folder) to activate your account.';
                } else {
                    $error = 'Account created, but failed to send activation email. Please contact support.';
                }
            }
            else if($result['code'] == 1){
                $error = 'This email is already exists';
            }
            else if($result['code'] == 3){
                $error = 'This username is already exists';
            }
            else{
                $error = 'An error occurred. Please try again later!';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register an account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        .gradient-bg {
            background: 	#A87E62;
background: linear-gradient(90deg, rgba(168, 126, 98, 1) 0%, rgba(217, 183, 158, 1) 41%, rgba(221, 191, 168, 1) 65%, rgba(235, 223, 204, 1) 100%);
    width: 100%;
    height: 100vh;
}

.register-box {
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
    <div class="register-box p-4">
        <h3 class="text-center text-dark mb-4">Create a New Account</h3>
        <form method="post" action="" novalidate>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstname" class="text-dark">First Name</label>
                    <input value="<?= $first_name?>" name="first" required class="form-control" type="text" placeholder="First name" id="firstname">
                </div>
                <div class="form-group col-md-6">
                    <label for="lastname" class="text-dark">Last Name</label>
                    <input value="<?= $last_name?>" name="last" required class="form-control" type="text" placeholder="Last name" id="lastname">
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="text-dark">Email</label>
                <input value="<?= $email?>" name="email" required class="form-control" type="email" placeholder="Email" id="email">
            </div>
            <div class="form-group">
                <label for="user" class="text-dark">Username</label>
                <input value="<?= $user?>" name="user" required class="form-control" type="text" placeholder="Username" id="user">
            </div>
            <div class="form-group">
                <label for="pass" class="text-dark">Password</label>
                <input value="<?= $pass?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
            </div>
            <div class="form-group">
                <label for="pass2" class="text-dark">Confirm Password</label>
                <input value="<?= $pass_confirm?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass2">
            </div>
            <div class="form-group">
                <?php if (!empty($error)): ?>
                    <div class='alert alert-danger'><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class='alert alert-success'><?= $success ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </div>
            <div class="form-group text-center">
                <p class="text-dark">Already have an account? <a href="login.php" class="text-primary">Login</a>.</p>
            </div>
        </form>
    </div>
</div>
</body>

</html>

