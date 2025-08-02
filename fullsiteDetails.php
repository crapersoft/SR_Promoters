<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$bookingId = $_GET['id'];

// Fetch booking details
$query_booking = "SELECT * FROM booking WHERE site_id = '" . $bookingId . "'";
$result_booking = mysqli_query($connection, $query_booking);
$bookings = mysqli_fetch_all($result_booking, MYSQLI_ASSOC);

// Fetch site details
$site_details = [];
foreach ($bookings as $booking) {
    $query_site = "SELECT * FROM site WHERE id = '" . $booking['site_id'] . "'";
    $result_site = mysqli_query($connection, $query_site);
    $site_details[$booking['site_id']] = mysqli_fetch_assoc($result_site);
}

// Fetch user details using customer_id from booking
if (!empty($bookings)) {
    $user_id = $bookings[0]['customer_id']; // Get the customer_id from the first booking record

    $query_user = "SELECT * FROM user WHERE user_id = '" . $user_id . "'";
    $result_user = mysqli_query($connection, $query_user);
    $user = mysqli_fetch_assoc($result_user);
}

// Fetch agent details
$query_agent = "SELECT * FROM agent WHERE agent_id = '" . $user['agent_id'] . "'";
$result_agent = mysqli_query($connection, $query_agent);
$agent = mysqli_fetch_assoc($result_agent);

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

        <h3>Site Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Site Name</th>
                <td><?php echo $site_details[$booking['site_id']]['site_name']; ?></td>
            </tr>
            <tr>
                <th>Total Sqft</th>
                <td><?php echo $site_details[$booking['site_id']]['total_sqft']; ?></td>
            </tr>
            <tr>
                <th>Price per Sqft</th>
                <td><?php echo $site_details[$booking['site_id']]['price_per_sqft']; ?></td>
            </tr>
            <tr>
                <th>Total Price Sqft</th>
                <td><?php echo $site_details[$booking['site_id']]['total_price_sqft']; ?></td>
            </tr>
            <tr>
                <th>Site Address</th>
                <td><?php echo $site_details[$booking['site_id']]['site_address']; ?></td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo $site_details[$booking['site_id']]['created_at']; ?></td>
            </tr>
            <tr>
                <th>Booked Sqft</th>
                <td><?php echo $site_details[$booking['site_id']]['bookedSqft']; ?></td>
            </tr>
            <tr>
                <th>Available Sqft</th>
                <td><?php echo $site_details[$booking['site_id']]['avaliableSqft']; ?></td>
            </tr>
        </table>

        <h3>Details</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Agent Name</th>
                    <th>Buying Sqft</th>
                    <th>Total Price</th>
                    <th>Buying Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $user['userName']; ?></td>
                    <td><?php echo $agent['agent_name']; ?></td>
                    <td><?php echo $booking['Buying_Sqft']; ?></td>
                    <td><?php echo $booking['totalBuyingPrice']; ?></td>
                    <td><?php echo $booking['buyingDate']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
