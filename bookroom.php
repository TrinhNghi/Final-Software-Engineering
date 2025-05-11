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

// Only get all rooms for initial display
$sql = "SELECT MIN(id) as id, room_name, room_category, room_type, floor, max_people, price, description
        FROM rooms
        GROUP BY room_name, room_category, room_type, floor, max_people, price, description";
$result = $conn->query($sql);
$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

// Pass only the initial rooms to JavaScript
echo '<script>';
echo 'var initial_rooms = ' . json_encode($rooms) . ';';
echo '</script>';
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

        .filter-box {
            background-color: white;
            margin-top: 20px;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 120px);
            /* Adjust height to account for navbar */
            overflow-y: auto;
            /* Allow scrolling inside the filter box if needed */
        }

        .display-section {
            height: calc(100vh - 80px);
            /* Adjust height to account for navbar */
            overflow-y: auto;
            /* Allow scrolling inside the display section */
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
                    <?php if (isset($_SESSION['name'])): ?>
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
    <div class="container-fluid gradient-bg" style="display: flex; padding-top: 80px;">
        <!-- Filter Section -->
        <div class="col-md-3 filter-box">
            <h4 class="text-center mb-4">Search & Filter</h4>
            <form method="GET" action="bookroom.php">
                <div class="form-group">
                    <label for="roomType">Room Type</label>
                    <select class="form-control" id="roomType" name="roomType">
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
                        <option value="0-50">$0 - $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-200">$100 - $200</option>
                        <option value="200+">$200+</option>
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

        <!-- Display Section -->
        <div class="col-md-9 display-section">
            <h2 class="text-center text-white mb-4">Available Rooms</h2>
            <div class="row">
                <!-- Example Room Card -->
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="images/room<?php echo ($room['id'] % 3) + 1; ?>.png" class="card-img-top"
                                alt="Room Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                                <p class="mb-1">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($room['room_category']); ?>
                                    &nbsp;|&nbsp;
                                    <strong>Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Max People:</strong> <?php echo htmlspecialchars($room['max_people']); ?>
                                    &nbsp;|&nbsp;
                                    <strong>Price:</strong> $<?php echo htmlspecialchars($room['price']); ?>/night
                                </p>
                                <p class="card-text mt-2">
                                    <?php echo nl2br(htmlspecialchars($room['description'])); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <a href="#" class="btn btn-primary book-btn" data-toggle="modal"
                                        data-target="#bookingModal" data-room-id="<?php echo $room['id']; ?>"
                                        data-room-type="<?php echo htmlspecialchars($room['room_type']); ?>"
                                        data-room-price="<?php echo htmlspecialchars($room['price']); ?>">
                                        Book Now
                                    </a>
                                    <?php if (strtolower($room['room_type']) === 'vip'): ?>
                                        <span class="badge bg-success">VIP</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>


            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Room Booking Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <input type="hidden" id="roomId" name="roomId">
                            <div class="form-group">
                                <label for="roomType">Room Type</label>
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

        <!-- Search Results Modal -->
        <div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog"
            aria-labelledby="searchResultsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchResultsModalLabel">Room Booking Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBodyContent">
                        <form id="bookingForm">
                            <input type="hidden" id="roomId" name="roomId">
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
                            <button type="submit" class="btn btn-primary">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get today's date in YYYY-MM-DD format
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const dd = String(today.getDate()).padStart(2, '0');
        const todayDate = `${yyyy}-${mm}-${dd}`;

        // Calculate the max date for the end date (1 year from today)
        const nextYear = new Date(today);
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        const maxEndDate = `${nextYear.getFullYear()}-${String(nextYear.getMonth() + 1).padStart(2, '0')}-${String(nextYear.getDate()).padStart(2, '0')}`;

        // Set constraints on the start date and end date inputs
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        startDateInput.setAttribute('min', todayDate); // Start date must be greater than or equal to today
        endDateInput.setAttribute('max', maxEndDate); // End date must be within 1 year from today

        // Update the end date's min and max attributes dynamically based on the selected start date
        startDateInput.addEventListener('change', function () {
            const selectedStartDate = new Date(startDateInput.value);

            // Ensure the end date is at least 1 day after the start date
            const nextDay = new Date(selectedStartDate);
            nextDay.setDate(nextDay.getDate() + 1);
            const nextDayFormatted = `${nextDay.getFullYear()}-${String(nextDay.getMonth() + 1).padStart(2, '0')}-${String(nextDay.getDate()).padStart(2, '0')}`;

            // Update the min and max for the end date
            endDateInput.setAttribute('min', nextDayFormatted); // End date must be at least 1 day after the start date
            endDateInput.setAttribute('max', maxEndDate); // End date must still be within 1 year from today

            // If the current end date is invalid, reset it
            if (new Date(endDateInput.value) < nextDay || new Date(endDateInput.value) > nextYear) {
                endDateInput.value = '';
            }
        });

        $('form').on('submit', function (e) {
            e.preventDefault();
            const adults = parseInt($('#adults').val()) || 1;  // Default to 1 if NaN
            const children = parseInt($('#children').val()) || 0;  // Default to 0 if NaN
            const totalPeople = adults + children;
            // Get form data
            const formData = {
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
                roomType: $('#roomType').val(),
                priceRange: $('#priceRange').val()
            };

            // Show loading state
            $('.display-section .row').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>');

            // Fetch available rooms via AJAX
            $.ajax({
                url: 'get_available_rooms.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        showError(response.error);
                        return;
                    }

                    // Process the available rooms
                    const available_rooms = response;
                    // Debug: Log available rooms to verify data
                    console.log('Available rooms:', available_rooms);

                    // Filter rooms that can accommodate all people at once
                    const suitableRooms = available_rooms.filter(room => {
                        // Ensure room.max_people is a number
                        const maxPeople = parseInt(room.max_people) || 0;
                        return maxPeople >= totalPeople;
                    });

                    if (suitableRooms.length > 0) {
                        // Display suitable rooms on the right panel
                        let roomHtml = '';
                        suitableRooms.forEach(room => {
                            // Debug: Log each room's data
                            console.log('Rendering room:', room);

                            // Ensure we have valid data
                            if (!room.id || !room.room_name) {
                                console.error('Invalid room data:', room);
                                return;  // Skip invalid rooms
                            }

                            roomHtml += `
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="images/room${(room.id % 3) + 1}.png" class="card-img-top" alt="Room Image">
                            <div class="card-body">
                                <h5 class="card-title">${room.room_name || 'Unnamed Room'}</h5>
                                <p class="mb-1">
                                    <strong>Category:</strong> ${room.room_category || 'N/A'} &nbsp;|&nbsp;
                                    <strong>Type:</strong> ${room.room_type || 'N/A'}
                                </p>
                                <p class="mb-1">
                                    <strong>Max People:</strong> ${room.max_people || 'N/A'} &nbsp;|&nbsp;
                                    <strong>Price:</strong> $${room.price ? parseFloat(room.price).toFixed(2) : '0.00'}/night
                                </p>
                                <p class="card-text mt-2">
                                    ${room.description || 'No description available.'}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <a href="#" class="btn btn-primary book-btn" data-toggle="modal"
                                            data-target="#bookingModal" 
                                            data-room-id="${room.id}"
                                            data-room-type="${room.room_type}"
                                            data-room-price="${room.price}">
                                            Book Now
                                        </a>
                                    ${room.room_type && room.room_type.toLowerCase() === 'vip' ? '<span class="badge bg-success">VIP</span>' : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                        });

                        if (roomHtml === '') {
                            console.error('No valid rooms to display');
                            $('.display-section .row').html('<div class="col-12"><div class="alert alert-warning">No valid rooms found</div></div>');
                        } else {
                            $('.display-section .row').html(roomHtml);
                        }
                    } else {
                         $('.display-section .row').html('<div class="col-12 text-center h2 text-white">Available options has been displayed inside modal.</div>');
                        // Create copies of the available rooms array for our combinations
                        const roomsCopy = [...available_rooms];

                        // Cheapest combination logic (sorted by price)
                        const cheapestCombinations = findBestCombination([...roomsCopy].sort((a, b) => a.price - b.price), totalPeople);

                        // Normal combination logic (sorted by capacity)
                        const normalCombinations = findBestCombination([...roomsCopy].sort((a, b) => b.max_people - a.max_people), totalPeople);

                        // Generate HTML for both options
                        let modalHtml = '<h5>Cheapest Combination</h5><ul class="list-group">';
                        let cheapestTotalCost = 0;

                        cheapestCombinations.forEach((room, index) => {
                            modalHtml += `<li class="list-group-item">Room ${index + 1}: ${room.room_name} (Capacity: ${room.max_people}) - $${room.price.toFixed(2)}</li>`;
                            cheapestTotalCost += room.price;
                        });

                        modalHtml += `</ul><p class="mt-3"><strong>Total Estimated Cost:</strong> $${cheapestTotalCost.toFixed(2)}</p>`;
                        modalHtml += `<button class="btn btn-success mt-2" onclick='bookRooms(${JSON.stringify(cheapestCombinations)})'>Book Cheapest</button>`;

                        // Create HTML for Normal Combination
                        modalHtml += '<h5 class="mt-4">Normal Combination</h5><ul class="list-group">';
                        let normalTotalCost = 0;

                        normalCombinations.forEach((room, index) => {
                            modalHtml += `<li class="list-group-item">Room ${index + 1}: ${room.room_name} (Capacity: ${room.max_people}) - $${room.price.toFixed(2)}</li>`;
                            normalTotalCost += room.price;
                        });

                        modalHtml += `</ul><p class="mt-3"><strong>Total Estimated Cost:</strong> $${normalTotalCost.toFixed(2)}</p>`;
                        modalHtml += `<button class="btn btn-primary mt-2" onclick='bookRooms(${JSON.stringify(normalCombinations)})'>Book Normal</button>`;

                        // Show in modal
                        $('#modalBodyContent').html(modalHtml);
                        $('#searchResultsModal').modal('show');
                    }
                },
                error: function (xhr, status, error) {
                    let errorMsg = 'Error loading available rooms';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    showError(errorMsg);
                }
            });
        });

        function showError(message) {
            $('.display-section .row').html(
                `<div class="col-12"><div class="alert alert-danger">${message}</div></div>`
            );
            console.error('Error:', message);
        }

        // Helper function to find the best room combination
        function findBestCombination(sortedRooms, remainingPeople) {
            const combination = [];
            let rooms = [...sortedRooms]; // Create a copy to work with

            while (remainingPeople > 0 && rooms.length > 0) {
                // Find the best fitting room (largest capacity that fits remaining people)
                let roomIndex = rooms.findIndex(r => r.max_people <= remainingPeople);
                if (roomIndex === -1) {
                    // If no room fits perfectly, take the largest remaining room
                    roomIndex = rooms.length - 1;
                }

                const selectedRoom = rooms[roomIndex];
                combination.push({
                    id: selectedRoom.id,
                    room_name: selectedRoom.room_name,
                    max_people: selectedRoom.max_people,
                    price: parseFloat(selectedRoom.price),
                    room_type: selectedRoom.room_type
                });

                remainingPeople -= selectedRoom.max_people;

                // Remove the used room from consideration
                rooms.splice(roomIndex, 1);
            }

            return combination;
        }


        // Populate modal with room details and pre-filled dates
        $('#bookingModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var roomId = button.data('room-id');
            var roomType = button.data('room-type');
            var roomPrice = button.data('room-price');
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            var modal = $(this);
            modal.find('#roomId').val(roomId); // Set the room ID
            modal.find('#roomType').val(roomType);
            modal.find('#roomPrice').val('$' + roomPrice);
            modal.find('#checkInDate').val(startDate);
            modal.find('#checkOutDate').val(endDate);
        });

        // Handle booking form submission
        $('#bookingForm').on('submit', function (e) {
            e.preventDefault();

            const roomId = $('#roomId').val();
            const checkInDate = $('#checkInDate').val();
            const checkOutDate = $('#checkOutDate').val();

            if (!checkInDate || !checkOutDate) {
                alert('Please select both check-in and check-out dates.');
                return;
            }

            bookRoom(roomId, checkInDate, checkOutDate);
            $('#bookingModal').modal('hide');
        });

        function bookRoom(roomId, checkInDate, checkOutDate) {
            fetch('book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_ids: [roomId],
                    checkin: checkInDate,
                    checkout: checkOutDate
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Booking confirmed! Reservation ID: ' + data.reservations[0].reservation_id);
                        $('#bookingModal').modal('hide');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Booking error:', error);
                    alert('There was an issue with your booking.');
                });
        }

        function bookRooms(rooms) {
            const roomIds = rooms.map(room => room.id);
            const checkin = document.getElementById('startDate').value;
            const checkout = document.getElementById('endDate').value;

            if (!checkin || !checkout) {
                alert("Please select check-in and check-out dates.");
                return;
            }

            fetch('book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_ids: roomIds,
                    checkin: checkin,
                    checkout: checkout
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Bookings confirmed! Total cost: $' + data.total_cost);
                        $('#searchResultsModal').modal('hide');
                        // Optionally refresh the page or update UI
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error booking rooms:', error);
                    alert('Failed to book rooms.');
                });
        }
    </script>

</body>

</html>