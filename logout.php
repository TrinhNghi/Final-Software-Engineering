<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Logout</title>
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

        .logout-box {
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
    <div class="logout-box p-4">
        <h3 class="text-center text-dark mb-4">Logout Successful</h3>
        <p class="text-dark text-center">Your account has been logged out of the system.</p>
        <p class="text-dark text-center">Click <a href="login.php" class="text-primary">here</a> to return to the login page, or the website will automatically redirect in <span id="counter" class="text-danger">5</span> seconds.</p>
        <div class="text-center">
            <a class="btn btn-success px-5" href="login.php">Login</a>
        </div>
    </div>
</div>
<script>
    let countDown = 5;
    const counterElement = document.getElementById('counter');
    const interval = setInterval(() => {
        countDown--;
        if (countDown >= 0) {
            counterElement.textContent = countDown;
        }
        if (countDown === 0) {
            clearInterval(interval);
            window.location.href = 'login.php';
        }
    }, 1000);
</script>
</body>
</html>
