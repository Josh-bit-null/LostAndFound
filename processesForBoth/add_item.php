<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $category = trim($_POST['category']);
    $userId = $_SESSION['user_id'];

    $imagePath = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = '../itemImages/';
        $imagePath = $uploadDir . time() . '_' . $imageName;

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            die("Failed to upload image.");
        }
    }


    if ($itemName !== "" && $description !== "" && $location !== "" && $category !== "" && $imagePath !== "")
    {
        $stmt = $conn->prepare("INSERT INTO items (title, description, location, category, image_url, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $itemName, $description, $location, $category, $imagePath, $userId);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../webPages/regularMenu.php#Items");
            exit();
        } else {
            echo "Error inserting item: " . $stmt->error;
        }
    } else {
        echo "Item name and description cannot be empty.";
    }
}
?>