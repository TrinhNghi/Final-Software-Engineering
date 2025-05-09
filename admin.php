<?php
// Start session to access user data
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Example session data (replace with actual session data from your application)
// $_SESSION['admin'] = [
//     'name' => 'Admin User',
//     'role' => 'Administrator',
//     'email' => 'admin@example.com'
// ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
                <i class="fa fa-home" style="font-size: 24px;"></i> Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo $_SESSION['admin']['name']; ?>!</span>
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
        <h4>Admin Menu</h4>
        <a href="#" onclick="renderContent('manageBooking')"><i class="fa fa-calendar"></i> Manage Booking</a>
        <a href="#" onclick="renderContent('manageStaff')"><i class="fa fa-users"></i> Manage Staff</a>
        <a href="#" onclick="renderContent('manageService')"><i class="fa fa-cogs"></i> Manage Service Requests</a>
        <a href="#" onclick="renderContent('checkInOut')"><i class="fa fa-sign-in"></i> Check-In/Check-Out</a>
        <a href="#" onclick="renderContent('support')"><i class="fa fa-life-ring"></i> Support</a>
        <a href="#" onclick="renderContent('reports')"><i class="fa fa-bar-chart"></i> Reports</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['admin']['name']; ?>!</h2>
        <p>Your role: <?php echo $_SESSION['admin']['role']; ?></p>
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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Guest Name</th>
                <th>Room Type</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#B001</td>
                <td>Michael Scott</td>
                <td>Suite</td>
                <td>2025-05-10</td>
                <td>2025-05-12</td>
                <td>
                    <button class="btn btn-sm btn-warning">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
            <tr>
                <td>#B002</td>
                <td>Pam Beesly</td>
                <td>Single</td>
                <td>2025-05-11</td>
                <td>2025-05-13</td>
                <td>
                    <button class="btn btn-sm btn-warning">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
`;

                    break;
                case 'manageStaff':
                    contentDiv.innerHTML = `
    <h2>Manage Staff</h2>
    <p>Here you can view staff details, assign roles, and manage schedules.</p>
    <button class="btn btn-primary mb-3">Add New Staff</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Shift</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#S001</td>
                <td>Jim Halpert</td>
                <td>Receptionist</td>
                <td>Morning</td>
                <td>
                    <button class="btn btn-sm btn-info">Edit</button>
                </td>
            </tr>
            <tr>
                <td>#S002</td>
                <td>Dwight Schrute</td>
                <td>Security</td>
                <td>Night</td>
                <td>
                    <button class="btn btn-sm btn-info">Edit</button>
                </td>
            </tr>
        </tbody>
    </table>
`;

                    break;
                case 'manageService':
                    contentDiv.innerHTML = `
    <h2>Manage Service Requests</h2>
    <p>Here you can view and respond to customer service requests.</p>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Guest Name</th>
                <th>Room</th>
                <th>Service</th>
                <th>Status</th>
                <th>Respond</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#SR001</td>
                <td>Angela Martin</td>
                <td>205</td>
                <td>Extra Towels</td>
                <td><span class="badge badge-warning">Pending</span></td>
                <td><button class="btn btn-success btn-sm">Mark as Done</button></td>
            </tr>
            <tr>
                <td>#SR002</td>
                <td>Kevin Malone</td>
                <td>301</td>
                <td>Food Delivery</td>
                <td><span class="badge badge-success">Completed</span></td>
                <td><button class="btn btn-secondary btn-sm" disabled>Done</button></td>
            </tr>
        </tbody>
    </table>
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
                    populateCheckInOut(); // Populate check-in and check-out data
                    break;
                case 'support':
                    contentDiv.innerHTML = `
    <h2>Support Tickets</h2>
    <p>Here you can view and respond to support tickets from customers.</p>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Guest: Stanley Hudson</h5>
            <p class="card-text">Issue: Wi-Fi not working in room 102</p>
            <p class="card-text"><small class="text-muted">Submitted: 2025-05-08 10:30AM</small></p>
            <button class="btn btn-primary btn-sm">Respond</button>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Guest: Oscar Martinez</h5>
            <p class="card-text">Issue: AC making noise in room 210</p>
            <p class="card-text"><small class="text-muted">Submitted: 2025-05-08 11:45AM</small></p>
            <button class="btn btn-primary btn-sm">Respond</button>
        </div>
    </div>
`;

                    break;
                case 'reports':
                    contentDiv.innerHTML = `
    <h2>Reports</h2>
    <p>Here you can view system reports and analytics.</p>
    <div class="mb-3">
        <label for="reportType">Choose Report Type:</label>
        <select class="form-control" id="reportType">
            <option>Daily Bookings</option>
            <option>Revenue</option>
            <option>Customer Feedback</option>
            <option>Occupancy Rate</option>
        </select>
    </div>
    <button class="btn btn-primary mb-4">Generate Report</button>

    <div class="alert alert-info">
        <strong>Mock Report:</strong> On 2025-05-08, there were 8 bookings. Total revenue was $2,350.
    </div>
`;

                    break;
                default:
                    contentDiv.innerHTML = `
                        <h2>Welcome, Admin</h2>
                        <p>Select an option from the sidebar to manage the system.</p>
                    `;
            }
        }

        // Function to populate check-in and check-out data
        function populateCheckInOut() {
            const pendingCheckIns = [
                { id: 1, guestName: 'John Doe', roomType: 'Single', checkInDate: '2025-05-10' },
                { id: 2, guestName: 'Jane Smith', roomType: 'Double', checkInDate: '2025-05-11' }
            ];

            const waitingCheckOuts = [
                { id: 3, guestName: 'Alice Johnson', roomType: 'Suite', checkOutDate: '2025-05-09' },
                { id: 4, guestName: 'Bob Brown', roomType: 'Double', checkOutDate: '2025-05-10' }
            ];

            let checkInHtml = '';
            pendingCheckIns.forEach(checkIn => {
                checkInHtml += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Guest: ${checkIn.guestName}</h5>
                            <p class="card-text">Room Type: ${checkIn.roomType}</p>
                            <p class="card-text">Check-In Date: ${checkIn.checkInDate}</p>
                            <button class="btn btn-primary btn-sm">Confirm Check-In</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('pendingCheckIns').innerHTML = checkInHtml;

            let checkOutHtml = '';
            waitingCheckOuts.forEach(checkOut => {
                checkOutHtml += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Guest: ${checkOut.guestName}</h5>
                            <p class="card-text">Room Type: ${checkOut.roomType}</p>
                            <p class="card-text">Check-Out Date: ${checkOut.checkOutDate}</p>
                            <button class="btn btn-primary btn-sm">Confirm Check-Out</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('waitingCheckOuts').innerHTML = checkOutHtml;
        }
    </script>
</body>
</html>