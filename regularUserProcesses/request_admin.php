<?php
session_start();
require_once('../config.php');

if (isset($_POST['apply_admin'])) {
    $userId = intval($_POST['user_id']);
    $message = $conn->real_escape_string(trim($_POST['request_message']));

    $checkSql = "SELECT * FROM admin_request WHERE user_id = $userId";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
        echo "already have pending bruv";
    } else {
        $sql = "INSERT INTO admin_request (user_id, message) VALUES ($userId, '$message')";
        if ($conn->query($sql) === TRUE) {
            $conn->close();
            header('Location: ../webPages/regularMenu.php');
            exit;
        } else {
            echo "nope, not happening";
        }
    }
}
?>
