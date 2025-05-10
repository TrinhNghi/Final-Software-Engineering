<?php
session_start();
require_once('db.php');

$conn = open_database();

// Validate session user ID
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Example selected room IDs (this would come from booking.php POST/GET ideally)
$id_request = array(1, 2, 3, 4);

// Example check-in and check-out dates (should come from user input via booking form)
$checkin_date = '2025-05-10';
$checkout_date = '2025-05-15';

// Validate date values
if (empty($checkin_date) || empty($checkout_date)) {
    die("Check-in and check-out dates are required.");
}

$datetime1 = new DateTime($checkin_date);
$datetime2 = new DateTime($checkout_date);
$interval = $datetime1->diff($datetime2);
$days = $interval->days;

if ($days <= 0) {
    die("Invalid check-in and check-out dates.");
}

// Transaction start
$conn->begin_transaction();

try {
    foreach ($id_request as $room_id) {
        // Check if room exists and get its price
        $stmt = $conn->prepare("SELECT price FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $stmt->close();

        if ($price === null) {
            throw new Exception("Room ID $room_id not found.");
        }

        // Calculate total amount
        $total_amount = $days * $price;

        // Insert reservation record
        $stmt = $conn->prepare("INSERT INTO reservation (user_id, room_id, checkin_date, checkout_date, total_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissd", $user_id, $room_id, $checkin_date, $checkout_date, $total_amount);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert reservation for Room ID $room_id: " . $stmt->error);
        }

        $stmt->close();

        echo "Reservation for Room $room_id created successfully. Total: $$total_amount<br>";
    }

    // Commit transaction if all successful
    $conn->commit();
    echo "<strong>All reservations processed successfully.</strong>";

} catch (Exception $e) {
    // Rollback on any failure
    $conn->rollback();
    echo "<strong>Error occurred:</strong> " . $e->getMessage();
}

// Close connection
$conn->close();
?>
