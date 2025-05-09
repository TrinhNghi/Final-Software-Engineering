<?php
// Start session to access user data
session_start();

// Check if the receptionist is logged in
// if (!isset($_SESSION['receptionist'])) {
//     header('Location: login.php'); // Redirect to login page if not logged in
//     exit();
// }

// Example session data (replace with actual session data from your application)
$_SESSION['receptionist'] = [
    'name' => 'Receptionist User',
    'role' => 'Receptionist',
    'email' => 'receptionist@example.com'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard</title>
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

        .navbar {
            background-color: #A87E62;
            color: white;
            padding: 15px;
        }

        .navbar .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .navbar .navbar-brand:hover {
            color: #f8f9fa;
        }

        .navbar .nav-link {
            color: white;
        }

        .navbar .nav-link:hover {
            color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #6a1000;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #5a0e00;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
        }

        .btn-primary {
            background-color: #6a1000;
            border-color: #6a1000;
        }

        .btn-primary:hover {
            background-color: #5a0e00;
            border-color: #5a0e00;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fa fa-home" style="font-size: 24px;"></i> Receptionist Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo $_SESSION['receptionist']['name']; ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Receptionist Menu</h4>
        <a href="#" onclick="renderContent('manageBooking')"><i class="fa fa-calendar"></i> Manage Booking</a>
        <a href="#" onclick="renderContent('manageService')"><i class="fa fa-cogs"></i> Manage Service Requests</a>
        <a href="#" onclick="renderContent('checkInOut')"><i class="fa fa-sign-in"></i> Check-In/Check-Out</a>
        <a href="#" onclick="renderContent('support')"><i class="fa fa-life-ring"></i> Support</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['receptionist']['name']; ?>!</h2>
        <p>Your role: <?php echo $_SESSION['receptionist']['role']; ?></p>
        <p>Select an option from the sidebar to manage the system.</p>
    </div>

    <script>
        // Function to render content dynamically
        function renderContent(section) {
            const contentDiv = document.querySelector('.content');
            switch (section) {
                case 'manageBooking':
                    contentDiv.innerHTML = `
                        <h2>Manage Booking</h2>
                        <p>Here you can view, edit, and delete bookings.</p>
                        <button class="btn btn-primary mb-3">Add New Booking</button>
                    `;
                    break;
                case 'manageService':
                    contentDiv.innerHTML = `
                        <h2>Manage Service Requests</h2>
                        <p>Here you can view and respond to customer service requests.</p>
                        <button class="btn btn-primary">View All Requests</button>
                    `;
                    break;
                case 'checkInOut':
                    contentDiv.innerHTML = `
                        <div class="row">
                            <div class="col-md-6 p-1">
                                <div class="section-box px-4">
                                    <h4 class="text-center mb-4">Pending Check-Ins</h4>
                                    <div id="pendingCheckIns">
                                        <!-- Pending check-ins will be displayed here dynamically -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 p-1">
                                <div class="section-box px-4">
                                    <h4 class="text-center mb-4">Waiting for Check-Out</h4>
                                    <div id="waitingCheckOuts">
                                        <!-- Rooms waiting for check-out will be displayed here dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                case 'support':
                    contentDiv.innerHTML = `
                        <h2>Support Tickets</h2>
                        <p>Here you can view and respond to support tickets from customers.</p>
                    `;
                    break;
                default:
                    contentDiv.innerHTML = `
                        <h2>Welcome, Receptionist</h2>
                        <p>Select an option from the sidebar to manage the system.</p>
                    `;
            }
        }
    </script>
</body>
</html>