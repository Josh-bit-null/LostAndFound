<?php
session_start();
require_once('config.php');


    if (isset($_POST['register'])) {
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";

        $fName = $_POST['fName'] ?? '';
        $lName = $_POST['lName'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $password_raw = $_POST['password'] ?? '';
        $secondPassword = $_POST['secondPassword'] ?? '';
        $creationDate = date('Y-m-d');
        if($secondPassword == $password_raw)
        {
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $_SESSION['register_error'] = 'Email is already registered.';
                $_SESSION['active_form'] = 'register';
                header("Location:index.php");
                exit();
            } else {
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, date_created) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $fName, $lName, $email, $username, $password, $creationDate);
                $stmt->execute();
                header("Location:index.php");
                exit();
            }
        }
        else{
                $_SESSION['register_error'] = 'Your passwords do not match.';
                $_SESSION['active_form'] = 'register';
                $conn->close();
                header("Location:index.php");
                exit();
        }
        $stmt->close();
        $conn->close();
        header("Location:index.php");
        exit();

    }
    
    if (isset($_POST['login'])) {
        echo "<pre>";
        var_dump($_POST); // Dump raw form data
        echo "</pre>";
    
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
    
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
    
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['date_created'] = $user['date_created'];
    
                    if($redirect = $user['role'] === 'admin')
                    {
                        header("Location: webPages/adminMenu.php");
                        exit();
                    }
                    else{
                        header("Location: webPages/regularMenu.php");
                        exit();
                    }
    
                } else {
                    $_SESSION['login_error'] = 'Incorrect username or password.';
                    $_SESSION['active_form'] = 'login';
                    header("Location: index.php");
                    exit();
                }
            } else {
                $_SESSION['login_error'] = 'Incorrect username or password.';
                $_SESSION['active_form'] = 'login';
                header("Location: index.php");
                exit();
            }
    
        $conn->close();
        exit();
    }
    
?>
