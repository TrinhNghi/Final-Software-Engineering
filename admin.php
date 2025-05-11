<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hotelmanagement', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Initialize message
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_booking':
                    $user_id = (int)$_POST['user_id'];
                    $room_id = (int)$_POST['room_id'];
                    $checkin_date = $_POST['checkin_date'];
                    $checkout_date = $_POST['checkout_date'];
                    $status = $_POST['status'];
                    $payment_status = $_POST['payment_status'];

                    // Validate dates
                    if (strtotime($checkin_date) >= strtotime($checkout_date)) {
                        throw new Exception('Check-out date must be after check-in date.');
                    }

                    // Check room availability
                    $stmt = $pdo->prepare('
                        SELECT COUNT(*) FROM reservation
                        WHERE room_id = :room_id
                        AND status != "cancelled"
                        AND (
                            (:checkin_date BETWEEN checkin_date AND checkout_date)
                            OR (:checkout_date BETWEEN checkin_date AND checkout_date)
                            OR (checkin_date BETWEEN :checkin_date AND :checkout_date)
                        )
                    ');
                    $stmt->execute([
                        'room_id' => $room_id,
                        'checkin_date' => $checkin_date,
                        'checkout_date' => $checkout_date
                    ]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception('Room is not available for the selected dates.');
                    }

                    // Insert reservation (total_amount set by trigger)
                    $stmt = $pdo->prepare('
                        INSERT INTO reservation (user_id, room_id, checkin_date, checkout_date, status, payment_status)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ');
                    $stmt->execute([$user_id, $room_id, $checkin_date, $checkout_date, $status, $payment_status]);
                    $reservation_id = $pdo->lastInsertId();

                    // Handle payment if status is 'checked_out' and payment_status is 'paid'
                    if ($status === 'checked_out' && $payment_status === 'paid') {
                        $stmt = $pdo->prepare('SELECT total_amount FROM reservation WHERE id = ?');
                        $stmt->execute([$reservation_id]);
                        $amount = $stmt->fetchColumn();
                        $stmt = $pdo->prepare('SELECT balance FROM account WHERE id = ?');
                        $stmt->execute([$user_id]);
                        $balance = $stmt->fetchColumn();
                        if ($balance < $amount) {
                            // Rollback by deleting reservation
                            $pdo->prepare('DELETE FROM reservation WHERE id = ?')->execute([$reservation_id]);
                            throw new Exception('Insufficient balance for payment. Balance: ' . number_format($balance, 0) . ', Required: ' . number_format($amount, 0));
                        }
                        $stmt = $pdo->prepare('CALL ProcessPayment(?, ?, ?, ?)');
                        $stmt->execute([$user_id, $reservation_id, $amount, 'cash']);
                    }

                    $message = 'Booking added successfully.';
                    $message_type = 'success';
                    // Redirect to checkInOut if status is pending, else stay in manageBooking
                    $redirect_section = ($status === 'pending') ? 'checkInOut' : 'manageBooking';
                    header("Location: admin.php?section=$redirect_section");
                    exit();
                    break;

                case 'edit_booking':
                    $reservation_id = (int)$_POST['reservation_id'];
                    $room_id = (int)$_POST['room_id'];
                    $checkin_date = $_POST['checkin_date'];
                    $checkout_date = $_POST['checkout_date'];
                    $status = $_POST['status'];
                    $payment_status = $_POST['payment_status'];
                    $user_id = (int)$_POST['user_id'];

                    // Validate inputs
                    if (strtotime($checkin_date) >= strtotime($checkout_date)) {
                        throw new Exception('Check-out date must be after check-in date.');
                    }
                    if (!in_array($status, ['pending', 'checked_in', 'checked_out', 'cancelled'])) {
                        throw new Exception('Invalid status.');
                    }
                    if (!in_array($payment_status, ['unpaid', 'paid'])) {
                        throw new Exception('Invalid payment status.');
                    }
                    // Verify room_id exists
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM rooms WHERE id = ?');
                    $stmt->execute([$room_id]);
                    if ($stmt->fetchColumn() == 0) {
                        throw new Exception('Invalid room selected.');
                    }
                    // Verify user_id exists
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM account WHERE id = ?');
                    $stmt->execute([$user_id]);
                    if ($stmt->fetchColumn() == 0) {
                        throw new Exception('Invalid user selected.');
                    }

                    // Start transaction
                    $pdo->beginTransaction();

                    try {
                        // Handle payment for checked_out + paid
                        if ($status === 'checked_out' && $payment_status === 'paid') {
                            // Fetch current reservation and balance
                            $stmt = $pdo->prepare('
                                SELECT r.total_amount, r.payment_status, a.balance
                                FROM reservation r
                                JOIN account a ON r.user_id = a.id
                                WHERE r.id = ?
                            ');
                            $stmt->execute([$reservation_id]);
                            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

                            if (!$reservation) {
                                throw new Exception('Reservation or user account not found.');
                            }

                            // Process payment if not already paid
                            if ($reservation['payment_status'] !== 'paid') {
                                $amount = $reservation['total_amount'];
                                $balance = $reservation['balance'];

                                if ($balance < $amount) {
                                    throw new Exception(
                                        'Insufficient balance for payment. ' .
                                        'Balance: ' . number_format($balance, 0) . ', ' .
                                        'Required: ' . number_format($amount, 0)
                                    );
                                }

                                // Process payment
                                $stmt = $pdo->prepare('CALL ProcessPayment(?, ?, ?, ?)');
                                $stmt->execute([$user_id, $reservation_id, $amount, 'cash']);
                            }
                        }

                        // Update reservation
                        $stmt = $pdo->prepare('
                            UPDATE reservation
                            SET room_id = ?, checkin_date = ?, checkout_date = ?, status = ?, payment_status = ?
                            WHERE id = ?
                        ');
                        $stmt->execute([$room_id, $checkin_date, $checkout_date, $status, $payment_status, $reservation_id]);

                        // Commit transaction
                        $pdo->commit();
                        $message = 'Booking updated successfully.';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        // Rollback transaction
                        $pdo->rollBack();
                        throw new Exception('Failed to update booking: ' . $e->getMessage());
                    }
                    break;

                case 'cancel_booking':
                    $reservation_id = (int)$_POST['reservation_id'];
                    $stmt = $pdo->prepare('UPDATE reservation SET status = "cancelled" WHERE id = ?');
                    $stmt->execute([$reservation_id]);
                    $message = 'Booking cancelled successfully.';
                    $message_type = 'success';
                    break;

                case 'approve_request':
                    $request_id = (int)$_POST['request_id'];

                    // Start transaction
                    $pdo->beginTransaction();

                    try {
                        // Fetch request details
                        $stmt = $pdo->prepare('
                            SELECT r.service_id, r.reservation_id, s.price
                            FROM request r
                            LEFT JOIN service s ON r.service_id = s.id
                            WHERE r.id = ?
                        ');
                        $stmt->execute([$request_id]);
                        $request = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$request) {
                            throw new Exception('Request not found.');
                        }

                        // Update request status to approved
                        $stmt = $pdo->prepare('UPDATE request SET status = "approved" WHERE id = ?');
                        $stmt->execute([$request_id]);

                        // If there's a reservation_id and service price, update reservation total_amount
                        if ($request['reservation_id'] && $request['price']) {
                            $stmt = $pdo->prepare('
                                UPDATE reservation
                                SET total_amount = total_amount + ?
                                WHERE id = ?
                            ');
                            $stmt->execute([$request['price'], $request['reservation_id']]);
                        }

                        // Commit transaction
                        $pdo->commit();
                        $message = 'Request approved successfully.';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        // Rollback transaction
                        $pdo->rollBack();
                        throw new Exception('Failed to approve request: ' . $e->getMessage());
                    }
                    break;

                case 'reject_request':
                    $request_id = (int)$_POST['request_id'];
                    $stmt = $pdo->prepare('UPDATE request SET status = "rejected" WHERE id = ?');
                    $stmt->execute([$request_id]);
                    $message = 'Request rejected successfully.';
                    $message_type = 'success';
                    break;

                case 'check_in':
                    $reservation_id = (int)$_POST['reservation_id'];
                    $stmt = $pdo->prepare('UPDATE reservation SET status = "checked_in", payment_status = "unpaid" WHERE id = ?');
                    $stmt->execute([$reservation_id]);
                    $message = 'Check-in confirmed successfully.';
                    $message_type = 'success';
                    break;

                case 'check_out':
                    $reservation_id = (int)$_POST['reservation_id'];

                    // Start transaction
                    $pdo->beginTransaction();

                    try {
                        // Fetch reservation and balance
                        $stmt = $pdo->prepare('
                            SELECT r.total_amount, r.payment_status, r.user_id, a.balance
                            FROM reservation r
                            JOIN account a ON r.user_id = a.id
                            WHERE r.id = ?
                        ');
                        $stmt->execute([$reservation_id]);
                        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$reservation) {
                            throw new Exception('Reservation or user account not found.');
                        }

                        // If unpaid, process payment to deduct balance
                        if ($reservation['payment_status'] === 'unpaid') {
                            $amount = $reservation['total_amount'];
                            $user_id = $reservation['user_id'];
                            $balance = $reservation['balance'];

                            // Verify balance
                            if ($balance < $amount) {
                                throw new Exception(
                                    'Insufficient balance for check-out payment. ' .
                                    'Balance: ' . number_format($balance, 0) . ', ' .
                                    'Required: ' . number_format($amount, 0)
                                );
                            }

                            // Process payment
                            $stmt = $pdo->prepare('CALL ProcessPayment(?, ?, ?, ?)');
                            $stmt->execute([$user_id, $reservation_id, $amount, 'cash']);
                        }

                        // Update status to checked_out
                        $stmt = $pdo->prepare('UPDATE reservation SET status = "checked_out" WHERE id = ?');
                        $stmt->execute([$reservation_id]);

                        // Commit transaction
                        $pdo->commit();
                        $message = 'Check-out confirmed successfully.';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        // Rollback transaction
                        $pdo->rollBack();
                        throw $e;
                    }
                    break;

                case 'edit_staff':
                    $staff_id = (int)$_POST['staff_id'];
                    $firstname = trim($_POST['firstname']);
                    $lastname = trim($_POST['lastname']);
                    $email = trim($_POST['email']);
                    $phone = trim($_POST['phone']);
                    $role = $_POST['role'];

                    // Validate email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Invalid email format.');
                    }

                    // Check email uniqueness
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM staff WHERE email = ? AND id != ?');
                    $stmt->execute([$email, $staff_id]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception('Email is already in use.');
                    }

                    $stmt = $pdo->prepare('
                        UPDATE staff
                        SET firstname = ?, lastname = ?, email = ?, phone = ?, role = ?
                        WHERE id = ?
                    ');
                    $stmt->execute([$firstname, $lastname, $email, $phone, $role, $staff_id]);
                    $message = 'Staff updated successfully.';
                    $message_type = 'success';
                    break;

                case 'delete_staff':
                    $staff_id = (int)$_POST['staff_id'];
                    $stmt = $pdo->prepare('DELETE FROM staff WHERE id = ?');
                    $stmt->execute([$staff_id]);
                    $message = 'Staff deleted successfully.';
                    $message_type = 'success';
                    break;

                case 'send_service_request':
                    $service_id = (int)$_POST['service_id'];
                    $reservation_id = !empty($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : null;
                    $request_details = trim($_POST['request_details']);
                    $user_id = 1; // Admin user_id (admin123)

                    // Validate reservation_id if provided
                    if ($reservation_id) {
                        $stmt = $pdo->prepare('SELECT status FROM reservation WHERE id = ?');
                        $stmt->execute([$reservation_id]);
                        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$reservation || $reservation['status'] !== 'checked_in') {
                            throw new Exception('Selected reservation is not checked in.');
                        }
                    }

                    // Insert service request
                    $stmt = $pdo->prepare('
                        INSERT INTO request (user_id, service_id, reservation_id, request_type, request_details, status)
                        VALUES (?, ?, ?, "service", ?, "pending")
                    ');
                    $stmt->execute([$user_id, $service_id, $reservation_id, $request_details]);
                    $message = 'Service request sent successfully.';
                    $message_type = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }

    // Redirect to current section (unless overridden in add_booking)
    $section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
    header("Location: admin.php?section=$section");
    exit();
}

// Function to fetch bookings
function getBookings($pdo) {
    try {
        $stmt = $pdo->query('
            SELECT r.id, a.id AS user_id, a.firstname, a.lastname, ro.id AS room_id, ro.room_number, ro.room_name, r.checkin_date, r.checkout_date, r.total_amount, r.status, r.payment_status
            FROM reservation r
            JOIN account a ON r.user_id = a.id
            JOIN rooms ro ON r.room_id = ro.id
            ORDER BY r.id ASC
        ');
        $rows = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Disable Edit if status is 'checked_out' AND payment_status is 'paid' OR status is 'cancelled'
            $editDisabled = ($row['status'] === 'cancelled' || ($row['status'] === 'checked_out' && $row['payment_status'] === 'paid')) ? 'disabled' : '';
            $cancelDisabled = $row['status'] === 'cancelled' ? 'disabled' : '';
            $rows .= '
                <tr>
                    <td>#' . $row['id'] . '</td>
                    <td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>
                    <td>' . $row['room_number'] . ' (' . htmlspecialchars($row['room_name']) . ')</td>
                    <td>' . $row['checkin_date'] . '</td>
                    <td>' . $row['checkout_date'] . '</td>
                    <td>' . number_format($row['total_amount'], 0) . '</td>
                    <td>' . ucfirst($row['status']) . '</td>
                    <td>' . ucfirst($row['payment_status']) . '</td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return showEditBookingModal(' . $row['id'] . ', \'' . $row['room_id'] . '\', \'' . $row['checkin_date'] . '\', \'' . $row['checkout_date'] . '\', \'' . $row['status'] . '\', \'' . $row['payment_status'] . '\', ' . $row['user_id'] . ');">
                            <input type="hidden" name="action" value="edit_booking">
                            <input type="hidden" name="reservation_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-sm btn-warning" ' . $editDisabled . '>Edit</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to cancel this booking?\');">
                            <input type="hidden" name="action" value="cancel_booking">
                            <input type="hidden" name="reservation_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-sm btn-danger" ' . $cancelDisabled . '>Cancel</button>
                        </form>
                    </td>
                </tr>
            ';
        }
        return $rows ?: '<tr><td colspan="9">No bookings found.</td></tr>';
    } catch (PDOException $e) {
        return '<tr><td colspan="9">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

// Function to fetch service requests
function getServiceRequests($pdo) {
    try {
        $stmt = $pdo->query('
            SELECT req.id, a.firstname, a.lastname, ro.room_number, s.service_name, s.price, req.request_type, req.request_details, req.status, r.id AS reservation_id
            FROM request req
            JOIN account a ON req.user_id = a.id
            LEFT JOIN service s ON req.service_id = s.id
            LEFT JOIN reservation r ON req.reservation_id = r.id
            LEFT JOIN rooms ro ON r.room_id = ro.id
            ORDER BY req.id
        ');
        $rows = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $serviceDisplay = $row['service_name']
                ? htmlspecialchars($row['service_name']) . ' (' . number_format($row['price'], 0) . ')'
                : ucfirst($row['request_type']);
            $badgeClass = $row['status'] === 'pending' ? 'badge-warning' : (
                $row['status'] === 'approved' ? 'badge-success' : (
                    $row['status'] === 'rejected' ? 'badge-danger' : (
                        $row['status'] === 'completed' ? 'badge-primary' : (
                            $row['status'] === 'cancelled' ? 'badge-info' : 'badge-secondary'
                        )
                    )
                )
            );
            $buttonDisabled = in_array($row['status'], ['approved', 'completed', 'rejected', 'cancelled']) ? 'disabled' : '';
            $rows .= '
                <tr>
                    <td>#' . $row['id'] . '</td>
                    <td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>
                    <td>' . ($row['room_number'] ?: 'N/A') . '</td>
                    <td>' . $serviceDisplay . '</td>
                    <td>' . ($row['reservation_id'] ? '#' . $row['reservation_id'] : 'N/A') . '</td>
                    <td>' . htmlspecialchars($row['request_details'] ?: 'N/A') . '</td>
                    <td><span class="badge ' . $badgeClass . '">' . ($row['status'] ? ucfirst($row['status']) : 'Unknown') . '</span></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="approve_request">
                            <input type="hidden" name="request_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-success btn-sm ' . $buttonDisabled . '">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="reject_request">
                            <input type="hidden" name="request_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-danger btn-sm ' . $buttonDisabled . '">Reject</button>
                        </form>
                    </td>
                </tr>
            ';
        }
        return $rows ?: '<tr><td colspan="8">No service requests found.</td></tr>';
    } catch (PDOException $e) {
        return '<tr><td colspan="8">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

// Function to fetch staff
function getStaff($pdo) {
    try {
        $stmt = $pdo->query('
            SELECT id, firstname, lastname, email, phone, role, created_at
            FROM staff
            ORDER BY created_at
        ');
        $rows = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows .= '
                <tr>
                    <td>#' . $row['id'] . '</td>
                    <td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                    <td>' . ($row['phone'] ?: 'N/A') . '</td>
                    <td>' . ucfirst($row['role']) . '</td>
                    <td>' . $row['created_at'] . '</td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return showEditStaffModal(' . $row['id'] . ', \'' . htmlspecialchars($row['firstname']) . '\', \'' . htmlspecialchars($row['lastname']) . '\', \'' . htmlspecialchars($row['email']) . '\', \'' . ($row['phone'] ?: '') . '\', \'' . $row['role'] . '\');">
                            <input type="hidden" name="action" value="edit_staff">
                            <input type="hidden" name="staff_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this staff member?\');">
                            <input type="hidden" name="action" value="delete_staff">
                            <input type="hidden" name="staff_id" value="' . $row['id'] . '">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            ';
        }
        return $rows ?: '<tr><td colspan="7">No staff found.</td></tr>';
    } catch (PDOException $e) {
        return '<tr><td colspan="7">Error: ' . $e->getMessage() . '</td></tr>';
    }
}

// Function to fetch reports
function getReports($pdo) {
    try {
        $stmt = $pdo->query('
            SELECT mrd.month, r.room_number, r.room_name, 
                   mrd.total_checkin AS bookings, 
                   mrd.total_money AS total_revenue
            FROM monthly_room_data mrd
            JOIN rooms r ON mrd.room_id = r.id
            WHERE mrd.month IS NOT NULL
            ORDER BY mrd.month DESC, r.room_number ASC
            LIMIT 50
        ');
        
        $report = '<h4>Monthly Venue Report</h4>';
        $current_month = '';
        $rows = '';
        $monthly_totals = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Start a new month section if month changes
            if ($current_month !== $row['month']) {
                if ($rows) {
                    // Close previous month table
                    $report .= '
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Room Name</th>
                                    <th>Bookings</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>' . $rows . '
                                <tr>
                                    <td colspan="2"><strong>Total for ' . $current_month . '</strong></td>
                                    <td><strong>' . ($monthly_totals[$current_month]['bookings'] ?? 0) . '</strong></td>
                                    <td><strong>' . number_format($monthly_totals[$current_month]['revenue'] ?? 0, 0) . '</strong></td>
                                </tr>
                            </tbody>
                        </table>';
                }
                $current_month = $row['month'];
                $rows = '';
                $report .= '<h5>Month: ' . $row['month'] . '</h5>';
            }
            
            // Add row data
            $rows .= '
                <tr>
                    <td>' . $row['room_number'] . '</td>
                    <td>' . htmlspecialchars($row['room_name']) . '</td>
                    <td>' . $row['bookings'] . '</td>
                    <td>' . number_format($row['total_revenue'], 0) . '</td>
                </tr>';
            
            // Update monthly totals
            $monthly_totals[$current_month]['bookings'] = ($monthly_totals[$current_month]['bookings'] ?? 0) + $row['bookings'];
            $monthly_totals[$current_month]['revenue'] = ($monthly_totals[$current_month]['revenue'] ?? 0) + $row['total_revenue'];
        }
        
        // Add final month table
        if ($rows) {
            $report .= '
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Room Name</th>
                            <th>Bookings</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>' . $rows . '
                        <tr>
                            <td colspan="2"><strong>Total for ' . $current_month . '</strong></td>
                            <td><strong>' . ($monthly_totals[$current_month]['bookings'] ?? 0) . '</strong></td>
                            <td><strong>' . number_format($monthly_totals[$current_month]['revenue'] ?? 0, 0) . '</strong></td>
                        </tr>
                    </tbody>
                </table>';
        }
        
        return $report ?: '<strong>No data available.</strong>';
    } catch (PDOException $e) {
        return '<strong>Error: ' . $e->getMessage() . '</strong>';
    }
}

// Function to fetch rooms for edit/add form
function getRoomsOptions($pdo, $selected_room_id) {
    try {
        $stmt = $pdo->query('SELECT id, room_number, room_name FROM rooms ORDER BY room_number');
        $options = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_room_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['room_number'] . ' - ' . htmlspecialchars($row['room_name']) . '</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Function to fetch users for add/edit booking
function getUsersOptions($pdo, $selected_user_id) {
    try {
        $stmt = $pdo->query('SELECT id, firstname, lastname FROM account ORDER BY lastname, firstname');
        $options = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_user_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Function to fetch services for service request
function getServicesOptions($pdo, $selected_service_id) {
    try {
        $stmt = $pdo->query('SELECT id, service_name, price FROM service ORDER BY service_name');
        $options = '';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_service_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['service_name']) . ' (' . number_format($row['price'], 0) . ')</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Function to fetch reservations for service request
function getReservationsOptions($pdo, $selected_reservation_id) {
    try {
        $stmt = $pdo->query('
            SELECT r.id, a.firstname, a.lastname, ro.room_name
            FROM reservation r
            JOIN account a ON r.user_id = a.id
            JOIN rooms ro ON r.room_id = ro.id
            WHERE r.status = "checked_in"
            ORDER BY r.id ASC
        ');
        $options = '<option value="">None</option>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = $row['id'] == $selected_reservation_id ? 'selected' : '';
            $options .= '<option value="' . $row['id'] . '" ' . $selected . '>#' . $row['id'] . ' - ' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . ' (' . htmlspecialchars($row['room_name']) . ')</option>';
        }
        return $options;
    } catch (PDOException $e) {
        return '<option>Error: ' . $e->getMessage() . '</option>';
    }
}

// Determine which section to display
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$content = '';

switch ($section) {
    case 'manageBooking':
        $content = '
            <h2>Manage Booking</h2>
            <p>View, edit, or cancel bookings.</p>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBookingModal">Add New Booking</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ' . getBookings($pdo) . '
                </tbody>
            </table>
        ';
        break;
    case 'manageStaff':
        $content = '
            <h2>Manage Staff</h2>
            <p>View, edit, or remove staff members.</p>
            <button class="btn btn-primary mb-3">Add New Staff</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ' . getStaff($pdo) . '
                </tbody>
            </table>
        ';
        break;
    case 'manageService':
        $content = '
            <h2>Manage Service Requests</h2>
            <p>View and respond to customer requests.</p>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Service/Type</th>
                        <th>Reservation ID</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ' . getServiceRequests($pdo) . '
                </tbody>
            </table>
        ';
        break;
    case 'checkInOut':
        $content = '
            <div class="row">
                <div class="col-md-6 p-1">
                    <div class="section-box px-4">
                        <h4 class="text-center mb-4">Pending Check-Ins</h4>
                        <div id="pendingCheckIns">
                            <!-- Pending check-ins will be displayed here dynamically -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6 p-1">
                    <div class="section-box px-4">
                        <h4 class="text-center mb-4">Waiting for Check-Out</h4>
                        <div id="waitingCheckOuts">
                            <!-- Rooms waiting for check-out will be displayed here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        ';
        break;
    case 'support':
        $content = '
            <h2>Send Service Request</h2>
            <p>Create a new service request for guests.</p>
            <form method="POST">
                <input type="hidden" name="action" value="send_service_request">
                <div class="form-group">
                    <label for="service_id">Service</label>
                    <select class="form-control" name="service_id" id="service_id" required>
                        ' . getServicesOptions($pdo, 0) . '
                    </select>
                </div>
                <div class="form-group">
                    <label for="reservation_id">Reservation (Optional)</label>
                    <select class="form-control" name="reservation_id" id="reservation_id">
                        ' . getReservationsOptions($pdo, 0) . '
                    </select>
                </div>
                <div class="form-group">
                    <label for="request_details">Details</label>
                    <textarea class="form-control" name="request_details" id="request_details" rows="4" placeholder="Enter request details"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Request</button>
            </form>
        ';
        break;
    case 'reports':
        $content = '
            <h2>Reports</h2>
            <p>View monthly venue performance by room.</p>
            <div class="mb-3">
                <label for="reportType">Choose Report Type:</label>
                <select class="form-control" id="reportType">
                    <option>Monthly Venue Performance</option>
                </select>
            </div>
            <button class="btn btn-primary mb-4">Generate Report</button>
            <div class="alert alert-info">
                ' . getReports($pdo) . '
            </div>
        ';
        break;
    default:
        $content = '
            <h2>Welcome, ' . htmlspecialchars($_SESSION['user']) . '!</h2>
            <p>Your role: ' . htmlspecialchars($_SESSION['role']) . '</p>
            <p>Select an option from the sidebar to manage the system.</p>
        ';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            background: #f8f9fa;
        }

        .navbar {
            background-color: #A87E62;
            color: white;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .navbar .navbar-brand:hover {
            color: #f8f9fa;
        }

        .navbar .nav-link {
            color: white;
        }

        .navbar .nav-link:hover {
            color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #6a1000;
            color: white;
            padding: 20px;
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            overflow-y: auto;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #5a0e00;
        }

        .content {
            margin-left: 270px;
            margin-top: 80px;
            padding: 20px;
        }

        .btn-primary {
            background-color: #6a1000;
            border-color: #6a1000;
        }

        .btn-primary:hover {
            background-color: #5a0e00;
            border-color: #5a0e00;
        }

        .badge-primary {
            background-color: #007bff;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .section-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="?section=dashboard">
                <i class="fa fa-home" style="font-size: 24px;"></i> Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Admin Menu</h4>
        <a href="?section=manageBooking"><i class="fa fa-calendar"></i> Manage Booking</a>
        <a href="?section=manageStaff"><i class="fa fa-users"></i> Manage Staff</a>
        <a href="?section=manageService"><i class="fa fa-cogs"></i> Manage Service Requests</a>
        <a href="?section=checkInOut"><i class="fa fa-sign-in"></i> Check-In/Check-Out</a>
        <a href="?section=support"><i class="fa fa-life-ring"></i> Support</a>
        <a href="?section=reports"><i class="fa fa-bar-chart"></i> Reports</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php endif; ?>
        <?php echo $content; ?>
    </div>

    <!-- Add Booking Modal -->
    <div class="modal fade" id="addBookingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" onsubmit="return validateBookingForm('add_checkin_date', 'add_checkout_date')">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Booking</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_booking">
                        <div class="form-group">
                            <label for="add_user_id">Guest</label>
                            <select class="form-control" name="user_id" id="add_user_id" required>
                                <?php echo getUsersOptions($pdo, 0); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_room_id">Room</label>
                            <select class="form-control" name="room_id" id="add_room_id" required>
                                <?php echo getRoomsOptions($pdo, 0); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_checkin_date">Check-In Date</label>
                            <input type="date" class="form-control" name="checkin_date" id="add_checkin_date" value="2025-05-11" required>
                        </div>
                        <div class="form-group">
                            <label for="add_checkout_date">Check-Out Date</label>
                            <input type="date" class="form-control" name="checkout_date" id="add_checkout_date" required>
                        </div>
                        <div class="form-group">
                            <label for="add_status">Status</label>
                            <select class="form-control" name="status" id="add_status">
                                <option value="pending">Pending</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_payment_status">Payment Status</label>
                            <select class="form-control" name="payment_status" id="add_payment_status">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div class="modal fade" id="editBookingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" onsubmit="return validateBookingForm('edit_checkin_date', 'edit_checkout_date')">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Booking</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_booking">
                        <input type="hidden" name="reservation_id" id="edit_reservation_id">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="form-group">
                            <label for="edit_room_id">Room</label>
                            <select class="form-control" name="room_id" id="edit_room_id">
                                <?php echo getRoomsOptions($pdo, 0); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_checkin_date">Check-In Date</label>
                            <input type="date" class="form-control" name="checkin_date" id="edit_checkin_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_checkout_date">Check-Out Date</label>
                            <input type="date" class="form-control" name="checkout_date" id="edit_checkout_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-control" name="status" id="edit_status">
                                <option value="pending">Pending</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_payment_status">Payment Status</label>
                            <select class="form-control" name="payment_status" id="edit_payment_status">
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Staff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_staff">
                        <input type="hidden" name="staff_id" id="edit_staff_id">
                        <div class="form-group">
                            <label for="edit_firstname">First Name</label>
                            <input type="text" class="form-control" name="firstname" id="edit_firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_lastname">Last Name</label>
                            <input type="text" class="form-control" name="lastname" id="edit_lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="edit_phone">
                        </div>
                        <div class="form-group">
                            <label for="edit_role">Role</label>
                            <select class="form-control" name="role" id="edit_role">
                                <option value="manager">Manager</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="housekeeping">Housekeeping</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update sidebar link highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const section = '<?php echo $section; ?>';
            const links = document.querySelectorAll('.sidebar a');
            links.forEach(link => {
                if (link.href.includes(`section=${section}`)) {
                    link.style.backgroundColor = '#5a0e00';
                }
            });

            // Populate check-in/check-out data if on checkInOut section
            if (section === 'checkInOut') {
                populateCheckInOut();
            }
        });

        // Show edit booking modal
        function showEditBookingModal(reservation_id, room_id, checkin_date, checkout_date, status, payment_status, user_id) {
            document.getElementById('edit_reservation_id').value = reservation_id;
            document.getElementById('edit_user_id').value = user_id;
            document.getElementById('edit_room_id').value = room_id;
            document.getElementById('edit_checkin_date').value = checkin_date;
            document.getElementById('edit_checkout_date').value = checkout_date;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_payment_status').value = payment_status;
            $('#editBookingModal').modal('show');
            return false;
        }

        // Show edit staff modal
        function showEditStaffModal(staff_id, firstname, lastname, email, phone, role) {
            document.getElementById('edit_staff_id').value = staff_id;
            document.getElementById('edit_firstname').value = firstname;
            document.getElementById('edit_lastname').value = lastname;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_role').value = role;
            $('#editStaffModal').modal('show');
            return false;
        }

        // Validate booking form dates
        function validateBookingForm(checkinId, checkoutId) {
            const checkinDate = document.getElementById(checkinId).value;
            const checkoutDate = document.getElementById(checkoutId).value;
            if (checkinDate && checkoutDate) {
                if (new Date(checkoutDate) <= new Date(checkinDate)) {
                    alert('Reminder: Check-out date must be after check-in date.');
                    return false;
                }
            }
            return true;
        }

        // Populate check-in and check-out data
        function populateCheckInOut() {
            $.ajax({
                url: 'check_in_out_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (!data || typeof data !== 'object') {
                        $('#pendingCheckIns').html('<p>Error: Invalid data received from server.</p>');
                        $('#waitingCheckOuts').html('<p>Error: Invalid data received from server.</p>');
                        return;
                    }
                    if (data.error) {
                        $('#pendingCheckIns').html('<p>Error: ' + data.error + '</p>');
                        $('#waitingCheckOuts').html('<p>Error: ' + data.error + '</p>');
                        return;
                    }

                    // Populate Pending Check-Ins
                    let checkInsHtml = '';
                    if (data.checkIns.length === 0) {
                        checkInsHtml = '<p>No pending check-ins.</p>';
                    } else {
                        data.checkIns.forEach(function(item) {
                            checkInsHtml += `
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Guest: ${item.firstname} ${item.lastname}</h5>
                                        <p class="card-text">Room: ${item.room_name} (${item.room_type.charAt(0).toUpperCase() + item.room_type.slice(1)})</p>
                                        <p class="card-text">Check-In Date: ${item.checkin_date}</p>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="check_in">
                                            <input type="hidden" name="reservation_id" value="${item.id}">
                                            <button type="submit" class="btn btn-primary btn-sm">Confirm Check-In</button>
                                        </form>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#pendingCheckIns').html(checkInsHtml);

                    // Populate Waiting for Check-Out
                    let checkOutsHtml = '';
                    if (data.checkOuts.length === 0) {
                        checkOutsHtml = '<p>No pending check-outs.</p>';
                    } else {
                        data.checkOuts.forEach(function(item) {
                            checkOutsHtml += `
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Guest: ${item.firstname} ${item.lastname}</h5>
                                        <p class="card-text">Room: ${item.room_name} (${item.room_type.charAt(0).toUpperCase() + item.room_type.slice(1)})</p>
                                        <p class="card-text">Check-Out Date: ${item.checkout_date}</p>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="check_out">
                                            <input type="hidden" name="reservation_id" value="${item.id}">
                                            <button type="submit" class="btn btn-primary btn-sm">Confirm Check-Out</button>
                                        </form>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#waitingCheckOuts').html(checkOutsHtml);
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Error: Failed to load data. ';
                    if (xhr.status) {
                        errorMessage += `Status: ${xhr.status} (${xhr.statusText}). `;
                    }
                    errorMessage += `Details: ${error}`;
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        responseText: xhr.responseText
                    });
                    $('#pendingCheckIns').html(`<p>${errorMessage}</p>`);
                    $('#waitingCheckOuts').html(`<p>${errorMessage}</p>`);
                }
            });
        }
    </script>
</body>
</html>