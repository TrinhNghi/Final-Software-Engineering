<?php
// Start session to access user data
session_start();
require_once('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$conn = open_database();
$usercurrent = $_SESSION['user'];
$message = '';
$message_type = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    try {
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);

        // Validate inputs
        if (empty($firstname) || empty($lastname) || empty($email)) {
            throw new Exception('All fields are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }
        if (strlen($firstname) > 64 || strlen($lastname) > 64 || strlen($email) > 64) {
            throw new Exception('Input fields must not exceed 64 characters.');
        }

        // Check if email is already used by another user
        $stmt = $conn->prepare('SELECT id FROM account WHERE email = ? AND username != ?');
        $stmt->bind_param('ss', $email, $usercurrent);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('Email is already in use by another account.');
        }
        $stmt->close();

        // Update user profile
        $stmt = $conn->prepare('UPDATE account SET firstname = ?, lastname = ?, email = ? WHERE username = ?');
        $stmt->bind_param('ssss', $firstname, $lastname, $email, $usercurrent);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update profile.');
        }
        $stmt->close();

        $message = 'Profile updated successfully.';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    try {
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Validate password
        if ($newPassword !== $confirmPassword) {
            throw new Exception('Passwords do not match.');
        }
        if (strlen($newPassword) < 6) {
            throw new Exception('Password must have at least 6 characters.');
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            throw new Exception('Password must contain at least 1 uppercase letter.');
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            throw new Exception('Password must contain at least 1 lowercase letter.');
        }
        if (!preg_match('/\d/', $newPassword)) {
            throw new Exception('Password must contain at least 1 number.');
        }
        if (!preg_match('/[!@#$%^&*]/', $newPassword)) {
            throw new Exception('Password must contain at least 1 special character (!@#$%^&*).');
        }

        // Fetch user email
        $stmt = $conn->prepare('SELECT email FROM account WHERE username = ?');
        $stmt->bind_param('s', $usercurrent);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('User not found.');
        }
        $user = $result->fetch_assoc();
        $email = $user['email'];
        $stmt->close();

        // Update password using db.php function
        $result = update_password($email, $newPassword, '');
        if ($result['code'] !== 0) {
            throw new Exception($result['error']);
        }

        $message = 'Password updated successfully.';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Fetch user data
$sql = 'SELECT id, username, firstname, lastname, email FROM account WHERE username = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $usercurrent);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
} else {
    header('Location: login.php');
    exit();
}
$stmt->close();

// Fetch booking history
$sqlHistory = '
    SELECT 
        r.checkin_date, 
        r.checkout_date, 
        r.total_amount, 
        rm.room_name
    FROM reservation r
    JOIN rooms rm ON r.room_id = rm.id
    WHERE r.user_id = ?
    ORDER BY r.checkin_date DESC
';
$stmtHistory = $conn->prepare($sqlHistory);
$stmtHistory->bind_param('i', $user['id']);
$stmtHistory->execute();
$resultHistory = $stmtHistory->get_result();

$bookingHistory = [];
while ($row = $resultHistory->fetch_assoc()) {
    $bookingHistory[] = $row;
}
$stmtHistory->close();

$conn->close();
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

        .alert {
            margin-bottom: 20px;
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
                        <a class="nav-link" href="requestservice.php" style="color: #6a1000;">Request Service</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php" style="color: #6a1000;">
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
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
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
                                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['checkout_date']); ?></td>
                                    <td><?php echo number_format($booking['total_amount'], 2); ?> VND</td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($bookingHistory)): ?>
                                <tr><td colspan="4">No bookings found.</td></tr>
                            <?php endif; ?>
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
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" method="POST" action="profile.php">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Re-enter New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div id="error-message"></div>
                        <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#changePasswordForm').on('submit', function (e) {
                var pass = $('#newPassword').val();
                var passConfirm = $('#confirmPassword').val();
                var error = '';

                if (pass.length < 6) {
                    error = 'Password must have at least 6 characters.';
                } else if (!/(?=.*[A-Z])/.test(pass)) {
                    error = 'Password must contain at least 1 uppercase letter.';
                } else if (!/(?=.*[a-z])/.test(pass)) {
                    error = 'Password must contain at least 1 lowercase letter.';
                } else if (!/(?=.*\d)/.test(pass)) {
                    error = 'Password must contain at least 1 number.';
                } else if (!/(?=.*[!@#$%^&*])/.test(pass)) {
                    error = 'Password must contain at least 1 special character (!@#$%^&*).';
                } else if (pass !== passConfirm) {
                    error = 'Passwords do not match.';
                }

                if (error) {
                    e.preventDefault();
                    $('#error-message').html('<div class="alert alert-danger">' + error + '</div>');
                }
            });
        });
    </script>
</body>
</html>