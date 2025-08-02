<?php
include ('../database/database.php');

// Define the plain text password and username
$plainPassword = 'user';  // Replace with your plain text password
$username = 'user';  // Replace with the username

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Prepare the SQL query
$query = 'UPDATE login SET password = ? WHERE userName = ?';
$stmt = mysqli_prepare($connection, $query);

// Bind the parameters (variables only, not literals)
mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $username);

// Execute the query
mysqli_stmt_execute($stmt);

// Check if the password was updated
if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo 'Password updated successfully.';
} else {
    echo 'Failed to update password.';
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
