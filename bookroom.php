<?php 
session_start();
require_once('db.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

$conn = open_database();
if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Database connection failed');
}

$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        const rooms = <?php echo json_encode($rooms); ?>;
    </script>

    <style>
        body {
            margin: 0;
            overflow: hidden;
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
            height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .display-section {
            height: calc(100vh - 80px);
            overflow-y: auto;
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

        .btn-selected {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-selected:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .notification {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
            padding: 15px;
            border-radius: 5px;
            color: white;
            min-width: 250px;
            display: none;
        }

        .notification-success {
            background-color: #28a745;
        }

        .notification-danger {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
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

    <div class="container-fluid gradient-bg" style="display: flex; padding-top: 80px;">
        <div class="col-md-3 filter-box">
            <h4 class="text-center mb-4">Search & Filter</h4>
            <form method="GET" action="bookroom.php">
                <div class="form-group">
                    <label for="roomCategory">Room Category</label>
                    <select class="form-control" id="roomCategory" name="roomCategory">
                        <option value="">All</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="priceRange">Price Range</label>
                    <select class="form-control" id="priceRange" name="priceRange">
                        <option value="">All</option>
                        <option value="0-100000">0 - 100,000</option>
                        <option value="100000-500000">100,000 - 500,000</option>
                        <option value="500000-1000000">500,000 - 1,000,000</option>
                        <option value="1000000+">1,000,000+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="floor">Floor</label>
                    <select class="form-control" id="floor" name="floor">
                        <option value="">All</option>
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="startDate">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="endDate">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="adults">Adults</label>
                            <input type="number" class="form-control" id="adults" name="adults"
                                placeholder="Number of Adults" min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label for="children">Children</label>
                            <input type="number" class="form-control" id="children" name="children"
                                placeholder="Number of Children" min="0" value="0">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </form>
        </div>

        <div class="col-md-9 display-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-center text-white">Available Rooms</h2>
                <button id="bookAllBtn" class="btn btn-primary">Book All</button>
            </div>
            <div class="row" id="roomList">
                <?php foreach ($rooms as $index => $room): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="images/room<?php echo ($index % 3) + 1; ?>.png" class="card-img-top" alt="Room Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                                <p class="card-text"><?php echo number_format($room['price'], 0, '.', ','); ?> VND per night</p>
                                <p class="card-text">Category: <?php echo htmlspecialchars($room['room_category']); ?></p>
                                <p class="card-text">Type: <?php echo htmlspecialchars($room['room_type']); ?></p>
                                <p class="card-text">Room Number: <?php echo $room['room_number']; ?></p>
                                <p class="card-text">Floor: <?php echo $room['floor']; ?></p>
                                <p class="card-text">Capacity: <?php echo $room['max_people']; ?> people</p>
                                <p class="card-text">Description: <?php echo htmlspecialchars($room['description']); ?></p>
                                <button class="btn btn-primary select-room-btn"
                                    data-room-id="<?php echo $room['id']; ?>"
                                    data-room-type="<?php echo htmlspecialchars($room['room_name']); ?>"
                                    data-room-price="<?php echo $room['price']; ?>"
                                    data-room-capacity="<?php echo $room['max_people']; ?>">
                                    Select Room
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Room Booking Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <div class="form-group">
                                <label for="roomType">Room</label>
                                <input type="text" class="form-control" id="roomType" name="roomType" readonly>
                            </div>
                            <div class="form-group">
                                <label for="roomPrice">Price per Night</label>
                                <input type="text" class="form-control" id="roomPrice" name="roomPrice" readonly>
                            </div>
                            <div class="form-group">
                                <label for="checkInDate">Check-In Date</label>
                                <input type="date" class="form-control" id="checkInDate" name="checkInDate" required>
                            </div>
                            <div class="form-group">
                                <label for="checkOutDate">Check-Out Date</label>
                                <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog"
            aria-labelledby="searchResultsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchResultsModalLabel">Room Booking Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBodyContent">
                        <form id="multiBookingForm">
                            <div class="form-group">
                                <label for="modalStartDate">Start Date</label>
                                <input type="date" class="form-control" id="modalStartDate" name="modalStartDate"
                                    readonly>
                            </div>
                            <div class="form-group">
                                <label for="modalEndDate">End Date</label>
                                <input type="date" class="form-control" id="modalEndDate" name="modalEndDate" readonly>
                            </div>
                            <div class="form-group">
                                <label for="modalAdults">Adults</label>
                                <input type="number" class="form-control" id="modalAdults" name="modalAdults" readonly>
                            </div>
                            <div class="form-group">
                                <label for="modalChildren">Children</label>
                                <input type="number" class="form-control" id="modalChildren" name="modalChildren"
                                    readonly>
                            </div>
                            <div id="selectedRoomsList" class="mb-3"></div>
                            <button type="submit" class="btn btn-primary">Book All</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>