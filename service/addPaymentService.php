<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../database/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentDate = mysqli_real_escape_string($connection, $_POST['paymentDate']);
    $bookingId = mysqli_real_escape_string($connection, $_POST['bookingId']);
    $siteId = mysqli_real_escape_string($connection, $_POST['siteId']);
    $emiId = mysqli_real_escape_string($connection, $_POST['emiId']);
    $customerId = mysqli_real_escape_string($connection, $_POST['userId']);
    $totalAmount = mysqli_real_escape_string($connection, $_POST['totalAmount']);
    $paymentMode = mysqli_real_escape_string($connection, $_POST['paymentMode']);
    $amount = mysqli_real_escape_string($connection, $_POST['amount']);
    $penalty = mysqli_real_escape_string($connection, $_POST['penalty']);
    $totalAmountAndPenalty = mysqli_real_escape_string($connection, $_POST['totalAmountAndPenalty']);
    $paidAmount = mysqli_real_escape_string($connection, $_POST['paidAmount']);
    $isEmi = mysqli_real_escape_string($connection, $_POST['isEmi']);

    // Debug: Log input data
    echo "<script>console.log('Input Data: paymentDate=$paymentDate, bookingId=$bookingId, siteId=$siteId, emiId=$emiId, customerId=$customerId, totalAmount=$totalAmount, paymentMode=$paymentMode, amount=$amount, penalty=$penalty, totalAmountAndPenalty=$totalAmountAndPenalty, paidAmount=$paidAmount, isEmi=$isEmi');</script>";

    // Begin transaction
    mysqli_begin_transaction($connection);

    try {
        $agentCommission = 0;
        $agentCommissionAmount = 0;

        if ($isEmi == 1) {
            // Retrieve agentCommission percentage from emis table
            $queryAgentCommission = 'SELECT agentCommission FROM emis WHERE emi_ids = ?';
            $stmtAgentCommission = mysqli_prepare($connection, $queryAgentCommission);

            if ($stmtAgentCommission) {
                mysqli_stmt_bind_param($stmtAgentCommission, 's', $emiId);
                mysqli_stmt_execute($stmtAgentCommission);
                mysqli_stmt_bind_result($stmtAgentCommission, $agentCommission);
                mysqli_stmt_fetch($stmtAgentCommission);
                mysqli_stmt_close($stmtAgentCommission);

                // Debug: Log the retrieved agentCommission
                echo "<script>console.log('Retrieved agentCommission: $agentCommission');</script>";

                // Calculate agentCommissionAmount
                $agentCommissionAmount = ($amount * $agentCommission) / 100;

                // Debug: Log the calculated agentCommissionAmount
                echo "<script>console.log('Calculated agentCommissionAmount: $agentCommissionAmount');</script>";
            } else {
                throw new Exception('SQL error while fetching agent commission: ' . mysqli_error($connection));
            }

            // Calculate new paid and balance amounts
            $newPaidAmount = $paidAmount + $amount;
            $balanceAmount = $totalAmount - $newPaidAmount;
            $balanceAmount = max($balanceAmount, 0);

            // Calculate next EMI due date (assuming monthly interval)
            $nextEmiDueDate = date('Y-m-d', strtotime('+1 month', strtotime($paymentDate)));

            // Insert into payment table (for EMI payments)
            $query = 'INSERT INTO payment (customer_id, booking_id, emi_id, site_id, payment_date, amount, penalty, totalAmountAndPenalty, total_amount, paid_amount, balance_amount, payment_mode, agent_commison) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($connection, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssssssssssss', $customerId, $bookingId, $emiId, $siteId, $paymentDate, $amount, $penalty, $totalAmountAndPenalty, $totalAmount, $newPaidAmount, $balanceAmount, $paymentMode, $agentCommissionAmount);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Execution error in payment insert: ' . mysqli_stmt_error($stmt));
                }
            } else {
                throw new Exception('SQL error in payment insert: ' . mysqli_error($connection));
            }

            // Update emis table
            $updateQuery = 'UPDATE emis SET paid_amount = ?, balance_amount = ?, nextDueDate = ?, payment_count = payment_count + 1, total_payment_commissions = total_payment_commissions + ? WHERE emi_ids = ?';
            $updateStmt = mysqli_prepare($connection, $updateQuery);

            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, 'sssss', $newPaidAmount, $balanceAmount, $nextEmiDueDate, $agentCommissionAmount, $emiId);

                if (!mysqli_stmt_execute($updateStmt)) {
                    throw new Exception('Execution error in emis update: ' . mysqli_stmt_error($updateStmt));
                }
            } else {
                throw new Exception('SQL error in emis update: ' . mysqli_error($connection));
            }
        } else {
            // Insert into fullpayments table (for full payments)
            $query = 'INSERT INTO fullpayments (booking_id, customer_id, site_id, payment_amount, payment_date, payment_method) VALUES (?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($connection, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssss', $bookingId, $customerId, $siteId, $totalAmount, $paymentDate, $paymentMode);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Execution error in fullpayments insert: ' . mysqli_stmt_error($stmt));
                }
            } else {
                throw new Exception('SQL error in fullpayments insert: ' . mysqli_error($connection));
            }
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Debug: Log success
        echo "<script>console.log('Transaction committed successfully');</script>";
        header('Location: ../payment.php?status=success');
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connection);

        // Output error to browser console
        $errorMessage = addslashes($e->getMessage());
        echo "<script>console.error('Transaction rolled back: $errorMessage');</script>";

        header('Location: ../payment.php?status=error');
        exit();
    }

    // Close the connection
    mysqli_close($connection);
} else {
    header('Location: ../payment.php');
    exit();
}
?>