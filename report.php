<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Fetch all agents
$sql_agents = 'SELECT * FROM agent';
$result_agents = mysqli_query($connection, $sql_agents);
$agents = mysqli_num_rows($result_agents) > 0 ? mysqli_fetch_all($result_agents, MYSQLI_ASSOC) : [];

// Fetch all users
$sql_users = 'SELECT * FROM user';
$result_users = mysqli_query($connection, $sql_users);
$users = mysqli_num_rows($result_users) > 0 ? mysqli_fetch_all($result_users, MYSQLI_ASSOC) : [];

// Fetch all sites
$sql_sites = 'SELECT * FROM site';
$result_sites = mysqli_query($connection, $sql_sites);
$sites = mysqli_num_rows($result_sites) > 0 ? mysqli_fetch_all($result_sites, MYSQLI_ASSOC) : [];

// Fetch all bookings
$sql_bookings = 'SELECT * FROM booking';
$result_bookings = mysqli_query($connection, $sql_bookings);
$bookings = mysqli_num_rows($result_bookings) > 0 ? mysqli_fetch_all($result_bookings, MYSQLI_ASSOC) : [];

// // Fetch all agentCommission
// $sql_agents = "SELECT agent.agent_name, 
//                       DATE_FORMAT(payment.payment_date, '%d/%m/%Y') AS payment_date, 
//                       payment.paid_amount, 
//                       payment.agent_commison  
//                FROM payment 
//                LEFT JOIN user ON user.user_id = payment.customer_id 
//                LEFT JOIN agent ON agent.agent_id = user.agent_id";


// $result_agents = mysqli_query($connection, $sql_agents);

// // Check if query failed
// if (!$result_agents) {
//     die("Query failed: " . mysqli_error($connection)); // Debugging
// }

// $agent_commissions = mysqli_fetch_all($result_agents, MYSQLI_ASSOC);

$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$sql_agents = "SELECT agent.agent_name, 
                      DATE_FORMAT(payment.payment_date, '%d/%m/%Y') AS payment_date, 
                      payment.paid_amount, 
                      payment.agent_commison  ,
                      payment.site_id
               FROM payment 
               LEFT JOIN user ON user.user_id = payment.customer_id
               LEFT JOIN agent ON agent.agent_id = user.agent_id";

if (!empty($from_date) && !empty($to_date)) {
    $sql_agents .= " WHERE payment.payment_date BETWEEN '$from_date' AND '$to_date'";
}
$sql_agents .= " ORDER BY payment.payment_date DESC";

$result_agents = mysqli_query($connection, $sql_agents);

if (!$result_agents) {
    die("Query failed: " . mysqli_error($connection));
}

$agent_commissions = mysqli_fetch_all($result_agents, MYSQLI_ASSOC);
 
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report | SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?php include './include/sidebar.php' ?>
        <!-- Sidebar End -->

        <!-- Main Wrapper -->
        <div class="body-wrapper">

            <!-- Header Start -->
            <?php include './include/header.php' ?>
            <!-- Header End -->

            <!-- WIDGETS -->
            <div class="container-fluid">
                <!-- Tabs for Reports -->
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily"
                            type="button" role="tab" aria-controls="daily" aria-selected="true" 
                            onclick="setActiveTab('daily')">User</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly"
                            type="button" role="tab" aria-controls="monthly" aria-selected="false" 
                            onclick="setActiveTab('monthly')">Agent</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="site-tab" data-bs-toggle="tab" data-bs-target="#site" type="button"
                            role="tab" aria-controls="site" aria-selected="false" 
                            onclick="setActiveTab('site')">Site</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking"
                            type="button" role="tab" aria-controls="booking" aria-selected="false"  
                            onclick="setActiveTab('booking')">Bookings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent"
                            type="button" role="tab" aria-controls="agent" aria-selected="false" 
                            onclick="setActiveTab('agent')">Agent Commission</button>
                    </li>
                </ul>


                <!-- Tab Content -->
                <div class="tab-content mt-4" id="reportTabContent">

                    <!-- Users -->
                    <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                        <h4 class="text-center mb-3">Users</h4>
                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="searchDaily" class="form-control" placeholder="Search by User"
                                onkeyup="searchTable('searchDaily', 'usersTable')">
                        </div>
                        <!-- Users Table -->
                        <table class="table table-striped" id="usersTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sno = 1;
                                foreach ($users as $user):
                                    ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['userName']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phoneNumber']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                                    <td><a href="fullDetails.php?id=<?php echo $user['user_id']; ?>"
                                            class="text-primary me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                    <!-- Agents -->
                    <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                        <h4 class="text-center mb-3">Agents</h4>
                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="searchMonthly" class="form-control" placeholder="Search by Agent"
                                onkeyup="searchTable('searchMonthly', 'agentsTable')">
                        </div>
                        <!-- Agents Table -->
                        <table class="table table-striped" id="agentsTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Agent ID</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Aadhar No</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1;
                                foreach ($agents as $agent): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($agent['agent_id']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['agent_name']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['emil_id']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['agent_address']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['agent_aadhar_no']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['status']); ?></td>
                                    <td><?php echo htmlspecialchars($agent['created_at']); ?></td>
                                    <td><a href="agentFullReport.php?id=<?php echo $agent['agent_id']; ?>"
                                            class="text-primary me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sites -->
                    <div class="tab-pane fade" id="site" role="tabpanel" aria-labelledby="site-tab">
                        <h4 class="text-center mb-3">Sites</h4>
                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="searchSite" class="form-control" placeholder="Search by Site"
                                onkeyup="searchTable('searchSite', 'sitesTable')">
                        </div>
                        <!-- Sites Table -->

                        <table class="table table-striped" id="sitesTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Site Name</th>
                                    <th>Total Sqft</th>
                                    <th>Price 1/Sqft</th>
                                    <th>Total Price</th>
                                    <th>Site Address</th>
                                    <th>Available Sqft</th>
                                    <th>sold out Sqft</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1;
                                foreach ($sites as $site): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($site['site_name']); ?></td>
                                    <td><?php echo htmlspecialchars($site['total_sqft']); ?></td>
                                    <td><?php echo htmlspecialchars($site['price_per_sqft']); ?></td>
                                    <td><?php echo htmlspecialchars($site['total_price_sqft']); ?></td>
                                    <td><?php echo htmlspecialchars($site['site_address']); ?></td>
                                    <td><?php echo htmlspecialchars($site['avaliableSqft']); ?></td>
                                    <td><?php echo htmlspecialchars($site['bookedSqft']); ?></td>
                                    <td><a href="fullsiteDetails.php?id=<?php echo $site['id']; ?>"
                                            class="text-primary me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bookings -->
                    <div class="tab-pane fade" id="booking" role="tabpanel" aria-labelledby="booking-tab">
                        <h4 class="text-center mb-3">Bookings</h4>
                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="searchBooking" class="form-control" placeholder="Search by Booking"
                                onkeyup="searchTable('searchBooking', 'bookingsTable')">
                        </div>
                        <!-- Bookings Table -->
                        <table class="table table-striped" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Customer name</th>
                                    <th>Site Name</th>
                                    <th>Site No</th>
                                    <th>Buying Sqft</th>
                                    <th>Total Price</th>
                                    <th>Agent name</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1;
                                foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td>
                                        <?php
                                        $customer_id = $booking['customer_id'];
                                        $sql = "SELECT userName FROM user WHERE user_id = $customer_id";
                                        $result = mysqli_query($connection, $sql);
                                        $customer = mysqli_fetch_assoc($result);
                                        echo htmlspecialchars($customer['userName']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $site_id = $booking['site_id'];
                                        $sql = "SELECT site_name FROM site WHERE id = $site_id";
                                        $result = mysqli_query($connection, $sql);
                                        $customer = mysqli_fetch_assoc($result);
                                        echo htmlspecialchars($customer['site_name']);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['siteno']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['Buying_Sqft']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['totalBuyingPrice']); ?></td>
                                    <td>
                                        <?php
                                        $agent_id = $booking['agent_id'];
                                        $sql = "SELECT agent_name FROM agent WHERE agent_id = $agent_id";
                                        $result = mysqli_query($connection, $sql);
                                        $customer = mysqli_fetch_assoc($result);
                                        echo htmlspecialchars($customer['agent_name']);
                                        ?>
                                    </td>
                                    <td><a href="fullBookingDetails.php?id=<?php echo $booking['bookingid']; ?>"
                                            class="text-primary me-2">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="agent" role="tabpanel" aria-labelledby="agent-tab">
                        <h4 class="text-center mb-3">Agent Commision</h4>

                        <div class="row mb-3">
                            <div class="col-md-5">
                                <input type="date" id="fromDate" class="form-control" placeholder="From Date">
                            </div>
                            <div class="col-md-5">
                                <input type="date" id="toDate" class="form-control" placeholder="To Date">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" onclick="filterByDate()">Filter</button>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="searchAgent" class="form-control" placeholder="Search by Agent"
                                onkeyup="searchTable('searchAgent', 'agentTable')">
                        </div>
                        <!-- Bookings Table -->
                        <table class="table table-striped" id="agentTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Agent name</th>
                                    <th>Site No</th>
                                    <th>Payment Date</th>
                                    <th>Commission</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $sno = 1;
                                foreach ($agent_commissions as $data): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($data['agent_name']); ?></td>
                                     <td><?php echo htmlspecialchars($data['site_id']); ?></td>
                                     <td><?php echo htmlspecialchars($data['payment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($data['agent_commison']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>

    <script>
    
    function setActiveTab(tabId) {
        localStorage.setItem('activeTab', tabId);
    }

    // Restore active tab on page load
    document.addEventListener("DOMContentLoaded", function () {
        let activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('show', 'active');
            });

            document.getElementById(`${activeTab}-tab`).classList.add('active');
            document.getElementById(activeTab).classList.add('show', 'active');
        }
    });

    function filterByDate() {
        let fromDate = document.getElementById('fromDate').value;
        let toDate = document.getElementById('toDate').value;
        
        if (fromDate && toDate) {
            let activeTab = localStorage.getItem('activeTab') || 'daily';
            window.location.href = `?from_date=${fromDate}&to_date=${toDate}`;
        } else {
            alert("Please select both dates!");
        }
    }

    function searchTable(inputId, tableId) {
        const input = document.getElementById(inputId);
        const filter = input.value.toLowerCase();
        const table = document.getElementById(tableId);
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = 'none';
            const td = tr[i].getElementsByTagName('td');
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    if (td[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                        break;
                    }
                }
            }
        }
    }
    </script>

</body>

</html>