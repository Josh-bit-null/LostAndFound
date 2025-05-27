<?php
session_start();
require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfo'])) {
    $userId = $_POST['userid_update'];
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);

    $sql = "UPDATE users SET 
                first_name = '$firstName',
                last_name = '$lastName',
                email = '$email',
                username = '$username'
            WHERE user_id = $userId";

    if ($conn->query($sql) == TRUE) {
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
