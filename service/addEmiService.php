<?php
// Include the database connection file
include ('../database/database.php');

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with proper escaping
    $bookingId = mysqli_real_escape_string($connection, $_POST['bookingId']);
    $siteId = mysqli_real_escape_string($connection, $_POST['siteId']);
    $emiPlan = mysqli_real_escape_string($connection, $_POST['emiPlan']);
    $startDate = mysqli_real_escape_string($connection, $_POST['startDate']);
    $endDate = mysqli_real_escape_string($connection, $_POST['endDate']);
    $totalPayable = mysqli_real_escape_string($connection, $_POST['totalPayable']);
    $monthlyPayable = mysqli_real_escape_string($connection, $_POST['monthlyPayable']);
    $paidAmount = 0;
    $balanceAmount = 0;
    $agentCommission = mysqli_real_escape_string($connection, $_POST['agentCommission']);
    $agentCommissionAmount = mysqli_real_escape_string($connection, $_POST['agentCommissionAmount']);

    // Prepare the SQL query to insert EMI details
    $query = 'INSERT INTO emis (booking_id, site_id, emi_plan, startDate, endDate, total_payable, monthly_payable, paid_amount, balance_amount, nextDueDate, agentCommission, agentCommissionAmount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $bookingId, $siteId, $emiPlan, $startDate, $endDate, $totalPayable, $monthlyPayable, $paidAmount, $balanceAmount, $startDate, $agentCommission, $agentCommissionAmount);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Get the last inserted EMI ID
            $emiId = mysqli_insert_id($connection);

            // Prepare the SQL query to update the booking table
            $updateQuery = 'UPDATE booking SET emi_id = ?, isEmi = 1 WHERE bookingid = ?';
            $updateStmt = mysqli_prepare($connection, $updateQuery);

            if ($updateStmt) {
                // Bind the parameters
                mysqli_stmt_bind_param($updateStmt, 'is', $emiId, $bookingId);

                // Execute the update query
                if (mysqli_stmt_execute($updateStmt)) {
                    // Redirect to EMI page after successful update
                    header('Location: ../emi.php');
                    exit();
                } else {
                    // Log the error if the update fails
                    die('Update error: ' . mysqli_error($connection));
                }

                // Close the update statement
                mysqli_stmt_close($updateStmt);
            } else {
                // Log the error if the update statement preparation fails
                die('SQL error: ' . mysqli_error($connection));
            }
        } else {
            // Redirect to the EMI page if the insert fails
            header('Location: ../emi.php');
            exit();
        }

        // Close the insert statement
        mysqli_stmt_close($stmt);
    } else {
        // Log the error if the statement preparation fails
        die('SQL error: ' . mysqli_error($connection));
    }

    // Close the database connection
    mysqli_close($connection);
} else {
    // Redirect to the main page if the request method is not POST
    header('Location: ../emi.php');
    exit();
}
