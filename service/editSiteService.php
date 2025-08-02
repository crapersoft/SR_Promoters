<?php
include ('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteId = $_POST['siteId'];
    $siteName = $_POST['siteName'];
    $totalSquareFeet = $_POST['totalSquareFeet'];
    $pricePerSquareFeet = $_POST['pricePerSquareFeet'];
    $totalPricePerSquareFeet = $_POST['totalPricePerSquareFeet'];
    $siteAddress = $_POST['siteAddress'];

    $sql = " UPDATE site SET site_name = '$siteName', total_sqft = '$totalSquareFeet', price_per_sqft = '$pricePerSquareFeet', 
            total_price_sqft = '$totalPricePerSquareFeet', site_address = '$siteAddress' WHERE id = $siteId ";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../site.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>