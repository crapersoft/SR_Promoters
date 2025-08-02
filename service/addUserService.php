<?php
// Include the database connection file
include ('../database/database.php');

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with proper escaping
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $aadhar = mysqli_real_escape_string($connection, $_POST['aadhar']);
    $status = 'active';
    $agent = mysqli_real_escape_string($connection, $_POST['agent_id']);
    $persantage = mysqli_real_escape_string($connection, $_POST['persantage']);

    // Prepare the SQL query
    $query = 'INSERT INTO user (userName, phoneNumber, email, address, aadharNumber, status, agent_id, persantage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $query);

    // Check if the query was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ssssssss', $name, $phone, $email, $address, $aadhar, $status, $agent, $persantage);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../users.php');
            exit();
        } else {
            header('Location: ../users.php');
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
    header('Location: ../users.php');
    exit();
}