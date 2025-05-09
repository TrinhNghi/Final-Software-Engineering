<?php?>
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
        <!-- Request Service Form -->
        <div class="col-md-3 filter-box">
            <h4 class="text-center mb-4">Request a Service</h4>
            <form id="serviceForm">
                <div class="form-group">
                    <label for="serviceType">Service Type</label>
                    <select class="form-control" id="serviceType" name="serviceType" required>
                        <option value="">Select a Service</option>
                        <option value="room_cleaning">Room Cleaning</option>
                        <option value="food_delivery">Food Delivery</option>
                        <option value="laundry">Laundry</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="serviceDate">Service Date</label>
                    <input type="date" class="form-control" id="serviceDate" name="serviceDate" required>
                </div>
                <div class="form-group">
                    <label for="serviceTime">Service Time</label>
                    <input type="time" class="form-control" id="serviceTime" name="serviceTime" required>
                </div>
                <div class="form-group">
                    <label for="additionalNotes">Additional Notes</label>
                    <textarea class="form-control" id="additionalNotes" name="additionalNotes" rows="3" placeholder="Enter any additional details"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Request Service</button>
            </form>
        </div>

        <!-- Display Section -->
        <div class="col-md-9 display-section">
            <h2 class="text-center text-white mb-4">Requested Services</h2>
            <div class="row" id="requestedServices">
                <!-- Requested services will be displayed here dynamically -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Handle service form submission
            $('#serviceForm').on('submit', function (e) {
                e.preventDefault(); // Prevent form redirection

                // Get form values
                const serviceType = $('#serviceType').val();
                const serviceDate = $('#serviceDate').val();
                const serviceTime = $('#serviceTime').val();
                const additionalNotes = $('#additionalNotes').val();

                // Generate service card
                const serviceHtml = `
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${serviceType.replace('_', ' ')}</h5>
                                <p class="card-text"><strong>Date:</strong> ${serviceDate}</p>
                                <p class="card-text"><strong>Time:</strong> ${serviceTime}</p>
                                <p class="card-text"><strong>Notes:</strong> ${additionalNotes || 'None'}</p>
                            </div>
                        </div>
                    </div>
                `;

                // Append the service card to the display section
                $('#requestedServices').append(serviceHtml);

                // Reset the form
                $('#serviceForm')[0].reset();
            });
        });
    </script>
</body>
</html>
>