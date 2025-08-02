<?php
include ('../database/database.php');
session_start();

if (isset($_GET['siteId'])) {
    $siteId = $_GET['siteId'];

    $sql = "DELETE FROM site WHERE id = $siteId";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../site.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>