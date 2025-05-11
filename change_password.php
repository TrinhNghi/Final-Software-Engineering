<?php 
    session_start();
    require_once('db.php');
    $conn = open_database();
    $user_id = $_SESSION['user_id'];
    $pass = $_POST['newPassword'];
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = 'UPDATE account SET password = ? WHERE id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hash, $user_id);
    if ($stmt->execute()) {
        header('Location: userprofile.php');
        exit();
    }
    $stmt->close();
    $conn->close();
?>