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
                            <a href="book.php?roomId=<?php echo $i; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
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
    </script>
</body>
</html>