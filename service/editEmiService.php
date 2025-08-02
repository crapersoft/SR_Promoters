<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data from the form
    $emi_ids = $_POST['emi_ids'];  // the EMI ID (primary key)
    $booking_id = $_POST['bookingId'];  // Booking ID passed from the form
    $site_id = $_POST['siteId'];  // Site ID passed from the form
    $emi_plan = $_POST['emiPlan'];  // EMI plan (months)
    $startDate = $_POST['startDate'];  // Start date of the EMI
    $endDate = $_POST['endDate'];  // End date of the EMI (calculated dynamically)
    $total_payable = $_POST['totalPayable'];  // Total payable amount (readonly)
    $monthly_payable = $_POST['monthlyPayable'];  // Monthly payable amount (can be modified)

    $agentCommission = $_POST['agentCommission'];
    $agentCommissionAmount = $_POST['agentCommissionAmount'];

    // Update EMI details in the database
    $sql = "UPDATE emis 
            SET booking_id = '$booking_id', 
                site_id = '$site_id', 
                emi_plan = '$emi_plan', 
                startDate = '$startDate', 
                endDate = '$endDate', 
                total_payable = '$total_payable', 
                monthly_payable = '$monthly_payable',
                agentCommission = '$agentCommission',
                agentCommissionAmount = '$agentCommissionAmount'
            WHERE emi_ids = $emi_ids";

    // Execute the query and check if successful
    if (mysqli_query($connection, $sql)) {
        // Redirect to the EMI management page upon successful update
        header('Location: ../emi.php');
        exit();
    } else {
        // If an error occurs, display it
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>