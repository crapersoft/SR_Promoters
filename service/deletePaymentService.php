<?php
include ('../database/database.php');
session_start();

if (isset($_GET['paymentId'])) {
    $paymentId = $_GET['paymentId'];

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Fetch the payment record to get the amount and related emi_id
        $sqlFetchPayment = "SELECT emi_id, amount, agent_commison FROM payment WHERE id = $paymentId";
        $resultFetchPayment = mysqli_query($connection, $sqlFetchPayment);

        if (!$resultFetchPayment || mysqli_num_rows($resultFetchPayment) === 0) {
            throw new Exception('Payment record not found.');
        }

        $paymentData = mysqli_fetch_assoc($resultFetchPayment);
        $emiId = $paymentData['emi_id'];
        $amount = $paymentData['amount'];
        $agentCommission = $paymentData['agent_commison'];

        // Update the emis table
        $sqlUpdateEmis = "UPDATE emis 
                          SET paid_amount = paid_amount - '$amount',
                              balance_amount = balance_amount + '$amount',
                                total_payment_commissions = total_payment_commissions - '$agentCommission'
                          WHERE emi_ids = '$emiId'";
        if (!mysqli_query($connection, $sqlUpdateEmis)) {
            throw new Exception('Error updating EMI balance: ' . mysqli_error($connection));
        }

        // Delete the payment record
        $sqlDeletePayment = "DELETE FROM payment WHERE id = $paymentId";
        if (!mysqli_query($connection, $sqlDeletePayment)) {
            throw new Exception('Error deleting payment record: ' . mysqli_error($connection));
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Redirect on success
        header('Location: ../payment.php');
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($connection);
        echo 'Error: ' . $e->getMessage();
    }
}
?>