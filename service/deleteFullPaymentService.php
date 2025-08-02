<?php
include ('../database/database.php');
session_start();

if (isset($_GET['paymentId'])) {
    $paymentId = $_GET['paymentId'];

    $sql = "DELETE FROM fullpayments WHERE fp_id = $paymentId";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../payment.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>