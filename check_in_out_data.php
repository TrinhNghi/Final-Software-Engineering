<?php
header('Content-Type: application/json');
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotelmanagement', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch pending check-ins
    $stmt = $pdo->query('
        SELECT r.id, a.firstname, a.lastname, ro.room_name, ro.room_type, r.checkin_date
        FROM reservation r
        JOIN account a ON r.user_id = a.id
        JOIN rooms ro ON r.room_id = ro.id
        WHERE r.status = "pending"
    ');
    $checkIns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch waiting check-outs
    $stmt = $pdo->query('
        SELECT r.id, a.firstname, a.lastname, ro.room_name, ro.room_type, r.checkout_date
        FROM reservation r
        JOIN account a ON r.user_id = a.id
        JOIN rooms ro ON r.room_id = ro.id
        WHERE r.status = "checked_out" and r.payment_status = "unpaid"
    ');
    $checkOuts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['checkIns' => $checkIns, 'checkOuts' => $checkOuts]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>