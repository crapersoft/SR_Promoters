<?php
include ('database/database.php');

if (isset($_GET['query'])) {
    $query = strtolower($_GET['query']);  // Convert to lowercase for case-insensitive search

    // Query to fetch matching users
    $stmt = $connection->prepare("SELECT user_id, userName FROM user WHERE LOWER(userName) LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'user_id' => $row['user_id'],
            'userName' => $row['userName']
        ];
    }

    // Return JSON response
    echo json_encode($users);
}
?>