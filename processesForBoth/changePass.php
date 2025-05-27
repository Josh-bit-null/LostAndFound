<?php
session_start();
require_once('../config.php');

if (isset($_POST['change_pass'])) {
    

    $current_pass = $_POST['current_pass'];
    $new1_pass = $_POST['new1_pass'];
    $new2_pass = $_POST['new2_pass'];

    $userId = $_POST['userid_changepass'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($hashed_password_from_db);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_pass, $hashed_password_from_db)) {
        echo "Current password is incorrect.";
    } elseif ($new1_pass !== $new2_pass) {
        echo"New passwords do not match.";
    } else {
        $new_hashed_password = password_hash($new1_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_hashed_password, $userId);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            echo "Failed to update password. Try again.";
        }
    }

    $conn->close();
}
?>