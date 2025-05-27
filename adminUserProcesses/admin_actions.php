<?php
session_start();
require_once('../config.php');

//this part is for deletion or cancellation or make item status already claimed of approved and rejected claims
    //delete claim
    if (isset($_POST['delete_claim'])) {
        $claim_id = intval($_POST['claim_id']);
        $conn->query("DELETE FROM claims WHERE claim_id = $claim_id");

        $admin_id = $_SESSION['user_id'];
        $action = "Deleted a claim";
        $conn->query("INSERT INTO admin_logs (admin_id, action) VALUES ($admin_id, '$action')");

        header("Location: ../webPages/adminMenu.php#requestClaim");
        exit;
    }
    //ginhimonga pending liwat
    if (isset($_POST['revert_claim'])) {
        $claim_id = intval($_POST['claim_id']);
        $conn->query("UPDATE claims SET status = 'pending' WHERE claim_id = $claim_id");

        $admin_id = $_SESSION['user_id'];
        $action = "Reverted claim to pending";
        $conn->query("INSERT INTO admin_logs (admin_id, action) VALUES ($admin_id, '$action')");

        header("Location: ../webPages/adminMenu.php#requestClaim");
        exit;
    }
    //make item status as claimed by the user who lost it
    if (isset($_POST['make_claimed'])) {
        $claim_id = intval($_POST['claim_id']);
        $sql = "SELECT items.item_id AS the_item_id FROM items
        JOIN claims ON items.item_id = claims.item_id
        WHERE claim_id = $claim_id";
        $result = $conn->query($sql);
        if($result && $result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                $item_id = $row['the_item_id'];
            }
        }
        $conn->query("UPDATE items SET status = 'claimed' WHERE item_id = $item_id");

        $admin_id = $_SESSION['user_id'];
        $action = "Marked item as claimed";
        $conn->query("INSERT INTO admin_logs (admin_id, action, target_item_id) VALUES ($admin_id, '$action', $item_id)");

        header("Location: ../webPages/adminMenu.php#requestClaim");
        exit;
    }


//this part is for verifying claims
//this is for the approved items
if (isset($_POST['approve'])) {
    $item_id= $_POST['claim_item_id'];
    $status = 'approved';

    $stmt = $conn->prepare("UPDATE claims SET status = ? WHERE item_id = ?");
    $stmt->bind_param("si", $status, $item_id);
    $stmt->execute();

    $action = "Approved a claim";
    $admin_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO admin_logs (admin_id, action, target_item_id) VALUES ($admin_id, '$action', $item_id)");


    header("Location: ../webPages/adminMenu.php#requestClaim");
}
//this is for the rejected items
if(isset($_POST['reject'])){
    $item_id = $_POST['claim_item_id'];
    $status = 'rejected';

    $stmt = $conn->prepare("UPDATE claims SET status = ? WHERE item_id = ?");
    $stmt->bind_param("si", $status, $item_id);
    $stmt->execute();

    $action = "Rejected a claim";
    $admin_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO admin_logs (admin_id, action, target_item_id) VALUES ($admin_id, '$action', $item_id)");

    header("Location: ../webPages/adminMenu.php#requestClaim");
}

//this one is when an admin wants to make someone an admin
if (isset($_POST['make_admin']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $action = "Promoted a user to admin";
    $admin_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO admin_logs (admin_id, action) VALUES ($admin_id, '$action')");


    header("Location: ../webPages/adminMenu.php");
}

//this part is for announcement action of the admin
if (isset($_POST['post_announcement'])) {
    $admin_name = trim($_POST['admin_name']);
    $content = $_POST['content'];

    $sql = "SELECT * FROM users
    WHERE username = '$admin_name' AND role = 'admin'";

    $result = $conn->query($sql);
    if($result && $result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $admin_id = $row['user_id'];
        }
    }
    else{
        echo "why not";
    }

    $action = "Announce to regular menu";

    $stmt = $conn->prepare("INSERT INTO admin_logs(admin_id, action, announcements) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $admin_id, $action, $content);

    if($stmt->execute()){
        $stmt->close();
        $conn->close();

        header("Location: ../webPages/adminMenu.php");
    }
    else{
        echo "didn\'t do anything";
    }

}

//this part is for the user deletion action of the admin
if (isset($_POST['delete_user_id'])) {
    $user_id = intval($_POST['delete_user_id']);

    // Delete messages
    $conn->query("DELETE FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id");

    // Delete claims
    $conn->query("DELETE FROM claims WHERE claimer_id = $user_id");

    // Delete items
    $conn->query("DELETE FROM items WHERE user_id = $user_id");

    // Delete admin_logs
    $conn->query("DELETE FROM admin_logs WHERE admin_id = $user_id");

    // Delete the user
    $conn->query("DELETE FROM users WHERE user_id = $user_id");


    $admin_id = $_SESSION['user_id'];
    $action = "Deleted user";
    $conn->query("INSERT INTO admin_logs (admin_id, action) VALUES ($admin_id, '$action')");


    //yo we go back
    header("Location: ../webPages/adminMenu.php#messages");
    exit();
}


//if admin wants to add item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['addItem'])) {
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

            $admin_id = $_SESSION['user_id'];
            $action = "Added new item";
            $new_item_id = $conn->insert_id;
            $conn->query("INSERT INTO admin_logs (admin_id, action, target_item_id) VALUES ($admin_id, '$action', $new_item_id)");
            $stmt->close();
            $conn->close();

            header("Location: ../webPages/adminMenu.php#items");
            exit();
        } else {
            echo "Error inserting item: " . $stmt->error;
        }
    } else {
        echo "Item name and description cannot be empty.";
    }
}

//if admin wants to update an item
if (isset($_POST['update_item'])) {
    $item_id = $_POST['update_item_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $status = $_POST['status'];


   if (!empty($_FILES['image']['name'])) {
        $targetDir = "../itemImages/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

        $query = "UPDATE items SET title=?, description=?, location=?, category=?, status=?, image_url=? WHERE item_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $title, $description, $location, $category, $status, $imagePath, $item_id);
    } else {
        $query = "UPDATE items SET title=?, description=?, location=?, category=?, status=? WHERE item_id=? ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $title, $description, $location, $category, $status, $item_id);
    }

    if ($stmt->execute()) {

        $admin_id = $_SESSION['user_id'];
        $action = "Updated item";
        $conn->query("INSERT INTO admin_logs (admin_id, action, target_item_id) VALUES ($admin_id, '$action', $item_id)");


        $stmt->close();
        $conn->close();

        header("Location: ../webPages/adminMenu.php#Items");
        exit;
    } else {
        echo "Error updating item: " . $stmt->error;
    }
}

//admin wants to send message
if(isset($_POST['send_message'])){
    $sender_id = $_POST['sender_user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message_content = trim($_POST['message_content']);
    $item_id = $_POST['user_item'];


    if($sender_id !== "" && $receiver_id !== "" && $message_content !== "" && $item_id !== "")
    {
        $stmt = $conn->prepare("INSERT INTO messages(content, sender_id, receiver_id, item_id) values(?, ?, ?, ?)");
        $stmt->bind_param("siii", $message_content, $sender_id, $receiver_id, $item_id);

        if($stmt->execute())
        {
            $stmt->close();
            $conn->close();
            
            header("Location: ../webPages/adminMenu.php#messages");
            exit();
        }
        else{
            echo "Here we are again.";
        }
    }
}
else{
    echo "bruh, why outside";
}
?>
