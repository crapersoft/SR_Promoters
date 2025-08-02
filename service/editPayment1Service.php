<?php
include ('../database/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentDate = $_POST['paymentDate'];
    $totalAmount = $_POST['totalAmount'];
    $paymentMode = $_POST['paymentMode'];

    $bookingId = $_POST['bookingId'];
    $userId = $_POST['userId'];
    $fpId = $_POST['fpId'];
    $siteId = $_POST['siteId'];

    // Validate the data
    if (empty($paymentDate) || empty($totalAmount) || empty($paymentMode)) {
        echo 'Please fill in all required fields.';
        exit();
    }

    // Update the payment record in the database
    $sql = "UPDATE fullpayments SET 
                payment_date = '$paymentDate',
                payment_amount = '$totalAmount',
                payment_method = '$paymentMode'
            WHERE booking_id = '$bookingId' AND fp_id = '$fpId' AND site_id = '$siteId'";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../payment.php?success=1');
        exit();
    } else {
        echo 'Error updating record: ' . mysqli_error($connection);
    }
}
?>
