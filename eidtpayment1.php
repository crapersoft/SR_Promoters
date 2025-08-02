<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$fullPaymentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$fullPaymentData = [];
if ($fullPaymentId > 0) {
    $query = 'SELECT fp.*, u.userName FROM fullpayments fp JOIN user u ON fp.customer_id = u.user_id WHERE fp.fp_id = ?';
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $fullPaymentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $fullPaymentData = $result->fetch_assoc();
    $stmt->close();
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Emi SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?php include './include/sidebar.php' ?>
        <!--  Sidebar End -->

        <!--  Main wrapper -->
        <div class="body-wrapper">

            <!--  Header Start -->
            <?php include './include/header.php' ?>
            <!--  Header End -->

            <div class="container-fluid">

                <!-- Form Start -->
                <form action="./service/editPayment1Service.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header my-3">
                            <h5 class="modal-title" id="editModalLabel">Edit Full Payments</h5>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="paymentDate" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="paymentDate" name="paymentDate" required
                                    value="<?php echo $fullPaymentData['payment_date'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="searchCustomer" class="form-label">Customer Name:</label>
                                <input type="text" id="searchCustomer" class="form-control"
                                    placeholder="Search for Customer..." onkeyup="myApp.searchCustomer()"
                                    value="<?php echo $fullPaymentData['userName'] ?? ''; ?>">
                                <div id="customerLoading" style="display:none;">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                                <ul id="customerSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                <input type="hidden" id="selectedBookingId" name="bookingId"
                                    value="<?php echo $fullPaymentData['booking_id'] ?? ''; ?>">
                            </div>

                            <input type="hidden" id="siteId" name="siteId"
                                value="<?php echo $fullPaymentData['site_id'] ?? ''; ?>">
                            <input type="hidden" id="fpId" name="fpId"
                                value="<?php echo $fullPaymentData['fp_id'] ?? ''; ?>">
                            <input type="hidden" id="userId" name="userId"
                                value="<?php echo $fullPaymentData['customer_id'] ?? ''; ?>">

                            <div class="mb-3">
                                <label for="totalAmount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="totalAmount" name="totalAmount" min="0"
                                    required value="<?php echo $fullPaymentData['payment_amount'] ?? ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="paymentMode" class="form-label">Mode of Payment</label>
                                <select class="form-select" id="paymentMode" name="paymentMode" required>
                                    <option value="" disabled>Select payment mode</option>
                                    <option value="cash"
                                        <?php echo (isset($fullPaymentData['payment_method']) && $fullPaymentData['payment_method'] == 'cash') ? 'selected' : ''; ?>>
                                        Cash</option>
                                    <option value="card"
                                        <?php echo (isset($fullPaymentData['payment_method']) && $fullPaymentData['payment_method'] == 'card') ? 'selected' : ''; ?>>
                                        Card</option>
                                    <option value="upi"
                                        <?php echo (isset($fullPaymentData['payment_method']) && $fullPaymentData['payment_method'] == 'upi') ? 'selected' : ''; ?>>
                                        UPI</option>
                                    <option value="net_banking"
                                        <?php echo (isset($fullPaymentData['payment_method']) && $fullPaymentData['payment_method'] == 'net_banking') ? 'selected' : ''; ?>>
                                        Net Banking</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer gap-3">
                            <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='allocate.php';">Close</button>
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
    </div>
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
    const myApp = {
        searchCustomer: function() {
            const searchTerm = document.getElementById("searchCustomer").value;
            const suggestionsList = document.getElementById("customerSuggestions");

            if (searchTerm.trim().length === 0) {
                suggestionsList.style.display = "none";
                return;
            }

            fetch(`searchForPayment.php?query=${searchTerm}`)
                .then((response) => response.json())
                .then((data) => {
                    suggestionsList.innerHTML = "";
                    data.forEach((customer) => {
                        const listItem = document.createElement("li");
                        listItem.className = "list-group-item";
                        const customerDisplay =
                            `${customer.userName} - ${customer.site_name} - ${customer.Buying_Sqft} sqft - Agent: ${customer.agent_name}`;
                        listItem.textContent = customerDisplay;
                        listItem.onclick = () =>
                            myApp.selectCustomer(
                                customerDisplay,
                                customer.bookingid,
                                customer.totalBuyingPrice,
                                customer.siteId,
                                customer.paid_amount,
                                customer.balance_amount,
                                customer.monthly_payable,
                                customer.isEmi,
                                customer.emi_id,
                                customer.userId
                            );
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = "block";
                })
                .catch((error) => {
                    console.error("Error fetching customer data:", error);
                });
        },

        selectCustomer: function(
            customerDisplay,
            bookingId,
            totalBuyingPrice,
            siteId,
            paidAmount,
            balanceAmount,
            monthlyPayable,
            isEmi,
            emiId,
            userId
        ) {
            document.getElementById("searchCustomer").value = customerDisplay;
            document.getElementById("selectedBookingId").value = bookingId;
            document.getElementById("totalAmount").value = totalBuyingPrice;
            document.getElementById("siteId").value = siteId;
            document.getElementById("fpId").value = emiId;
            document.getElementById("userId").value = userId;

            document.getElementById("customerSuggestions").style.display = "none";
        }
    };

    document.getElementById("addModal").addEventListener("shown.bs.modal", () => {
        myApp.setCurrentDate();
    });
    </script>

</body>

</html>