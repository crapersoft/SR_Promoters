<?php
include ('../database/database.php');
session_start();

if (isset($_GET['bookingid'])) {
    $bookingid = $_GET['bookingid'];

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Fetch the Buying_Sqft and site_id from the booking table
        $bookingQuery = "SELECT Buying_Sqft, site_id FROM booking WHERE bookingid = $bookingid";
        $bookingResult = mysqli_query($connection, $bookingQuery);

        if (!$bookingResult || mysqli_num_rows($bookingResult) === 0) {
            throw new Exception('Error fetching booking data: ' . mysqli_error($connection));
        }

        $booking = mysqli_fetch_assoc($bookingResult);
        $buyingSqft = $booking['Buying_Sqft'];
        $site_id = $booking['site_id'];

        // Fetch current bookedSqft and avaliableSqft for the site
        $siteQuery = "SELECT bookedSqft, avaliableSqft FROM site WHERE id = $site_id";
        $siteResult = mysqli_query($connection, $siteQuery);

        if (!$siteResult || mysqli_num_rows($siteResult) === 0) {
            throw new Exception('Error fetching site data: ' . mysqli_error($connection));
        }

        $site = mysqli_fetch_assoc($siteResult);

        // Update bookedSqft and avaliableSqft in the site table
        $newBookedSqft = $site['bookedSqft'] - $buyingSqft;
        $newAvailableSqft = $site['avaliableSqft'] + $buyingSqft;

        if ($newBookedSqft < 0) {
            throw new Exception('Error: Booked SqFt cannot be negative.');
        }

        $updateSiteQuery = "UPDATE site 
                            SET 
                                bookedSqft = '$newBookedSqft', 
                                avaliableSqft = '$newAvailableSqft' 
                            WHERE id = $site_id";

        if (!mysqli_query($connection, $updateSiteQuery)) {
            throw new Exception('Error updating site data: ' . mysqli_error($connection));
        }

        // Delete the booking record
        $deleteQuery = "DELETE FROM booking WHERE bookingid = $bookingid";

        if (!mysqli_query($connection, $deleteQuery)) {
            throw new Exception('Error deleting booking: ' . mysqli_error($connection));
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Redirect after successful deletion
        header('Location: ../allocate.php');
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($connection);
        echo 'Error: ' . $e->getMessage();
    }
}
?>