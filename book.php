<?php
session_start();
require_once('db.php');

$conn = open_database();

// Validate session and user ID
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in.'
    ]));
}

$user_id = (int)$_SESSION['user_id'];

// Get and validate input data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid input data'
    ]));
}

// Validate required fields
if (empty($data['room_ids']) || empty($data['checkin']) || empty($data['checkout'])) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]));
}

$room_ids = array_map('intval', (array)$data['room_ids']);
$checkin_date = $conn->real_escape_string($data['checkin']);
$checkout_date = $conn->real_escape_string($data['checkout']);

// Validate dates
try {
    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);
    if ($checkin >= $checkout) {
        throw new Exception('Checkout date must be after checkin date');
    }
    $days = $checkout->diff($checkin)->days;
} catch (Exception $e) {
    die(json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]));
}

// Verify user exists and is activated
$user_check = $conn->query("SELECT id, balance FROM account WHERE id = $user_id AND activated = 1");
if ($user_check->num_rows === 0) {
    die(json_encode([
        'status' => 'error',
        'message' => 'User account not found or not activated'
    ]));
}
$user = $user_check->fetch_assoc();

// Start transaction
$conn->begin_transaction();

try {
    $total_cost = 0;
    $reservations = [];
    
    foreach ($room_ids as $room_id) {
        // Get room details
        $room = $conn->query("SELECT id, price FROM rooms WHERE id = $room_id")->fetch_assoc();
        if (!$room) {
            throw new Exception("Room $room_id not found");
        }
        
        // Check room availability
        $available = $conn->query("
            SELECT id FROM reservation 
            WHERE room_id = $room_id 
            AND (
                (checkin_date <= '$checkin_date' AND checkout_date > '$checkin_date')
                OR (checkin_date < '$checkout_date' AND checkout_date >= '$checkout_date')
                OR (checkin_date >= '$checkin_date' AND checkout_date <= '$checkout_date')
            )
            AND status != 'cancelled'
        ")->num_rows === 0;
        
        if (!$available) {
            throw new Exception("Room $room_id is not available for the selected dates");
        }
        
        // Calculate room cost
        $room_cost = $days * $room['price'];
        $total_cost += $room_cost;
        
        // Create reservation
        $insert = $conn->query("
            INSERT INTO reservation (
                user_id, 
                room_id, 
                checkin_date, 
                checkout_date, 
                total_amount, 
                status, 
                payment_status
            ) VALUES (
                $user_id,
                $room_id,
                '$checkin_date',
                '$checkout_date',
                $room_cost,
                'pending',
                'unpaid'
            )
        ");
        
        if (!$insert) {
            throw new Exception("Failed to reserve room $room_id: " . $conn->error);
        }
        
        $reservations[] = [
            'room_id' => $room_id,
            'cost' => $room_cost,
            'reservation_id' => $conn->insert_id
        ];
    }
    
    // Check if user has sufficient balance (if you want to charge immediately)
    if ($user['balance'] < $total_cost) {
        throw new Exception("Insufficient account balance");
    }
    
    // Deduct from balance (optional - remove if you handle payment separately)
    $conn->query("UPDATE account SET balance = balance - $total_cost WHERE id = $user_id");
    
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Reservation successful',
        'total_cost' => $total_cost,
        'reservations' => $reservations
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>