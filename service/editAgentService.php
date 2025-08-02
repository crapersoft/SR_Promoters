<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agent_id = $_POST['agent_id'];
    $agentName = $_POST['agent_name'];
    $phoneNumber = $_POST['mobile_number'];
    $email = $_POST['emil_id'];
    $address = $_POST['agent_address'];
    $aadharNumber = $_POST['agent_aadhar_no'];
    $status = $_POST['status'];

    $sql = "UPDATE agent 
            SET agent_name = '$agentName', mobile_number = '$phoneNumber', emil_id = '$email', 
                agent_address = '$address', agent_aadhar_no = '$aadharNumber', status='$status'
            WHERE agent_id = $agent_id";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../agents.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>