<?php

session_start();
require_once('../config.php');

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request']))   
{
    $item_id = $_POST['claim_item_id'];
    $claimer_id = $_POST['claimer_id'];
    $status = 'pending';
    $claim_message = trim($_POST['claimMessage']);

    $stmt = $conn->prepare("SELECT * FROM claims WHERE item_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0)
    {
        $_SESSION['request_error'] = 'you are trying to claim an item that is being claimed or already claimed, please contact the admin, refer tonthe contact section if you really think that is yours';
        header("Location: ../webPages/regularMenu.php#requestClaim");
        exit();
    }
    else{
        $stmt = $conn->prepare("INSERT INTO claims (item_id, claimer_id, claim_message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $item_id, $claimer_id, $claim_message);


        if($stmt->execute())
        {
            $stmt->close();
            $conn->close();
            error_log("New claim registered: $item_id");
            header("Location: ../webPages/regularMenu.php#requestClaim");
            exit();
        }
        else{
            echo "Burh, what was that?";
        }
    }
}

?>