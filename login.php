<?php
    session_start();
    
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }


    require_once('db.php');
    $error = '';

    $user = '';
    $pass = '';

    if (isset($_COOKIE['remember_user'])) {
        $user = $_COOKIE['remember_user'];
    }
    
    if (isset($_COOKIE['remember_pass'])) {
        $pass = $_COOKIE['remember_pass'];
    }

    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];

        if (empty($user)) {
            $error = 'Please enter your username';
        }
        else if (empty($pass)) {
            $error = 'Please enter your password';
        }
        else if (strlen($pass) < 6) {
            $error = 'Password must have at least 6 characters';
        }
        else{
        
            $result = login($user, $pass);
            if($result['code'] == 0){
                $data = $result['data'];
                $_SESSION['user'] = $user;
                $_SESSION['name'] = $data['firstname'].' '.$data['lastname'];
                session_regenerate_id(true);

                if (isset($_POST['remember'])) {
                    setcookie('remember_user', $user, time() + (86400 * 30), "/");
                    setcookie('remember_pass', $pass, time() + (86400 * 30), "/");
                } else {

                    setcookie('remember_user', '', time() - 3600, "/");
                    setcookie('remember_pass', '', time() - 3600, "/");
                }

                header('Location: index.php');
                exit();
            }
            
            else{
                $error = $result['error'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
    /* Updated style for left side text overlay */
.left-text-overlay {
    position: absolute;
    top: 50%;
    left: 10%;
    transform: translateY(-70%);
    color: #ffd3b6; /* Light color for text */
    z-index: 2;
    max-width: 400px;
    font-family: 'Segoe UI', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 1); /* Shadow effect for readability */

    background-color: rgba(255, 255, 255, 0.3); /* soft warm white */
    backdrop-filter: blur(6px);
    border-radius: 20px;
    width: 100%;
    max-width: 300px; /* smaller form width */
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}
.left-text-overlay h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: auto;
    text-align: center;	
}

.left-text-overlay p {
    font-size: 1.2rem;
    line-height: 1.6;
    padding: 0 20px;
    text-align: center;
}
/* Update the login box style to match */
.login-box {
    background-color: rgba(255, 255, 255, 0.3); /* Soft white */
    backdrop-filter: blur(10px); /* Increased blur for better effect */
    border-radius: 20px;
    width: 100%;
    max-width: 500px; /* Slightly wider form */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); /* Deeper shadow for the form */
    padding: 30px 20px; /* Increased padding for more spacing */
    border-radius: 1;
}

/* Adjust background gradient and color */
.gradient-bg {
    background: linear-gradient(to right, rgba(232, 211, 192, 1), rgba(201, 167, 123, 0.7)); /* Softer gradient */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Login form text */
.login-box h3 {
    font-size: 1.8rem;
    color: #6a1000; /* Match the dark red from the theme */
    font-weight: bold;
    text-align: center;
}

/* Form input field style */
.form-control {
    background-color: rgba(255, 255, 255, 0.9); /* Light background for form fields */
    color: #6a1000; /* Dark red text */
    border-radius: 10px; /* Rounded corners */
}

/* Input focus style */
.form-control:focus {
    border-color: #6a1000; /* Dark red border on focus */
    box-shadow: 0 0 5px rgba(106, 16, 0, 0.8); /* Slight shadow effect */
}

/* Button styling */
.btn-success {
    background-color: #6a1000; /* Dark red button */
    border-color: #6a1000;
    color: white;
}

.btn-success:hover {
    background-color: #5a0e00; /* Darker red on hover */
    border-color: #5a0e00;
}

/* Links style */
.text-primary {
    color: #5a0e00 !important; /* Light color for links to match the left side theme */
}

.bg-image {
    background-image: url('images/login-bg.jpg');
    background-size: cover;
    background-position: center;
    position: relative;
    width: 100%;
    height: 100%;
}

/* Overlay that fades the image into a warm color */
.image-overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: linear-gradient(to right, rgba(0, 0, 0, 0) 50%, rgba(232, 211, 192, 1) 100%);
    z-index: 1;
}

</style>
</head>
<body>
<div class="container-fluid vh-100 d-flex p-0">
    <!-- Left image side -->
    <div class="col-4 d-none d-md-block p-0 position-relative">
    <!--<div class="left-text-overlay">
        <h1>Welcome to Peace Home</h1>
        <p>A cozy and tranquil getaway in the heart of Vietnam. Make yourself at home while discovering the culture and charm of the surroundings.</p>
    </div>-->
    <div class="h-100 w-100 bg-image position-relative">
        <div class="image-overlay"></div>
    </div>
</div>


<!-- Right form side -->
<div class="col-8 d-flex align-items-center justify-content-center gradient-bg">
    <div class="login-box p-4">
        <h3 class="text-center text-dark mb-4">User Login</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="user" class="text-dark">Username</label>
                <input value="<?= $user ?>" name="user" id="user" type="text" class="form-control" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="password" class="text-dark">Password</label>
                <input name="pass" value="<?= $pass ?>" id="password" type="password" class="form-control" placeholder="Password">
            </div>
            <div class="form-group custom-control custom-checkbox">
                <input <?= isset($_POST['remember']) ? 'checked' : '' ?> name="remember" type="checkbox" class="custom-control-input" id="remember">
                <label class="custom-control-label text-dark" for="remember">Remember login</label>
            </div>
            <div class="form-group">
                <?php if (!empty($error)): ?>
                    <div class='alert alert-danger'><?= $error ?></div>
                <?php endif; ?>
                <button class="btn btn-success w-100">Login</button>
            </div>
            <div class="form-group text-center">
                <p class="text-dark">Don't have an account? <a href="register.php" class="text-primary">Register</a>.</p>
                <p class="text-dark">Forgot password? <a href="forgot.php" class="text-primary">Reset it</a>.</p>
            </div>
        </form>
    </div>
</div>



</div>
</body>

</html>
