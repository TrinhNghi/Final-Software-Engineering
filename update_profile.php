<?php 
    session_start();
    require_once('db.php');
    $conn = open_database();
    $user_id = $_SESSION['user_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

    $sql = "UPDATE account 
            SET  
                firstname = ?, 
                lastname = ?, 
                email = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $firstname, $lastname, $email, $user_id);
    if ($stmt->execute()) {
        header('Location: userprofile.php');
        exit();
    }
    $stmt->close();
    $conn->close();
?>