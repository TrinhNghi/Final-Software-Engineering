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
                    <label for="availability">Availability</label>
                    <select class="form-control" id="availability" name="availability">
                        <option value="">All</option>
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="adults">Adults</label>
                            <input type="number" class="form-control" id="adults" name="adults" placeholder="Number of Adults" min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label for="children">Children</label>
                            <input type="number" class="form-control" id="children" name="children" placeholder="Number of Children" min="0" value="0">
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
                <?php for ($i = 1; $i <= 20; $i++): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="images/room<?php echo ($i % 3) + 1; ?>.png" class="card-img-top" alt="Room Image">
                        <div class="card-body">
                            <h5 class="card-title">Room <?php echo $i; ?></h5>
                            <p class="card-text">$<?php echo 50 + ($i * 5); ?> per night</p>
                            <a href="#" 
                               class="btn btn-primary" 
                               data-toggle="modal" 
                               data-target="#bookingModal" 
                               data-room-id="${room.id}" 
                               data-room-type="${room.type}" 
                               data-room-price="${room.price}">
                               Book Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
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
        <div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchResultsModalLabel">Room Booking Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <div class="form-group">
                                <label for="modalStartDate">Start Date</label>
                                <input type="date" class="form-control" id="modalStartDate" name="modalStartDate" readonly>
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
                                <input type="number" class="form-control" id="modalChildren" name="modalChildren" readonly>
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

 $(document).ready(function () {
  $('form').on('submit', function (e) {
    e.preventDefault();

    const adults = parseInt($('#adults').val());
    const children = parseInt($('#children').val());
    const totalPeople = adults + children;

    const rooms = [
      { id: 1, type: 'Single', capacity: 1, price: 50 },
      { id: 2, type: 'Double', capacity: 2, price: 80 },
      { id: 3, type: 'Suite', capacity: 4, price: 150 }
    ];

    // Check if any room can accommodate all people
    const suitableRooms = rooms.filter(room => room.capacity >= totalPeople);

    if (suitableRooms.length > 0) {
      // Display suitable rooms on the right panel
      let roomHtml = '';
      suitableRooms.forEach(room => {
        roomHtml += `
          <div class="col-md-4 mb-4">
            <div class="card">
              <img src="images/room${room.id}.png" class="card-img-top" alt="${room.type}">
              <div class="card-body">
                <h5 class="card-title">${room.type} Room</h5>
                <p class="card-text">Capacity: ${room.capacity} | $${room.price} per night</p>
                <a href="#" 
   class="btn btn-primary" 
   data-toggle="modal" 
   data-target="#bookingModal" 
   data-room-id="${room.id}" 
   data-room-type="${room.type}" 
   data-room-price="${room.price}">
   Book Now
</a>
              </div>
            </div>
          </div>
        `;
      });
      $('.display-section .row').html(roomHtml);
    } else {
      // Use greedy combination approach and show modal
      const combinations = [];
      let remaining = totalPeople;
      const sortedRooms = rooms.slice().sort((a, b) => b.capacity - a.capacity);

      while (remaining > 0) {
        const room = sortedRooms.find(r => r.capacity <= remaining) || sortedRooms[sortedRooms.length - 1];
        combinations.push(room);
        remaining -= room.capacity;
      }

      let modalHtml = '<ul class="list-group">';
      let totalCost = 0;
      combinations.forEach((room, index) => {
        modalHtml += `<li class="list-group-item">Room ${index + 1}: ${room.type} (Capacity: ${room.capacity}) - $${room.price}</li>`;
        totalCost += room.price;
      });
      modalHtml += `</ul><p class="mt-3"><strong>Total Estimated Cost:</strong> $${totalCost}</p>`;

      $('#modalBodyContent').html(modalHtml);
      $('#bookingModal').modal('show');
    }
  });
});

    </script>
<script>
    // Populate modal with room details and pre-filled dates
    $('#bookingModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var roomId = button.data('room-id'); // Extract room ID
        var roomType = button.data('room-type'); // Extract room type
        var roomPrice = button.data('room-price'); // Extract room price

        // Get the selected dates from the search form
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        // Update the modal's content
        var modal = $(this);
        modal.find('#roomType').val(roomType);
        modal.find('#roomPrice').val('$' + roomPrice);
        modal.find('#checkInDate').val(startDate);
        modal.find('#checkOutDate').val(endDate);
    });

    // Handle booking form submission
    $('#bookingForm').on('submit', function (e) {
        e.preventDefault(); // Prevent form redirection
        alert('Booking confirmed for ' + $('#roomType').val() + ' at ' + $('#roomPrice').val() + ' per night.');
        $('#bookingModal').modal('hide'); // Close the modal after confirmation
    });
</script>
<script>
    $(document).ready(function () {
        // Handle search form submission
        $('#searchForm').on('submit', function (e) {
            e.preventDefault(); // Prevent form redirection

            // Get the form values
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const adults = $('#adults').val();
            const children = $('#children').val();

            // Populate the modal with the form values
            $('#modalStartDate').val(startDate);
            $('#modalEndDate').val(endDate);
            $('#modalAdults').val(adults);
            $('#modalChildren').val(children);

            // Show the modal
            $('#searchResultsModal').modal('show');
        });

        // Handle booking form submission
        $('#bookingForm').on('submit', function (e) {
            e.preventDefault(); // Prevent form redirection
            alert('Booking confirmed!');
            $('#searchResultsModal').modal('hide'); // Close the modal
        });
    });
</script>
</body>
</html>