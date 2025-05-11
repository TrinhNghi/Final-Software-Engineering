<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=HotelManagement', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get user_id from session username
try {
    $stmt = $pdo->prepare('SELECT id FROM account WHERE username = ?');
    $stmt->execute([$_SESSION['user']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: login.php");
        exit();
    }
    $user_id = $user['id'];
} catch (PDOException $e) {
    die('Error fetching user ID: ' . $e->getMessage());
}

// Initialize message
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'send_service_request') {
            $service_id = (int)$_POST['service_id'];
            $reservation_id = !empty($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : null;
            $request_details = trim($_POST['request_details']);

            // Validate reservation_id if provided
            if ($reservation_id) {
                $stmt = $pdo->prepare('SELECT status FROM reservation WHERE id = ? AND user_id = ?');
                $stmt->execute([$reservation_id, $user_id]);
                $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$reservation || $reservation['status'] !== 'checked_in') {
                    throw new Exception('Selected reservation is not checked in or does not belong to you.');
                }
            }

            // Insert service request
            $stmt = $pdo->prepare('
                INSERT INTO request (user_id, service_id, reservation_id, request_type, request_details, status)
                VALUES (?, ?, ?, "service", ?, "pending")
            ');
            $stmt->execute([$user_id, $service_id, $reservation_id, $request_details]);
            $message = 'Service request sent successfully.';
            $message_type = 'success';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }

    // Redirect to current page
    header("Location: requestservice.php");
    exit();
}

// Function to fetch services for service request
function getServicesOptions($pdo, $selected_service_id) {
    try {
        $stmt = $pdo->query('SELECT id, service_name FROM service ORDER BY service_name');
        $options = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_service_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['service_name']) . '</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Function to fetch reservations for service request
function getReservationsOptions($pdo, $selected_reservation_id, $user_id) {
    try {
        $stmt = $pdo->prepare('
            SELECT r.id, a.firstname, a.lastname, ro.room_name
            FROM reservation r
            JOIN account a ON r.user_id = a.id
            JOIN rooms ro ON r.room_id = ro.id
            WHERE r.user_id = ? AND r.status != "checked_out"
            ORDER BY r.checkin_date DESC
        ');
        $stmt->execute([$user_id]);
        $options = '<option value="">None</option>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_reservation_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>#' . $row['id'] . ' - ' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . ' (' . htmlspecialchars($row['room_name']) . ')</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Function to fetch approved service requests for the user
function getApprovedServiceRequests($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare('
            SELECT req.id, s.service_name, r.id AS reservation_id, ro.room_name, req.request_details
            FROM request req
            LEFT JOIN service s ON req.service_id = s.id
            LEFT JOIN reservation r ON req.reservation_id = r.id
            LEFT JOIN rooms ro ON r.room_id = ro.id
            WHERE req.user_id = ? AND req.status = "approved" AND req.request_type = "service"
            ORDER BY req.id DESC
        ');
        $stmt->execute([$user_id]);
        $html = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $html .= '
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($row['service_name']) . '</h5>
                            <p class="card-text"><strong>Reservation:</strong> ' . ($row['reservation_id'] ? '#' . $row['reservation_id'] . ' (' . htmlspecialchars($row['room_name']) . ')' : 'None') . '</p>
                            <p class="card-text"><strong>Details:</strong> ' . htmlspecialchars($row['request_details'] ?: 'None') . '</p>
                            <p class="card-text"><strong>Status:</strong> <span class="badge badge-success">Approved</span></p>
                        </div>
                    </div>
                </div>
            ';
        }
        return $html ?: '<p>No approved service requests found.</p>';
    } catch (PDOException $e) {
        return '<p>Error: ' . $e->getMessage() . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Service</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden; /* Prevent scrolling on the entire page */
        }

        .gradient-bg {
            background: #A87E62;
            background: linear-gradient(90deg, rgba(168, 126, 98, 1) 0%, rgba(217, 183, 158, 1) 41%, rgba(221, 191, 168, 1) 65%, rgba(235, 223, 204, 1) 100%);
            height: 100vh;
        }

        .filter-box {
            background-color: white;
            margin-top: 20px;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 120px); /* Adjust height to account for navbar */
            overflow-y: auto; /* Allow scrolling inside the filter box if needed */
        }

        .display-section {
            height: calc(100vh - 80px); /* Adjust height to account for navbar */
            overflow-y: auto; /* Allow scrolling inside the display section */
            padding: 20px;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #6a1000;
            border-color: #6a1000;
        }

        .btn-primary:hover {
            background-color: #5a0e00;
            border-color: #5a0e00;
        }

        .alert {
            margin-bottom: 20px;
        }

        .badge-success {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
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
    <div class="container-fluid gradient-bg" style="display: flex; padding-top: 80px; text-align: center;">
        <!-- Request Service Form -->
        <div class="col-md-2"></div>
        <div class="col-md-3 filter-box">
            <h4 class="text-center mb-4">Request a Service</h4>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="send_service_request">
                <div class="form-group">
                    <label for="service_id">Service</label>
                    <select class="form-control" name="service_id" id="service_id" required>
                        <?php echo getServicesOptions($pdo, 0); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reservation_id">Reservation (Optional)</label>
                    <select class="form-control" name="reservation_id" id="reservation_id">
                        <?php echo getReservationsOptions($pdo, 0, $user_id); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="request_details">Details</label>
                    <textarea class="form-control" name="request_details" id="request_details" rows="3" placeholder="Enter request details"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Request</button>
            </form>
        </div>

        <!-- Display Section -->
        <div class="col-md-2"></div>
        <div class="col-md-3 filter-box">	
            <h2 class="text-center mb-4">Requested Services</h2>
            <div class="row" id="requestedServices">
                <?php echo getApprovedServiceRequests($pdo, $user_id); ?>
            </div>
        </div>
    </div>
</body>
</html>