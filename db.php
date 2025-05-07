<?php
// Set UTC timezone early
date_default_timezone_set('UTC');

define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '');
define('DB', 'hotelmanagement');
define('PORT', 3306);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function open_database() {
    $conn = new mysqli(HOST, USER, PASS, DB, PORT);
    if ($conn->connect_error) {
        error_log("open_database: Connection failed: " . $conn->connect_error);
        die('Connect error: ' . $conn->connect_error);
    }
    return $conn;
}

function login($user, $pass) {
    $sql = "SELECT * FROM account WHERE username = ?";
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $user);
    if (!$stm->execute()) {
        error_log("login: Failed to execute query for user $user: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        error_log("login: User does not exist: $user");
        return array('code' => 1, 'error' => 'User does not exists!');
    }
    $data = $result->fetch_assoc();
    if (!password_verify($pass, $data['password'])) {
        error_log("login: Invalid password for user $user");
        return array('code' => 2, 'error' => 'Invalid password!');
    }
    if ($data['activated'] == 0) {
        error_log("login: Account not activated for user $user");
        return array('code' => 3, 'error' => 'This account is not activated!');
    }
    error_log("login: Successful for user $user");
    return array('code' => 0, 'error' => '', 'data' => $data);
}

function is_email_exists($email) {
    $sql = 'SELECT COUNT(*) as count FROM account WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("is_email_exists: Query error for email $email: " . $stm->error);
        die('Query error: ' . $stm->error);
    }

    $result = $stm->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function register($user, $pass, $first, $last, $email) {
    if (is_email_exists($email)) {
        error_log("register: Email already exists: $email");
        return array('code' => 1, 'error' => 'Email exists!');
    }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $rand = random_int(0, 1000);
    $token = md5($user . '+' . $rand);
    $sql = 'INSERT INTO account(username, firstname, lastname, email, password, activate_token) VALUES (?,?,?,?,?,?)';

    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('ssssss', $user, $first, $last, $email, $hash, $token);
    if (!$stm->execute()) {
        error_log("register: Failed to insert account for email $email: " . $stm->error);
        return array('code' => 2, 'error' => 'Can not execute command');
    }
    error_log("register: Account created for email $email with token $token");
    return array('code' => 0, 'error' => 'Create account successful!', 'token' => $token);
}

function send_activation_email($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'guidervirus7486@gmail.com';
        $mail->Password = 'btnladyuxqdbjmdu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('guidervirus7486@gmail.com', 'Administrator');
        $mail->addAddress($email, 'Receiver');

        $mail->isHTML(true);
        $mail->Subject = 'Verify account (activate your account!)';

        // Dynamically construct the base URL
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

        // Construct the activation link
        $relative_link = "/activate.php?email=" . urlencode($email) . "&token=" . urlencode($token);
        $full_url = $base_url . $relative_link;

        // Set email body
        $mail->Body = "Click <a href='$full_url'>here</a> to activate your account!";
        $mail->AltBody = "Copy and paste this link to activate your account: $full_url";

        $mail->send();
        error_log("send_activation_email: Email sent to $email with token $token");
        return true;
    } catch (Exception $e) {
        error_log("send_activation_email: Failed to send email to $email: {$mail->ErrorInfo}");
        return false;
    }
}

function send_reset_email($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'guidervirus7486@gmail.com';
        $mail->Password = 'btnladyuxqdbjmdu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('guidervirus7486@gmail.com', 'Administrator');
        $mail->addAddress($email, 'Receiver');

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';

        // Dynamically construct the base URL
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

        // Construct the reset password link
        $relative_link = "/reset_password.php?email=" . urlencode($email) . "&token=" . urlencode($token);
        $full_url = $base_url . $relative_link;

        // Set email body
        $mail->Body = "Click <a href='$full_url'>here</a> to reset your password. This link expires in 24 hours.";
        $mail->AltBody = "Copy and paste this link to reset your password: $full_url";

        $mail->send();
        error_log("send_reset_email: Email sent to $email with token $token");
        return true;
    } catch (Exception $e) {
        error_log("send_reset_email: Failed to send email to $email: {$mail->ErrorInfo}");
        return false;
    }
}

function active_account($email, $token) {
    $sql = 'SELECT username, activate_token, activated FROM account WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);

    if (!$stm->execute()) {
        error_log("active_account: Failed to execute SELECT query for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        error_log("active_account: No account found for email $email");
        return array('code' => 2, 'error' => 'Email address not found!');
    }

    $row = $result->fetch_assoc();
    if ($row['activate_token'] !== $token) {
        error_log("active_account: Token mismatch for email $email. URL token: $token, DB token: {$row['activate_token']}");
        return array('code' => 2, 'error' => 'Invalid token!');
    }
    if ($row['activated'] == 1) {
        error_log("active_account: Account already activated for email $email");
        return array('code' => 3, 'error' => 'Account already activated!');
    }

    $sql = "UPDATE account SET activated = 1, activate_token = '' WHERE email = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("active_account: Failed to execute UPDATE query for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }
    error_log("active_account: Account activated successfully for email $email");
    return array('code' => 0, 'error' => 'Account activated successfully!');
}

function generate_reset_token($email) {
    $email = strtolower($email);
    if (!is_email_exists($email)) {
        error_log("generate_reset_token: Email does not exist: $email");
        return array('code' => 1, 'error' => 'Email does not exist!');
    }

    $token = bin2hex(random_bytes(16)); // 32-character token
    $expire_on = time() + 3600 * 24; // 24-hour expiration
    $conn = open_database();

    // Clear existing tokens
    $sql = 'DELETE FROM reset_token WHERE email = ?';
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("generate_reset_token: Failed to clear old tokens for email $email: " . $stm->error);
    }

    // Insert new token
    $sql = 'INSERT INTO reset_token (email, token, expire_on) VALUES (?, ?, ?)';
    $stm = $conn->prepare($sql);
    $stm->bind_param('ssi', $email, $token, $expire_on);
    if (!$stm->execute()) {
        error_log("generate_reset_token: Failed to insert token for email $email: " . $stm->error);
        return array('code' => 2, 'error' => 'Failed to generate token!');
    }

    $success = send_reset_email($email, $token);
    error_log("generate_reset_token: Token generated for email $email, token: $token, expire_on: $expire_on, email_success: " . ($success ? 'true' : 'false'));
    return array('code' => 0, 'success' => $success);
}

function verify_reset_token($email, $token) {
    $email = strtolower($email);
    $sql = 'SELECT token, expire_on FROM reset_token WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("verify_reset_token: Failed to execute query for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Database error!');
    }

    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        error_log("verify_reset_token: No token found for email $email");
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }

    $row = $result->fetch_assoc();
    error_log("verify_reset_token: Found token for email $email: " . json_encode($row));
    if ($row['token'] !== $token) {
        error_log("verify_reset_token: Token mismatch for email $email. URL token: $token, DB token: {$row['token']}");
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }

    if ($row['expire_on'] === null || $row['expire_on'] < time()) {
        error_log("verify_reset_token: Token expired for email $email. expire_on: " . ($row['expire_on'] ?? 'NULL') . ", current time: " . time());
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }

    return array('code' => 0, 'error' => '');
}

function update_password($email, $pass, $token) {
    $email = strtolower($email);
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = 'UPDATE account SET password = ? WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('ss', $hash, $email);
    if (!$stm->execute()) {
        error_log("update_password: Failed to update password for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Failed to update password!');
    }

    $sql = 'DELETE FROM reset_token WHERE email = ?';
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("update_password: Failed to clear token for email $email: " . $stm->error);
    }

    error_log("update_password: Password updated successfully for email $email");
    return array('code' => 0, 'error' => '');
}
?>