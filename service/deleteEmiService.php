<?php
include ('../database/database.php');
session_start();

if (isset($_GET['emiIds'])) {
    $emiIds = $_GET['emiIds'];

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Update the booking table to set isEmi and emi_id to 0 for the given emiIds
        $updateBookingQuery = "UPDATE booking 
                               SET 
                                   isEmi = 0, 
                                   emi_id = 0 
                               WHERE emi_id = $emiIds";

        if (!mysqli_query($connection, $updateBookingQuery)) {
            throw new Exception('Error updating booking table: ' . mysqli_error($connection));
        }

        // Delete the record from the emis table
        $deleteEmiQuery = "DELETE FROM emis WHERE emi_ids = $emiIds";

        if (!mysqli_query($connection, $deleteEmiQuery)) {
            throw new Exception('Error deleting from emis table: ' . mysqli_error($connection));
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Redirect after successful operation
        header('Location: ../emi.php');
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($connection);
        echo 'Error: ' . $e->getMessage();
    }
}
?>