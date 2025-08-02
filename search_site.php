<?php
include ('database/database.php');

if (isset($_GET['query'])) {
    $query = strtolower($_GET['query']);  // Convert to lowercase for case-insensitive search

    // Query to fetch matching sites
    $stmt = $connection->prepare("SELECT id, site_name, total_sqft, price_per_sqft, total_price_sqft, site_address, created_at, bookedSqft, (total_sqft - bookedSqft) AS avaliableSqft FROM site WHERE LOWER(site_name) LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $sites = [];
    while ($row = $result->fetch_assoc()) {
        $sites[] = [
            'id' => $row['id'],
            'site_name' => $row['site_name'],
            'total_sqft' => $row['total_sqft'],
            'price_per_sqft' => $row['price_per_sqft'],
            'total_price_sqft' => $row['total_price_sqft'],
            'site_address' => $row['site_address'],
            'created_at' => $row['created_at'],
            'bookedSqft' => $row['bookedSqft'],
            'avaliableSqft' => $row['avaliableSqft']
        ];
    }

    // Return JSON response
    echo json_encode($sites);
}
?>