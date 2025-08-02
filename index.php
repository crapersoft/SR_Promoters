<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Helper function to fetch single values
function fetchSingleValue($connection, $query, $paramType = '', $params = [])
{
    $stmt = $connection->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($paramType, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? array_values($row)[0] : null;
    } else {
        return null;
    }
}

// Get the current month in YYYY-MM format
$currentMonth = date('Y-m');

// Fetch EMI payments received for the current month
$emi_payment_received = fetchSingleValue($connection, 
    'SELECT SUM(amount) AS emi_payment_received FROM payment WHERE DATE_FORMAT(payment_date, "%Y-%m") = ?', 's', [$currentMonth]) ?? 0;

// Fetch full payments received for the current month
$full_payment_received = fetchSingleValue($connection, 
    'SELECT SUM(payment_amount) AS full_payment_received FROM fullpayments WHERE DATE_FORMAT(payment_date, "%Y-%m") = ?', 's', [$currentMonth]) ?? 0;

// Calculate total payments for the current month
$total_payments = $emi_payment_received + $full_payment_received;

// Fetch total EMIs for the current month
$total_emis = fetchSingleValue($connection, 
    'SELECT COUNT(*) AS total_emis FROM emis WHERE DATE_FORMAT(created_at, "%Y-%m") = ?', 's', [$currentMonth]) ?? 0;

// Fetch EMI data for the current month
$sql = "SELECT e.*, 
            (SELECT u.userName FROM booking b 
             JOIN user u ON b.customer_id = u.user_id 
             WHERE b.bookingid = e.booking_id LIMIT 1) AS customer_name,
            (SELECT site_name FROM site WHERE id = e.site_id LIMIT 1) AS site_name,
            (SELECT Buying_Sqft FROM booking WHERE bookingid = e.booking_id LIMIT 1) AS sqft,
            p.payment_date, p.amount, e.nextDueDate
        FROM emis e
        JOIN payment p ON e.emi_ids = p.emi_id
        WHERE DATE_FORMAT(p.payment_date, '%Y-%m') = ?
        ORDER BY p.payment_date ASC";

$stmt = $connection->prepare($sql);
if (!$stmt) {
    die('SQL Error: ' . $connection->error);
}

$stmt->bind_param('s', $currentMonth);
$stmt->execute();
$result = $stmt->get_result();
$emis = mysqli_num_rows($result) > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Custom styles to increase the card height -->
    <style>
    .custom-card {
        min-height: 180px;
        /* Set a minimum height for the cards */
        display: flex;
        /* Flexbox layout */
        flex-direction: column;
        /* Make the contents of the card align vertically */
        justify-content: space-between;
        /* Distribute the content evenly */
    }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include './include/sidebar.php'; ?>
        <div class="body-wrapper">
            <?php include './include/header.php'; ?>
            <div class="container-fluid">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-sm-3">
                                <div class="card bg-success text-center custom-card">
                                    <div class="card-header bg-info-subtle">
                                        <h1 class="card-title">Full Payments </h1>
                                    </div>
                                    <div class="card-body">
                                        <h3>₹<?php echo number_format($full_payment_received, 2); ?></h3>
                                        <a href="./payment.php" class="btn btn-light btn-sm mt-2">View Payments</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="card bg-warning text-center custom-card">
                                    <div class="card-header bg-info-subtle">
                                        <h1 class="card-title"> EMI Payments </h1>
                                    </div>
                                    <div class="card-body">
                                        <h3>₹<?php echo number_format($emi_payment_received, 2); ?></h3>
                                        <a href="./payment.php" class="btn btn-light btn-sm mt-2">View Payments</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="card bg-danger text-center custom-card">
                                    <div class="card-header bg-info-subtle">
                                        <h1 class="card-title">Total Payments </h1>
                                    </div>
                                    <div class="card-body">
                                        <h3>₹<?php echo number_format($total_payments, 2); ?></h3>
                                        <a href="./payment.php" class="btn btn-light btn-sm mt-2">View Payments</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="card bg-info text-center custom-card">
                                    <div class="card-header bg-info-subtle">
                                        <h1 class="card-title">Total EMIs</h1>
                                    </div>
                                    <div class="card-body">
                                        <h3><?php echo $total_emis; ?></h3>
                                        <a href="./emi.php" class="btn btn-light btn-sm mt-2">View EMIs</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card w-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">This Month's EMI Payments</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Site Name</th>
                                        <th>SqFt</th>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Next Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($emis)): ?>
                                    <?php foreach ($emis as $emi): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($emi['customer_name'] ?? 'Customer Name Not Found'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($emi['site_name'] ?? 'Site Name Not Found'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($emi['sqft'] ?? 'SqFt Not Found'); ?> sqft</td>
                                        <td><?php echo htmlspecialchars($emi['payment_date']); ?></td>
                                        <td><?php echo number_format($emi['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($emi['nextDueDate'] ?? 'Not Available'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No EMI payments found for this month</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
            <div class="py-6 px-6 text-center">
                <p class="mb-0 fs-4">Design and Developed by <a href="https://crapersoft.com/" target="_blank"
                        class="pe-1 text-primary text-decoration-underline">Crapersoft</a></p>
            </div>
        </div>
    </div>
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
</body>

</html>