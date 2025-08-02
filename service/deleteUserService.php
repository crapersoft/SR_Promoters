<?php
include ('../database/database.php');
session_start();

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    $sql = "DELETE FROM user WHERE user_id = $userId";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../users.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>