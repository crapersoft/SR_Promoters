<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['bookingId'];
    $customer_id = $_POST['customer_id'];
    $site_id = $_POST['site_id'];
    $cBuySqft = $_POST['cBuySqft'];
    $pricePerSquareFeet = $_POST['pricePerSquareFeet'];
    $totalBuyingPrice = $_POST['totalBuyingPrice'];
    $buyingDate = $_POST['buyingDate'];
    $agent_id = $_POST['agent_id'];
    $persantage = $_POST['persantage'];
    $persantageAmount = $_POST['persantageAmount'];
    $siteno = $_POST['siteno'];

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Fetch the current Buying_Sqft from the booking table
        $bookingQuery = "SELECT Buying_Sqft FROM booking WHERE bookingid = $bookingId";
        $bookingResult = mysqli_query($connection, $bookingQuery);

        if (!$bookingResult || mysqli_num_rows($bookingResult) === 0) {
            throw new Exception('Error fetching booking data: ' . mysqli_error($connection));
        }

        $currentBooking = mysqli_fetch_assoc($bookingResult);
        $currentBuyingSqft = $currentBooking['Buying_Sqft'];

        // Fetch current bookedSqft and avaliableSqft for the site
        $siteQuery = "SELECT bookedSqft, avaliableSqft FROM site WHERE id = $site_id";
        $siteResult = mysqli_query($connection, $siteQuery);

        if (!$siteResult || mysqli_num_rows($siteResult) === 0) {
            throw new Exception('Error fetching site data: ' . mysqli_error($connection));
        }

        $site = mysqli_fetch_assoc($siteResult);

        // Adjust bookedSqft and avaliableSqft based on the difference in Buying_Sqft
        $differenceSqft = $cBuySqft - $currentBuyingSqft;
        $newBookedSqft = $site['bookedSqft'] + $differenceSqft;
        $newAvailableSqft = $site['avaliableSqft'] - $differenceSqft;

        if ($newAvailableSqft < 0) {
            throw new Exception('Error: Available SqFt cannot be negative.');
        }

        // Update site table with adjusted values
        $updateSiteQuery = "UPDATE site 
                            SET 
                                bookedSqft = '$newBookedSqft', 
                                avaliableSqft = '$newAvailableSqft' 
                            WHERE id = $site_id";

        if (!mysqli_query($connection, $updateSiteQuery)) {
            throw new Exception('Error updating site data: ' . mysqli_error($connection));
        }

        // Update query for the booking table
        $sql = "UPDATE booking 
                SET 
                    customer_id = '$customer_id', 
                    site_id = '$site_id', 
                    Buying_Sqft = '$cBuySqft', 
                    pricePerSquareFeet = '$pricePerSquareFeet', 
                    totalBuyingPrice = '$totalBuyingPrice', 
                    buyingDate = '$buyingDate', 
                    agent_id = '$agent_id', 
                    persantage = '$persantage',
                    persantageAmount = '$persantageAmount',
                    siteno = '$siteno'
                WHERE 
                    bookingid = $bookingId";

        if (!mysqli_query($connection, $sql)) {
            throw new Exception('Error updating booking: ' . mysqli_error($connection));
        }

        // Commit the transaction
        mysqli_commit($connection);

        // Redirect after successful update
        header('Location: ../allocate.php');
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($connection);
        echo 'Error: ' . $e->getMessage();
    }
}
?>