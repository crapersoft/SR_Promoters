<?php
include ('../database/database.php');
session_start();

if (isset($_GET['agentId'])) {
    $agentId = $_GET['agentId'];

    $sql = "DELETE FROM agent WHERE agent_id = $agentId";

    if (mysqli_query($connection, $sql)) {
        header('Location: ../agents.php');
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}
?>