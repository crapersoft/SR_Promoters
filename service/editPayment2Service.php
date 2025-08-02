<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentDate = $_POST['paymentDate'];
    $totalAmount = $_POST['totalAmount'];
    $paymentMode = $_POST['paymentMode'];
    $amount = $_POST['amount'];
    $penalty = $_POST['penalty'];
    $totalAmountAndPenalty = $_POST['totalAmountAndPenalty'];
    $paidAmount = $_POST['paidAmount'];
    $balanceAmount = $_POST['balanceAmount'];
    $bookingId = $_POST['bookingId'];
    $userId = $_POST['userId'];
    $emiId = $_POST['emiId'];
    $siteId = $_POST['siteId'];
    $paymentId =  $_POST['payment_id'];

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Fetch agentCommission from the emis table
        $sqlFetchEmis = "SELECT agentCommission FROM emis WHERE emi_ids = '$emiId'";
        $resultFetchEmis = mysqli_query($connection, $sqlFetchEmis);

        if (!$resultFetchEmis || mysqli_num_rows($resultFetchEmis) === 0) {
            throw new Exception('EMI record not found.');
        }

        $emisData = mysqli_fetch_assoc($resultFetchEmis);
        $agentCommission = $emisData['agentCommission'];

        // Calculate the agentCommissionAmount
        $agentCommissionAmount = ($amount * $agentCommission) / 100;

        // Fetch the current paid_amount from the payment table
        $sqlFetchPayment = "SELECT amount, agent_commison FROM payment 
                            WHERE booking_id = '$bookingId' AND emi_id = '$emiId' AND site_id = '$siteId'";
        $resultFetchPayment = mysqli_query($connection, $sqlFetchPayment);

        if (!$resultFetchPayment || mysqli_num_rows($resultFetchPayment) === 0) {
            throw new Exception('Payment record not found.');
        }

        $paymentData = mysqli_fetch_assoc($resultFetchPayment);
        $currentPaidAmount = $paymentData['amount'];
        $currentAgentCommission = $paymentData['agent_commison'];

        // Update the balance_amount in the emis table
        $sqlUpdateEmis = "UPDATE emis 
                          SET paid_amount = paid_amount - '$currentPaidAmount' + '$amount',
                              balance_amount = balance_amount + '$currentPaidAmount' - '$amount',
                              total_payment_commissions = total_payment_commissions - '$currentAgentCommission' + '$agentCommissionAmount'
                          WHERE emi_ids = '$emiId'";
        if (!mysqli_query($connection, $sqlUpdateEmis)) {
            throw new Exception('Error updating EMI balance: ' . mysqli_error($connection));
        }

         $logFile = 'log.txt'; // Define log file

        // Ensure variables are defined and have values
        $bookingId = isset($bookingId) ? $bookingId : 'N/A';
        $emiId = isset($emiId) ? $emiId : 'N/A';
        $siteId = isset($siteId) ? $siteId : 'N/A';
        $paymentId = isset($paymentId) ? $paymentId : 'N/A';

        $message = date("Y-m-d H:i:s") . " - booking_id = $bookingId, emi_id = $emiId, site_id = $siteId\n"; 

        echo $message; // Debugging Output

        // Check if file exists, if not create it (Optional, not required for file_put_contents)
        if (!file_exists($logFile)) {
            touch($logFile); // Create file if it doesn't exist
            chmod($logFile, 0666); // Ensure the file has write permissions
        }

        // Write log message
        if (file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX) === false) {
            echo "Failed to write to log file.";
        } else {
            echo "Log entry added successfully.";
        }

        // Update the payment record in the database
        $sqlUpdatePayment = "UPDATE payment SET 
                                payment_date = '$paymentDate',
                                total_amount = '$totalAmount',
                                payment_mode = '$paymentMode',
                                amount = '$amount',
                                penalty = '$penalty',
                                totalAmountAndPenalty = '$totalAmountAndPenalty',
                                paid_amount = paid_amount - '$currentPaidAmount' + '$amount',
                                balance_amount = balance_amount + '$currentPaidAmount' - '$amount',
                                agent_commison = '$agentCommissionAmount',
                                updated_at = NOW() 
                            WHERE booking_id = '$bookingId' AND emi_id = '$emiId' AND site_id = '$siteId' AND id = '$paymentId'";
        if (!mysqli_query($connection, $sqlUpdatePayment)) {
            throw new Exception('Error updating payment record: ' . mysqli_error($connection));
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Redirect on success
        header('Location: ../payment.php?success=1');
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($connection);
        echo 'Error: ' . $e->getMessage();
    }
}
?>
