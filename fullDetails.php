<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$user_id = $_GET['id'];

// Fetch user details
$query_user = "SELECT * FROM user WHERE user_id = '" . $user_id . "'";
$result_user = mysqli_query($connection, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Fetch agent details
$query_agent = "SELECT * FROM agent WHERE agent_id = '" . $user['agent_id'] . "'";
$result_agent = mysqli_query($connection, $query_agent);
$agent = mysqli_fetch_assoc($result_agent);

// Fetch booking details
$query_booking = "SELECT * FROM booking WHERE customer_id = '" . $user_id . "'";
$result_booking = mysqli_query($connection, $query_booking);
$bookings = mysqli_fetch_all($result_booking, MYSQLI_ASSOC);

// Fetch site details
$site_details = [];
foreach ($bookings as $booking) {
    $query_site = "SELECT * FROM site WHERE id = '" . $booking['site_id'] . "'";
    $result_site = mysqli_query($connection, $query_site);
    $site_details[$booking['site_id']] = mysqli_fetch_assoc($result_site);
}

// Fetch EMI and payment details
$emi_details = [];
$fullpayment_details = [];
$payment_details = [];

foreach ($bookings as $booking) {
    if ($booking['isEmi'] == 1) {
        // Fetch EMI details for EMI-based bookings
        $query_emi = "SELECT * FROM emis WHERE booking_id = '" . $booking['bookingid'] . "'";
        $result_emi = mysqli_query($connection, $query_emi);
        $emi_details[$booking['bookingid']] = mysqli_fetch_assoc($result_emi);

        // Fetch payment history for EMI-based bookings
        $query_payment = "SELECT * FROM payment WHERE booking_id = '" . $booking['bookingid'] . "'";
        $result_payment = mysqli_query($connection, $query_payment);
        $payment_details[$booking['bookingid']] = mysqli_fetch_all($result_payment, MYSQLI_ASSOC);
    } else {
        // Fetch full payment details for non-EMI bookings
        $query_fullpayment = "SELECT * FROM fullpayments WHERE booking_id = '" . $booking['bookingid'] . "'";
        $result_fullpayment = mysqli_query($connection, $query_fullpayment);
        $fullpayment_details[$booking['bookingid']] = mysqli_fetch_assoc($result_fullpayment);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details</title>
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
        <h3>Customer Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td><?php echo $user['userName']; ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo $user['phoneNumber']; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $user['email']; ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo $user['address']; ?></td>
            </tr>
            <tr>
                <th>Aadhar Number</th>
                <td><?php echo $user['aadharNumber']; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo $user['status']; ?></td>
            </tr>
        </table>

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
        </table>

        <h3>Booking Details</h3>
        <?php foreach ($bookings as $booking): ?>
        <table class="table table-bordered">
            <tr>
                <th>Booking ID</th>
                <td><?php echo $booking['bookingid']; ?></td>
            </tr>
            <tr>
                <th>Site Name</th>
                <td><?php echo $site_details[$booking['site_id']]['site_name']; ?></td>
            </tr>
            <tr>
                <th>Buying Sqft</th>
                <td><?php echo $booking['Buying_Sqft']; ?></td>
            </tr>
            <tr>
                <th>Total Price</th>
                <td><?php echo $booking['totalBuyingPrice']; ?></td>
            </tr>
            <tr>
                <th>Buying Date</th>
                <td><?php echo $booking['buyingDate']; ?></td>
            </tr>
        </table>

        <?php if ($booking['isEmi'] == 1): ?>
        <h3>EMI Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>EMI Plan</th>
                <td><?php echo $emi_details[$booking['bookingid']]['emi_plan']; ?></td>
            </tr>
            <tr>
                <th>Total Payable</th>
                <td><?php echo $emi_details[$booking['bookingid']]['total_payable']; ?></td>
            </tr>
            <tr>
                <th>Monthly Payable</th>
                <td><?php echo $emi_details[$booking['bookingid']]['monthly_payable']; ?></td>
            </tr>
            <tr>
                <th>Balance Amount</th>
                <td><?php echo $emi_details[$booking['bookingid']]['balance_amount']; ?></td>
            </tr>
            <tr>
                <th>Next Due Date</th>
                <td><?php echo $emi_details[$booking['bookingid']]['nextDueDate']; ?></td>
            </tr>
        </table>

        <h3>Payment History</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Penalty</th>
                    <th>Total Amount</th>
                    <th>Payment Mode</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payment_details[$booking['bookingid']] as $payment): ?>
                <tr>
                    <td><?php echo $payment['payment_date']; ?></td>
                    <td><?php echo $payment['amount']; ?></td>
                    <td><?php echo $payment['penalty']; ?></td>
                    <td><?php echo $payment['total_amount']; ?></td>
                    <td><?php echo $payment['payment_mode']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <h3>Full Payment Details</h3>
        <table class="table table-bordered">
            <?php if (empty($fullpayment_details[$booking['bookingid']])): ?>
            <tr>
                <td colspan="2">Payment not done</td>
            </tr>
            <?php else: ?>
            <tr>
                <th>Payment Date</th>
                <td><?php echo $fullpayment_details[$booking['bookingid']]['payment_date']; ?></td>
            </tr>
            <tr>
                <th>Amount</th>
                <td><?php echo $fullpayment_details[$booking['bookingid']]['payment_amount']; ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?php echo $fullpayment_details[$booking['bookingid']]['payment_method']; ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
