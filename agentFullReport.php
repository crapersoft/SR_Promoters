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
        e.total_payment_commissions,
        b.customer_id,
        b.site_id,
        b.siteno,
        u.userName,
        s.site_name,
        p.payment_date,
        MONTH(p.payment_date) AS payment_month,
        YEAR(p.payment_date) AS payment_year
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
        <?php if (empty($emis)): ?>
        <h3 class="text-center">No EMI commissions found</h3>
        <?php else: ?>
        <table class="table table-bordered">
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Site Name</th>
            <th>Site No</th>
            <th>EMI ID</th>
            <th>Payment Month</th>
            <th>Agent Commission</th>
            <th>Agent Commission Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Assuming $emis is fetched from the database as an array
        $current_month = null;
        $current_year = null;

        foreach ($emis as $emi): 
            // Get month and year from the result
            $payment_month = $emi['payment_month'];
            $payment_year = $emi['payment_year'];

            // If the current year or month changes, add a new row for the new month
            if ($payment_year != $current_year || $payment_month != $current_month) {
                // Update current month and year
                $current_month = $payment_month;
                $current_year = $payment_year;
        ?>
        
        <!-- Displaying the Month and Year once -->
        <tr class="month-header">
            <td colspan="7" style="background-color: #f2f2f2; font-weight: bold;">
                <?php echo date('F Y', strtotime("$payment_year-$payment_month-01")); ?>
            </td>
        </tr>
        
        <?php } ?>

        <!-- Now display the actual commission data for each row -->
        <tr>
            <td><?php echo $emi['userName']; ?></td>
            <td><?php echo $emi['site_name']; ?></td>
            <td><?php echo $emi['siteno']; ?></td>
            <td><?php echo $emi['emi_ids']; ?></td>
            <td><?php echo date('F Y', strtotime("$emi[payment_year]-$emi[payment_month]-01")); ?></td>
            <td><?php echo number_format($emi['agentCommission'], 2); ?></td>
            <td><?php echo number_format($emi['total_payment_commissions'], 2); ?></td>
        </tr>

        <?php endforeach; ?>
    </tbody>
</table>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>