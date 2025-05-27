<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $itemId = $_POST['delete_item_id'];
    $userId = $_POST['delete_item_user_id'];

    $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $userId);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
        
    } else {
        echo "stmt mo not executed";
    }
}
else{
    echo "indi POST";
}

?>