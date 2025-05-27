<?php

session_start();
require_once('../config.php');

if(isset($_POST['send_message'])){
    $sender_id = $_POST['sender_user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message_content = trim($_POST['message_content']);

    if(isset($_POST['user_item']))
    {
        $item_id = $_POST['user_item'];
        if($sender_id !== "" && $receiver_id !== "" && $message_content !== "" && $item_id !== "")
        {
            $stmt = $conn->prepare("INSERT INTO messages(content, sender_id, receiver_id, item_id) values(?, ?, ?, ?)");
            $stmt->bind_param("siii", $message_content, $sender_id, $receiver_id, $item_id);

            if($stmt->execute())
            {
                $stmt->close();
                $conn->close();
                
                header("Location: ../webPages/regularMenu.php#messages");
                exit();
            }
            else{
                echo "Here we are again.";
            }
        }
    }
    else{
        if($sender_id !== "" && $receiver_id !== "" && $message_content !== "")
        {
            $stmt = $conn->prepare("INSERT INTO messages(content, sender_id, receiver_id) values(?, ?, ?)");
            $stmt->bind_param("sii", $message_content, $sender_id, $receiver_id);

            if($stmt->execute())
            {
                $stmt->close();
                $conn->close();
                
                header("Location: ../webPages/regularMenu.php#messages");
                exit();
            }
            else{
                echo "Here we are again.";
            }
        }
    }
    
}
else{
    echo "bruh, why outside";
}

?>