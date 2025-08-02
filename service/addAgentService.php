<?php
// Include the database connection file
include ('../database/database.php');

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with proper escaping
    $name = mysqli_real_escape_string($connection, $_POST['agent_name']);
    $phone = mysqli_real_escape_string($connection, $_POST['mobile_number']);
    $email = mysqli_real_escape_string($connection, $_POST['emil_id']);
    $address = mysqli_real_escape_string($connection, $_POST['agent_address']);
    $aadhar = mysqli_real_escape_string($connection, $_POST['agent_aadhar_no']);
    $status = 'active';

    // Prepare the SQL query
    $query = 'INSERT INTO agent (agent_name, mobile_number, emil_id, agent_address, agent_aadhar_no, status) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $query);

    // Check if the query was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ssssss', $name, $phone, $email, $address, $aadhar, $status);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../agents.php');
            exit();
        } else {
            header('Location: ../agents.php');
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Log the error if the statement preparation failed
        die('SQL error: ' . mysqli_error($connection));
    }

    // Close the database connection
    mysqli_close($connection);
} else {
    // Redirect to the main page if the request method is not POST
    header('Location: ../agents.php');
    exit();
}