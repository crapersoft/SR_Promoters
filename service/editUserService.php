<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $userName = $_POST['userName'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $aadharNumber = $_POST['aadharNumber'];
    $status = $_POST['status'];
    $agent = $_POST['agent_id'];
    $persantage = $_POST['persantage'];

    $sql = "UPDATE user 
            SET userName = '$userName', phoneNumber = '$phoneNumber', email = '$email', 
                address = '$address', aadharNumber = '$aadharNumber', status = '$status', agent_id = '$agent', persantage = '$persantage'
            WHERE user_id = $userId";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../users.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>