<?php
include ('database/database.php');
session_start();

// Check if the user is logged in as an admin
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Get the agent ID from the query parameters
$agent_id = $_GET['id'];

// Fetch agent details
$query_agent = "SELECT * FROM agent WHERE agent_id = '" . $agent_id . "'";
$result_agent = mysqli_query($connection, $query_agent);
$agent = mysqli_fetch_assoc($result_agent);

// Check if agent query was successful
if (!$result_agent) {
    die('Error fetching agent details: ' . mysqli_error($connection));
}

// Fetch all bookings associated with the agent
$query_bookings = "SELECT 
                        b.bookingid,
                        b.persantage,
                        b.Buying_Sqft,
                        b.totalBuyingPrice,
                        b.persantageAmount,
                        b.buyingDate,
                        b.customer_id,
                        b.site_id,
                        b.siteno,
                        b.emi_id,
                        u.userName,
                        u.phoneNumber,
                        u.email,
                        s.site_name 
                    FROM 
                        booking b 
                    LEFT JOIN 
                        user u ON b.customer_id = u.user_id 
                    LEFT JOIN 
                        site s ON b.site_id = s.id 
                    WHERE 
                        b.agent_id = '" . $agent_id . "'";

$result_bookings = mysqli_query($connection, $query_bookings);

// Check if bookings query was successful
if (!$result_bookings) {
    die('Error fetching bookings: ' . mysqli_error($connection));
}

// Fetch bookings data
$bookings = mysqli_fetch_all($result_bookings, MYSQLI_ASSOC);

// Fetch EMI commissions
$query_emis = "
    SELECT 
        e.emi_ids,
        e.booking_id,
        e.agentCommission,
        b.customer_id,
        b.site_id,
        b.siteno,
        u.userName,
        s.site_name,
        p.payment_date,
        MONTH(p.payment_date) AS payment_month,
        YEAR(p.payment_date) AS payment_year,
        p.agent_commison AS agent_commission_percent,
        p.total_amount AS agent_commission_amount
    FROM 
        emis e
    LEFT JOIN 
        booking b ON e.booking_id = b.bookingid
    LEFT JOIN 
        user u ON b.customer_id = u.user_id
    LEFT JOIN 
        site s ON b.site_id = s.id
    LEFT JOIN 
        payment p ON e.emi_ids = p.emi_id
    WHERE 
        b.agent_id = '" . $agent_id . "'
    ORDER BY 
        YEAR(p.payment_date), MONTH(p.payment_date), p.payment_date";

$result_emis = mysqli_query($connection, $query_emis);

// Check if EMI query was successful
if (!$result_emis) {
    die('Error fetching EMI commissions: ' . mysqli_error($connection));
}

// Fetch EMI data
$emis = mysqli_fetch_all($result_emis, MYSQLI_ASSOC);

// Initialize variables for calculating totals
$totalPercentage = 0;
$totalPercentageAmount = 0;
$totalBuyingPrice = 0;

// Handle month/year filter for EMI commissions
$selected_month = isset($_GET['emi_month']) ? intval($_GET['emi_month']) : null;
$selected_year = isset($_GET['emi_year']) ? intval($_GET['emi_year']) : null;

// Group emis by year and month
$grouped_emis = [];
foreach ($emis as $emi) {
    $y = $emi['payment_year'];
    $m = $emi['payment_month'];
    if (!isset($grouped_emis[$y])) $grouped_emis[$y] = [];
    if (!isset($grouped_emis[$y][$m])) $grouped_emis[$y][$m] = [];
    $grouped_emis[$y][$m][] = $emi;
}

// If filter is set, show only that month, else show all months
if ($selected_month && $selected_year) {
    $display_emis = isset($grouped_emis[$selected_year][$selected_month]) ? [$selected_year => [$selected_month => $grouped_emis[$selected_year][$selected_month]]] : [];
} else {
    $display_emis = $grouped_emis;
}

// Calculate totals for the agent's bookings
foreach ($bookings as $booking) {
    $percentage = $booking['persantage'];
    $buyingPrice = $booking['totalBuyingPrice'];
    $percentageAmount = $booking['persantageAmount'];
    $totalPercentage += $percentage;
    $totalPercentageAmount += $percentageAmount;
    $totalBuyingPrice += $buyingPrice;
}

// Calculate the percentage of totalPercentageAmount relative to totalBuyingPrice
$percentageOfTotal = 0;
if ($totalBuyingPrice > 0) {
    $percentageOfTotal = ($totalPercentageAmount / $totalBuyingPrice) * 100;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
        color: #007bff;
    }

    table {
        margin-bottom: 20px;
    }

    .back-button-container {
        text-align: right;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-button-container">
            <a href="javascript:history.back()" class="btn btn-primary">Back</a>
        </div>

        <h3>Agent Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Agent Name</th>
                <td><?php echo $agent['agent_name']; ?></td>
            </tr>
            <tr>
                <th>Mobile Number</th>
                <td><?php echo $agent['mobile_number']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $agent['emil_id']; ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo $agent['agent_address']; ?></td>
            </tr>
            <tr>
                <th>Aadhar Number</th>
                <td><?php echo $agent['agent_aadhar_no']; ?></td>
            </tr>
            <tr>
                <th>Agent total commission</th>
                <td><?php echo number_format($totalPercentageAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Total commission Percentage</th>
                <td><?php echo number_format($percentageOfTotal, 2); ?>%</td>
            </tr>
        </table>

        <h3>Customers and Their Bookings</h3>
        <?php if (empty($bookings)): ?>
        <h3 class="text-center">No customers or bookings found</h3>
        <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Agent commission</th>
                    <th>Agent Percentage</th>
                    <th>Site Name</th>
                    <th>Buying Sqft</th>
                    <th>Total Price</th>
                    <th>Buying Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['userName']; ?></td>
                    <td><?php echo $booking['phoneNumber']; ?></td>
                    <td><?php echo number_format($booking['persantageAmount'], 2); ?></td>
                    <td><?php echo $booking['persantage']; ?>%</td>
                    <td><?php echo $booking['site_name']; ?></td>
                    <td><?php echo $booking['Buying_Sqft']; ?></td>
                    <td><?php echo number_format($booking['totalBuyingPrice'], 2); ?></td>
                    <td><?php echo $booking['buyingDate']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <h3>EMI Commissions</h3>
            <form method="get" class="form-inline mb-3">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($agent_id); ?>">
                <div class="form-group mr-2">
                    <label for="emi_month">Month:</label>
                    <select name="emi_month" id="emi_month" class="form-control mx-2">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php if ($selected_month == $m) echo 'selected'; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="emi_year">Year:</label>
                    <select name="emi_year" id="emi_year" class="form-control mx-2">
                        <option value="">All</option>
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php if ($selected_year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <?php if (empty($display_emis)): ?>
                <h3 class="text-center">No EMI commissions found.</h3>
            <?php else: ?>
                <?php foreach ($display_emis as $year => $months): ?>
                    <?php foreach ($months as $month => $emis_in_month): ?>
                        <h5 class="mt-4 mb-2">EMI Commissions for <?php echo date('F Y', strtotime("$year-$month-01")); ?></h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Site Name</th>
                                    <th>Site No</th>
                                    <th>EMI ID</th>
                                    <th>Payment Month</th>
                                    <th>Agent Commission (%)</th>
                                    <th>Agent Commission Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $month_total = 0; ?>
                                <?php foreach ($emis_in_month as $emi): ?>
                                    <tr>
                                        <td><?php echo $emi['userName']; ?></td>
                                        <td><?php echo $emi['site_name']; ?></td>
                                        <td><?php echo $emi['siteno']; ?></td>
                                        <td><?php echo $emi['emi_ids']; ?></td>
                                        <td><?php echo date('F Y', strtotime("{$emi['payment_year']}-{$emi['payment_month']}-01")); ?></td>
                                        <td><?php echo number_format((float)($emi['agent_commission_percent'] ?? 0), 2); ?></td>
                                        <td><?php echo number_format((float)($emi['agent_commission_amount'] ?? 0), 2); ?></td>
                                    </tr>
                                    <?php $month_total += (float)($emi['agent_commission_amount'] ?? 0); ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-right mb-3">
                            <strong>Total EMI Commission for <?php echo date('F Y', strtotime("$year-$month-01")); ?>:</strong>
                            <span class="badge badge-success">₹<?php echo number_format($month_total, 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>