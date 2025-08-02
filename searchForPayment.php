<?php
include ('./database/database.php');

if (isset($_GET['query'])) {
    $query = strtolower($_GET['query']);

    $stmt = $connection->prepare(
        "SELECT b.bookingid, b.Buying_Sqft, b.totalBuyingPrice, b.isEmi, b.emi_id, u.userName, u.user_id, s.site_name, s.id, 
            a.agent_name, e.paid_amount, e.balance_amount, e.nextDueDate, e.total_payable, e.monthly_payable, e.emi_ids
            FROM booking b
            LEFT JOIN user u ON b.customer_id = u.user_id
            LEFT JOIN site s ON b.site_id = s.id
            LEFT JOIN agent a ON b.agent_id = a.agent_id
            LEFT JOIN emis e ON b.emi_id = e.emi_ids
            WHERE LOWER(s.site_name) LIKE CONCAT('%', ?, '%') 
            OR LOWER(u.userName) LIKE CONCAT('%', ?, '%')
            OR LOWER(a.agent_name) LIKE CONCAT('%', ?, '%')
            LIMIT 10"
    );

    $stmt->bind_param('sss', $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'bookingid' => $row['bookingid'],
            'userName' => $row['userName'],
            'userId' => $row['user_id'],
            'site_name' => $row['site_name'],
            'Buying_Sqft' => $row['Buying_Sqft'],
            'agent_name' => $row['agent_name'],
            'totalBuyingPrice' => $row['totalBuyingPrice'],
            'siteId' => $row['id'],
            'paid_amount' => $row['paid_amount'],
            'balance_amount' => $row['balance_amount'],
            'nextDueDate' => $row['nextDueDate'],
            'total_payable' => $row['total_payable'],
            'monthly_payable' => $row['monthly_payable'],
            'isEmi' => $row['isEmi'],
            'emi_id' => $row['emi_ids'],
        ];
    }

    echo json_encode($data);
}
?>
