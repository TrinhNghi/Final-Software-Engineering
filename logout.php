<?php
    session_start();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Đăng xuất</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <link
      rel="stylesheet"
      href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
      integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
      crossorigin="anonymous"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-6 mt-5 mx-auto p-3 border rounded">
        <h4>Logout Successful</h4>
        <p>Your account has been logged out of the system.</p>
        <p>Click <a href="login.php">here</a> to return to the login page, or the website will automatically redirect in <span id="counter" class="text-danger">5</span> seconds.</p>
        <a class="btn btn-success px-5" href="login.php">Login</a>

        </div>
      </div>
    </div>
  <script>
      let duration = 5;
      let countDown = 5;
      let id = setInterval(() => {

          countDown --;
          if (countDown >= 0) {
              $('#counter').html(countDown);
          }
          if (countDown == -1) {
              clearInterval(id);
              window.location.href = 'login.php';
          }

      }, 1000);
  </script>
  </body>
</html>
