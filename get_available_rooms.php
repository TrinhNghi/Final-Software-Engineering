<?php
session_start();
require_once('db.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');

$conn = open_database();
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get filter parameters from POST
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception('No input data received');
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Required fields
    $startDate = $data['startDate'] ?? null;
    $endDate = $data['endDate'] ?? null;
    $adults = (int)($data['adults'] ?? 1);
    $children = (int)($data['children'] ?? 0);
    $totalPeople = $adults + $children;

    if (!$startDate || !$endDate) {
        throw new Exception('Start date and end date are required');
    }

    // Optional filters
    $roomType = $data['roomType'] ?? '';
    $priceRange = $data['priceRange'] ?? '';

    // Build query
    $sql = "SELECT * FROM rooms WHERE max_people >= $totalPeople";

    // Room Type filter (using room_category from your DB)
if ($roomType && $roomType !== 'All') {
    $sql .= " AND room_category = '" . $conn->real_escape_string($roomType) . "'";
}

// Price Range filter (using price from your DB)
if ($priceRange) {
    // Remove dollar sign if present
    $priceRange = str_replace('$', '', $priceRange);
    
    if ($priceRange === "200+") {
        $sql .= " AND price >= 200";
    } else {
        [$min, $max] = explode('-', $priceRange);
        $sql .= " AND price BETWEEN " . (float)$min . " AND " . (float)$max;
    }
}
    // Convert dates to MySQL format (YYYY-MM-DD)
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = date('Y-m-d', strtotime($endDate));
    
    $sql .= " AND id NOT IN (
        SELECT room_id FROM reservation
        WHERE (
            ('$startDate' BETWEEN checkin_date AND checkout_date) OR
            ('$endDate' BETWEEN checkin_date AND checkout_date) OR
            (checkin_date BETWEEN '$startDate' AND '$endDate') OR
            (checkout_date BETWEEN '$startDate' AND '$endDate')
        )
        AND status != 'cancelled'
    )";

    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }

    $available_rooms = [];
    while ($row = $result->fetch_assoc()) {
        $available_rooms[] = $row;
    }

    echo json_encode($available_rooms);

} catch (Exception $e) {
    error_log('Error in get_available_rooms.php: ' . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>