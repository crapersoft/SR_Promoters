<?php
include ('database/database.php');

if (isset($_GET['query'])) {
    $query = strtolower($_GET['query']);  // Convert to lowercase for case-insensitive search

    // Query to fetch matching agents
    $stmt = $connection->prepare("SELECT agent_id, agent_name FROM agent WHERE LOWER(agent_name) LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $agents = [];
    while ($row = $result->fetch_assoc()) {
        $agents[] = [
            'id' => $row['agent_id'],
            'name' => $row['agent_name']
        ];
    }

    // Return JSON response
    echo json_encode($agents);
}
?>