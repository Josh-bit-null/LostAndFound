<?php
session_start();
require_once('../config.php');

if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "itemImages/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

        $query = "UPDATE items SET title=?, description=?, location=?, category=?, status=?, image_url=? WHERE item_id=? AND user_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssii", $title, $description, $location, $category, $status, $imagePath, $item_id, $user_id);
    } else {
        $query = "UPDATE items SET title=?, description=?, location=?, category=?, status=? WHERE item_id=? AND user_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $title, $description, $location, $category, $status, $item_id, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: ../webPages/regularMenu.php");
        exit;
    } else {
        echo "Error updating item: " . $stmt->error;
    }
}
?>
