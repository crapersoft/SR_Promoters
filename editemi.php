<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$emiId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($emiId > 0) {
    // Query to get the existing EMI details using mysqli prepared statements
    $query = 'SELECT * FROM emis WHERE emi_ids = ?';
    $stmt = $connection->prepare($query);

    if ($stmt === false) {
        // Handle error in preparing the statement
        die('MySQL prepare error: ' . $connection->error);
    }

    $stmt->bind_param('i', $emiId);  // 'i' stands for integer
    $stmt->execute();

    $result = $stmt->get_result();
    $emiData = $result->fetch_assoc();

    if ($emiData) {
        $bookingId = $emiData['booking_id'];
        $siteId = $emiData['site_id'];
        $emiPlan = $emiData['emi_plan'];
        $startDate = $emiData['startDate'];
        $endDate = $emiData['endDate'];
        $totalPayable = $emiData['total_payable'];
        $monthlyPayable = $emiData['monthly_payable'];

        $agentCommission = $emiData['agentCommission'];
        $agentCommissionAmount = $emiData['agentCommissionAmount'];

        // Now get the customer name from the 'user' table based on the 'booking_id'
        $queryBooking = 'SELECT b.bookingid, u.userName, b.customer_id 
                         FROM booking b
                         JOIN user u ON b.customer_id = u.user_id
                         WHERE b.bookingid = ?';
        $stmtBooking = $connection->prepare($queryBooking);

        if ($stmtBooking === false) {
            // Handle error in preparing the statement
            die('MySQL prepare error: ' . $connection->error);
        }

        $stmtBooking->bind_param('i', $bookingId);
        $stmtBooking->execute();

        $resultBooking = $stmtBooking->get_result();
        $bookingData = $resultBooking->fetch_assoc();

        if ($bookingData) {
            $customerName = $bookingData['userName'];
        } else {
            $customerName = 'Customer not found';  // Handle case where customer is not found
        }
    } else {
        // Handle case where EMI data is not found
        header('Location: ./error.php');  // Redirect to an error page
        die();
    }
} else {
    // Redirect to an error page if no valid emiId is provided
    header('Location: ./error.php');
    die();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Bookings | SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?php include './include/sidebar.php' ?>
        <!-- Sidebar End -->

        <!-- Main wrapper -->
        <div class="body-wrapper">

            <!-- Header Start -->
            <?php include './include/header.php' ?>
            <!-- Header End -->

            <div class="container-fluid">

                <!-- Form Start -->
                <form action="./service/editEmiService.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header my-3">
                            <h5 class="modal-title" id="editModalLabel">Edit EMI</h5>
                        </div>
                        <div class="modal-body">
                            <!-- Customer Name Display -->
                            <div class="mb-3">
                                <label for="customerName" class="form-label">Customer Name:</label>
                                <input type="text" id="customerName" class="form-control"
                                    value="<?php echo htmlspecialchars($customerName); ?>" readonly>
                                <input type="hidden" id="selectedBookingId" name="bookingId"
                                    value="<?php echo $bookingId; ?>">
                                <input type="hidden" id="emi_ids" name="emi_ids" value="<?php echo $emiId; ?>">
                                <!-- Hidden EMI ID -->
                            </div>

                            <input type="hidden" id="siteId" name="siteId" value="<?php echo $siteId; ?>">

                            <div class="mb-3">
                                <label for="emiPlan" class="form-label">EMI Plan in Months</label>
                                <input type="number" class="form-control" id="emiPlan" name="emiPlan"
                                    value="<?php echo $emiPlan; ?>" min="2" max="36" required>
                            </div>

                            <div class="mb-3">
                                <label for="totalPayable" class="form-label">Total Payable Amount</label>
                                <input type="number" class="form-control" id="totalPayable" name="totalPayable" min="0"
                                    value="<?php echo $totalPayable; ?>" readonly>
                            </div>

                            <!-- Agent Commission  -->
                            <div class="mb-3">
                                <label for="agentCommission" class="form-label">Agent Commission</label>
                                <input type="text" class="form-control" id="agentCommission" name="agentCommission"
                                    value="<?php echo $agentCommission; ?>" required>
                            </div>

                            <!-- Agent Commission Amount -->
                            <div class="mb-3">
                                <label for="agentCommissionAmount" class="form-label">Agent Commission
                                    Amount</label>
                                <input type="text" class="form-control" id="agentCommissionAmount"
                                    name="agentCommissionAmount" value="<?php echo $agentCommissionAmount; ?>" required
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label for="monthlyPayable" class="form-label">Monthly Payable Amount</label>
                                <input type="number" class="form-control" id="monthlyPayable" name="monthlyPayable"
                                    min="0" value="<?php echo $monthlyPayable; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="startDate" class="form-label">EMI Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate"
                                    value="<?php echo $startDate; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="endDate" class="form-label">EMI End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate"
                                    value="<?php echo $endDate; ?>" readonly>
                            </div>
                        </div>
                        <div class="modal-footer gap-3">
                            <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='emi.php';">Close</button>
                            <button type="submit" class="btn btn-primary">Update Changes</button>
                        </div>
                    </div>
                </form>


            </div>

            <div class="py-6 px-6 text-center">
                <p class="mb-0 fs-4">Design and Developed by <a href="https://crapersoft.com/" target="_blank"
                        class="pe-1 text-success">Crapersoft</a></p>
            </div>

        </div>
    </div>

    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>

    <script>
    const myApp = {
        searchCustomer: function() {
            const searchTerm = document.getElementById("searchCustomer").value;
            const suggestionsList = document.getElementById("customerSuggestions");
            const loadingSpinner = document.getElementById("customerLoading");

            if (searchTerm.trim().length === 0) {
                suggestionsList.style.display = 'none';
                loadingSpinner.style.display = 'none';
                return;
            }

            loadingSpinner.style.display = 'block';

            fetch(`search_booking.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';
                    data.forEach(customer => {
                        const listItem = document.createElement('li');
                        listItem.className = 'list-group-item';
                        const customerDisplay =
                            `${customer.userName} - ${customer.site_name} - ${customer.Buying_Sqft} sqft - Agent: ${customer.agent_name}`;
                        listItem.textContent = customerDisplay;
                        listItem.onclick = () => myApp.selectCustomer(
                            customerDisplay,
                            customer.bookingid,
                            customer.totalBuyingPrice,
                            customer.siteId // Pass siteId here
                        );
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                    loadingSpinner.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching customer data:', error);
                    loadingSpinner.style.display = 'none';
                });
        },

        selectCustomer: function(customerDisplay, bookingId, totalBuyingPrice, siteId) {
            document.getElementById("searchCustomer").value = customerDisplay;
            document.getElementById("selectedBookingId").value = bookingId;
            document.getElementById("totalPayable").value = totalBuyingPrice;
            document.getElementById("siteId").value = siteId;

            myApp.updateMonthlyPayable();
        },

        updateMonthlyPayable: function() {
            const totalBuyingPrice = parseFloat(document.getElementById("totalPayable").value) || 0;
            const emiPlan = parseInt(document.getElementById("emiPlan").value) || 36;

            if (emiPlan >= 2 && emiPlan <= 36) {
                let monthlyPayable = totalBuyingPrice / emiPlan;

                // Ensure a minimum monthly payable of Rs 3000
                if (monthlyPayable < 3000) {
                    monthlyPayable = 3000;
                }

                document.getElementById("monthlyPayable").value = Math.round(monthlyPayable);

                // Calculate and set the end date
                const startDateValue = document.getElementById("startDate").value || new Date().toISOString()
                    .split('T')[0];
                const startDate = new Date(startDateValue);
                startDate.setMonth(startDate.getMonth() + emiPlan);
                document.getElementById("endDate").value = startDate.toISOString().split('T')[0];

                // Update agent commission amount dynamically
                myApp.updateAgentCommissionAmount();
            }
        },

        updateAgentCommissionAmount: function() {
            const monthlyPayable = parseFloat(document.getElementById("monthlyPayable").value) || 0;
            const agentCommission = parseFloat(document.getElementById("agentCommission").value) || 0;

            // Calculate agent commission amount based on the percentage
            const agentCommissionAmount = (agentCommission / 100) * monthlyPayable;

            // Update the agent commission amount field
            document.getElementById("agentCommissionAmount").value = agentCommissionAmount.toFixed(2);
        },

        setDefaultStartDate: function() {
            const currentDate = new Date().toISOString().split('T')[0];
            document.getElementById("startDate").value = currentDate;
        }
    };

    // Attach dynamic listeners
    document.getElementById("emiPlan").addEventListener("input", myApp.updateMonthlyPayable);
    document.getElementById("startDate").addEventListener("change", myApp.updateMonthlyPayable);
    document.getElementById("agentCommission").addEventListener("input", myApp.updateAgentCommissionAmount);

    // Set the default start date when the page loads
    document.addEventListener("DOMContentLoaded", myApp.setDefaultStartDate);
    </script>

</body>

</html>