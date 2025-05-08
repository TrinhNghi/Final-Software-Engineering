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
        .gradient-bg {
            background: #A87E62;
            background: linear-gradient(90deg, rgba(168, 126, 98, 1) 0%, rgba(217, 183, 158, 1) 41%, rgba(221, 191, 168, 1) 65%, rgba(235, 223, 204, 1) 100%);
            width: 100%;
            height: 100vh;
        }

        .filter-box {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
<div class="container-fluid vh-100 gradient-bg">
    <div class="row h-100">
        <!-- Filter Section -->
        <div class="col-md-3 d-flex align-items-center">
            <div class="filter-box w-100">
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
                        <label for="adults">Adults</label>
                        <input type="number" class="form-control" id="adults" name="adults" placeholder="Number of Adults" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label for="children">Children</label>
                        <input type="number" class="form-control" id="children" name="children" placeholder="Number of Children" min="0" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </form>
            </div>
        </div>

        <!-- Room Listings Section -->
        <div class="col-md-9">
            <div class="container py-4">
                <h2 class="text-center text-white mb-4">Available Rooms</h2>
                <div class="row">
                    <!-- Example Room Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="images/room1.png" class="card-img-top" alt="Room Image">
                            <div class="card-body">
                                <h5 class="card-title">Single Room</h5>
                                <p class="card-text">$50 per night</p>
                                <a href="book.php?roomId=1" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Add more room cards dynamically here -->
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>