
<?php
define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '');
define('DB', 'hotelmanagement');
define('PORT', 3306);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

function open_database() {
    $conn = new mysqli(HOST, USER, PASS, DB, PORT);
    if ($conn->connect_error) {
        die('Connect error: ' . $conn->connect_error);
    }
    return $conn;
}

function login($user, $pass) {
    $sql = "select * from account where username = ?";
    $conn = open_database();

    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $user);
    if (!$stm->execute()) {
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    $result = $stm->get_result();

    if ($result->num_rows == 0) {
        return array('code' => 1, 'error' => 'User does not exists!');
    }
    $data = $result->fetch_assoc();
    $hashed_password = $data['password'];
    if (!password_verify($pass, $hashed_password)) {
        return array('code' => 2, 'error' => 'Invalid password!');
    } else if ($data['activated'] == 0) {
        return array('code' => 3, 'error' => 'This account is not activated!');
    } else {
        return array('code' => 0, 'error' => '', 'data' => $data);
    }
}

function is_email_exists($email) {
    $sql = 'SELECT COUNT(*) as count FROM account WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        die('Query error: ' . $stm->error);
    }

    $result = $stm->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        return true;
    } else {
        return false;
    }
}

function register($user, $pass, $first, $last, $email) {
    if (is_email_exists($email)) {
        return array('code' => 1, 'error' => 'Email exists!');
    }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $rand = random_int(0, 1000);
    $token = md5($user . '+' . $rand);
    $sql = 'insert into account(username, firstname, lastname, email, password, activate_token) values (?,?,?,?,?,?)';

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
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'guidervirus7486@gmail.com';
        $mail->Password = 'btnladyuxqdbjmdu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('guidervirus7486@gmail.com', 'Administrator');
        $mail->addAddress($email, 'Receiver');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify account (activate your account!)';
        $mail->Body = "Click <a href='http://localhost/activate.php?email=$email&token=$token'>here</a> to activate your account!";
        $mail->AltBody = "Copy and paste this link to activate your account: http://localhost/activate.php?email=$email&token=$token";

        $mail->send();
        error_log("Activation email sent to $email with token $token");
        return true;
    } catch (Exception $e) {
        error_log("Failed to send activation email to $email: {$mail->ErrorInfo}");
        return false;
    }
}

function send_reset_email($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'guidervirus7486@gmail.com';
        $mail->Password = 'btnladyuxqdbjmdu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('guidervirus7486@gmail.com', 'Administrator');
        $mail->addAddress($email, 'Receiver');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body = "Click <a href='http://localhost/reset_password.php?email=$email&token=$token'>here</a> to reset your password. This link expires in 24 hours.";
        $mail->AltBody = "Copy and paste this link to reset your password: http://localhost/reset_password.php?email=$email&token=$token";

        $mail->send();
        error_log("Reset email sent to $email with token $token");
        return true;
    } catch (Exception $e) {
        error_log("Failed to send reset email to $email: {$mail->ErrorInfo}");
        return false;
    }
}

function active_account($email, $token) {
    $sql = 'SELECT username, activate_token, activated FROM account WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);

    if (!$stm->execute()) {
        error_log("activeAccount: Failed to execute SELECT query for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        error_log("activeAccount: No account found for email $email");
        return array('code' => 2, 'error' => 'Email address not found!');
    }

    $row = $result->fetch_assoc();
    if ($row['activate_token'] !== $token) {
        error_log("activeAccount: Token mismatch for email $email. URL token: $token, DB token: {$row['activate_token']}");
        return array('code' => 2, 'error' => 'Invalid token!');
    }
    if ($row['activated'] == 1) {
        error_log("activeAccount: Account already activated for email $email");
        return array('code' => 3, 'error' => 'Account already activated!');
    }

    $sql = "UPDATE account SET activated = 1, activate_token = '' WHERE email = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("activeAccount: Failed to execute UPDATE query for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }
    error_log("activeAccount: Account activated successfully for email $email");
    return array('code' => 0, 'error' => 'Account activated successfully!');
}

function reset_password($email) {
    $email = strtolower($email); // Normalize email
    if (!is_email_exists($email)) {
        error_log("reset_password: Email does not exist: $email");
        return array('code' => 1, 'error' => 'Email does not exist!');
    }

    $token = bin2hex(random_bytes(16)); // Secure token
    $expire_on = time() + 3600 * 24; // 24-hour expiration
    $sql = 'INSERT INTO reset_token (email, token, expire_on) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = ?, expire_on = ?';

    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('ssiss', $email, $token, $expire_on, $token, $expire_on);
    if (!$stm->execute()) {
        error_log("reset_password: Failed to execute query for email $email: " . $stm->error);
        return array('code' => 2, 'error' => 'Can not execute command!');
    }

    $success = send_reset_email($email, $token);
    error_log("reset_password: Token updated for email $email, token: $token, expire_on: $expire_on, email_success: " . ($success ? 'true' : 'false'));
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
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        error_log("verify_reset_token: No token found for email $email");
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }

    $row = $result->fetch_assoc();
    if ($row['token'] !== $token) {
        error_log("verify_reset_token: Token mismatch for email $email. URL token: $token, DB token: {$row['token']}");
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }
    if ($row['expire_on'] < time()) {
        error_log("verify_reset_token: Token expired for email $email. expire_on: {$row['expire_on']}");
        return array('code' => 2, 'error' => 'Invalid or expired token!');
    }

    return array('code' => 0, 'error' => '');
}

function update_password($email, $pass, $token) {
    $email = strtolower($email);
    // Verify token
    $result = verify_reset_token($email, $token);
    if ($result['code'] != 0) {
        return $result;
    }

    // Update password
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = 'UPDATE account SET password = ? WHERE email = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);
    $stm->bind_param('ss', $hash, $email);
    if (!$stm->execute()) {
        error_log("update_password: Failed to update password for email $email: " . $stm->error);
        return array('code' => 1, 'error' => 'Can not execute command!');
    }

    // Clear reset token
    $sql = 'DELETE FROM reset_token WHERE email = ?';
    $stm = $conn->prepare($sql);
    $stm->bind_param('s', $email);
    if (!$stm->execute()) {
        error_log("update_password: Failed to clear reset token for email $email: " . $stm->error);
    }

    error_log("update_password: Password updated successfully for email $email");
    return array('code' => 0, 'error' => 'Password updated successfully!');
}
?>
