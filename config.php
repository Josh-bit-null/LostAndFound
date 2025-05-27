<?php
    $host = 'localhost';
    $user = 'root';
    $password = '1212Wawasa';
    $database = 'lost_and_found';

    $conn = new mysqli($host, $user, $password, $database);

    if($conn->connect_error)
    {
        die("Connection Failed". $conn->connect_error);
    }
    
?>