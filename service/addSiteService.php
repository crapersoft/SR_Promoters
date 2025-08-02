<?php
// Include the database connection file
include ('../database/database.php');

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with proper escaping
    $siteName = mysqli_real_escape_string($connection, $_POST['siteName']);
    $totalSquareFeet = mysqli_real_escape_string($connection, $_POST['totalSquareFeet']);
    $pricePerSquareFeet = mysqli_real_escape_string($connection, $_POST['pricePerSquareFeet']);
    $totalPricePerSquareFeet = mysqli_real_escape_string($connection, $_POST['totalPricePerSquareFeet']);
    $siteAddress = mysqli_real_escape_string($connection, $_POST['siteAddress']);

    // Prepare the SQL query
    $query = 'INSERT INTO site (site_name, total_sqft, price_per_sqft, total_price_sqft, site_address, avaliableSqft) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $query);

    // Check if the query was prepared successfully
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ssssss', $siteName, $totalSquareFeet, $pricePerSquareFeet, $totalPricePerSquareFeet, $siteAddress, $totalSquareFeet);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../site.php');
            exit();
        } else {
            header('Location: ../site.php');
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
    header('Location: ../site.php');
    exit();
}
