<?php
// Include the database connection file
include ('../database/database.php');

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with proper escaping
    $customer_id = mysqli_real_escape_string($connection, $_POST['customer_id']);
    $site_id = mysqli_real_escape_string($connection, $_POST['site_id']);
    $avalableSqft = mysqli_real_escape_string($connection, $_POST['avalableSqft']);
    $cBuySqft = mysqli_real_escape_string($connection, $_POST['cBuySqft']);
    $pricePerSquareFeet = mysqli_real_escape_string($connection, $_POST['pricePerSquareFeet']);
    $totalBuyingPrice = mysqli_real_escape_string($connection, $_POST['totalBuyingPrice']);
    $buyingDate = mysqli_real_escape_string($connection, $_POST['buyingDate']);
    $agent_id = mysqli_real_escape_string($connection, $_POST['agent_id']);
    $persantage = mysqli_real_escape_string($connection, $_POST['persantage']); 
    $persantageAmount = mysqli_real_escape_string($connection, $_POST['persantageAmount']);
    $siteno = mysqli_real_escape_string($connection, $_POST['siteno']);
    
    // Prepare the SQL query for booking
    $query = 'INSERT INTO booking (customer_id, site_id, Buying_Sqft, pricePerSquareFeet, totalBuyingPrice, buyingDate, agent_id, persantage, persantageAmount, siteno) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $query);

    // Check if the query was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ssssssssss', $customer_id, $site_id, $cBuySqft, $pricePerSquareFeet, $totalBuyingPrice, $buyingDate, $agent_id, $persantage, $persantageAmount, $siteno);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Prepare the SQL query to update the site table
            $updateQuery = 'UPDATE site SET bookedSqft = bookedSqft + ?, avaliableSqft = avaliableSqft - ? WHERE id = ?';
            $updateStmt = mysqli_prepare($connection, $updateQuery);

            // Check if the update query was prepared successfully
            if ($updateStmt) {
                // Bind the parameters for the update query
                mysqli_stmt_bind_param($updateStmt, 'sss', $cBuySqft, $cBuySqft, $site_id);

                // Execute the update query
                if (mysqli_stmt_execute($updateStmt)) {
                    // Successfully updated the site table
                    header('Location: ../allocate.php');
                    exit();
                } else {
                    // Log the error if the update query failed
                    die('Error updating site table: ' . mysqli_error($connection));
                }

                // Close the update statement
                mysqli_stmt_close($updateStmt);
            } else {
                // Log the error if the update statement preparation failed
                die('Error preparing site update statement: ' . mysqli_error($connection));
            }
        } else {
            // Log the error if the insert query failed
            die('Error inserting into booking table: ' . mysqli_error($connection));
        }

        // Close the insert statement
        mysqli_stmt_close($stmt);
    } else {
        // Log the error if the statement preparation failed
        die('SQL error: ' . mysqli_error($connection));
    }

    // Close the database connection
    mysqli_close($connection);
} else {
    // Redirect to the main page if the request method is not POST
    header('Location: ../allocate.php');
    exit();
}