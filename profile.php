<?php
// Start session to access user data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Example user data (replace this with actual data from your database)
$user = [
    'username' => 'johndoe',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'johndoe@example.com'
];

// Example booking history (replace this with actual data from your database)
$bookingHistory = [
    ['room' => 'Single', 'checkin' => '2025-05-01', 'checkout' => '2025-05-05', 'price' => 200],
    ['room' => 'Double', 'checkin' => '2025-04-15', 'checkout' => '2025-04-20', 'price' => 400],
    ['room' => 'Suite', 'checkin' => '2025-03-10', 'checkout' => '2025-03-15', 'price' => 750]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            background: #f8f9fa;
        }

        .gradient-bg {
            background: #A87E62;
            background: linear-gradient(90deg, rgba(168, 126, 98, 1) 0%, rgba(217, 183, 158, 1) 41%, rgba(221, 191, 168, 1) 65%, rgba(235, 223, 204, 1) 100%);
            height: 100vh;
        }

        .section-box {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .btn-primary {
            background-color: #6a1000;
            border-color: #6a1000;
        }

        .btn-primary:hover {
            background-color: #5a0e00;
            border-color: #5a0e00;
        }

        .modal-header {
            background-color: #6a1000;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding: 20px; width: 100%; position: fixed; opacity: 0.9; z-index: 1000;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php" style="font-weight: bold; color: #6a1000;">
                <i class="fa fa-home" style="font-size: 24px; color: #6a1000;"></i> Peace Home
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="bookroom.php" style="color: #6a1000;">Book Room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="requestservice.php" style="color: #6a1000;">Request service</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="userprofile.php" style="color: #6a1000;">
                            <i class="fa fa-user-circle" style="font-size: 20px;"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php" style="color: #6a1000;">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid gradient-bg" style="padding-top: 80px;">
        <div class="row" style="width: 100%; margin: 0;">
            <!-- Profile Form -->
            <div class="col-md-6 p-1">
                <div class="section-box">
                    <h3 class="text-center mb-4">User Profile</h3>
                    <form method="POST" action="update_profile.php">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                    </form>
                    <button class="btn btn-secondary btn-block mt-3" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                </div>
            </div>

            <!-- Booking History -->
            <div class="col-md-6 p-1">
                <div class="section-box">
                    <h3 class="text-center mb-4">Booking History</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Check-In Date</th>
                                <th>Check-Out Date</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookingHistory as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['room']; ?></td>
                                    <td><?php echo $booking['checkin']; ?></td>
                                    <td><?php echo $booking['checkout']; ?></td>
                                    <td>$<?php echo $booking['price']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" method="POST" action="change_password.php">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Re-enter New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#changePasswordForm').on('submit', function (e) {
                e.preventDefault();

                const currentPassword = $('#currentPassword').val();
                const newPassword = $('#newPassword').val();
                const confirmPassword = $('#confirmPassword').val();

                if (newPassword !== confirmPassword) {
                    alert('New passwords do not match!');
                    return;
                }

                // Submit the form via AJAX or redirect to the server-side handler
                this.submit();
            });
        });
    </script>
</body>
</html>