<?php ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In and Check-Out</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            /* Prevent scrolling on the entire page */
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
            /* Adjust height to account for navbar */
            overflow-y: auto;
            /* Allow scrolling inside the section */
            width: 100%;
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
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light"
        style="padding: 20px; width: 100%; position: fixed; opacity: 0.9; z-index: 1000;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php" style="font-weight: bold; color: #6a1000;">
                <i class="fa fa-home" style="font-size: 24px; color: #6a1000;"></i> Peace Home
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="userprofile.php" style="color: #6a1000;">
                                <i class="fa fa-user-circle" style="font-size: 20px;"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" style="color: #6a1000;">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php" style="color: #6a1000;">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid gradient-bg" style="padding-top: 80px;">
        <div class="row" style="width: 100%; margin: 0;">
            <!-- Pending Check-Ins -->
            <div class="col-md-6 p-1">
                <div class="section-box px-4">
                    <h4 class="text-center mb-4">Pending Check-Ins</h4>
                    <div id="pendingCheckIns">
                        <!-- Pending check-ins will be displayed here dynamically -->
                    </div>
                </div>
            </div>

            <!-- Waiting for Check-Out -->
            <div class="col-md-6 p-1">
            <div class="section-box px-4">
                <h4 class="text-center mb-4">Waiting for Check-Out</h4>
                <div id="waitingCheckOuts">
                    <!-- Rooms waiting for check-out will be displayed here dynamically -->
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function () {
            // Sample data for pending check-ins and check-outs
            const pendingCheckIns = [
                { id: 1, guestName: 'John Doe', roomType: 'Single', checkInDate: '2025-05-10' },
                { id: 2, guestName: 'Jane Smith', roomType: 'Double', checkInDate: '2025-05-11' }
            ];

            const waitingCheckOuts = [
                { id: 3, guestName: 'Alice Johnson', roomType: 'Suite', checkOutDate: '2025-05-09' },
                { id: 4, guestName: 'Bob Brown', roomType: 'Double', checkOutDate: '2025-05-10' }
            ];

            // Populate Pending Check-Ins
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
            $('#pendingCheckIns').html(checkInHtml);

            // Populate Waiting for Check-Out
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
            $('#waitingCheckOuts').html(checkOutHtml);
        });
    </script>
</body>

</html>