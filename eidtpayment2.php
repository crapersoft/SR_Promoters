<?php
include ('database/database.php');
session_start();

// Check if the user is logged in and has admin role
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$emiPaymentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the existing payment details based on the EMI Payment ID
if ($emiPaymentId > 0) {
    $query = "SELECT * FROM payment WHERE id = $emiPaymentId";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        echo 'Error fetching payment data: ' . mysqli_error($connection);
        exit();
    }
    $payment = mysqli_fetch_assoc($result);

    // Fetch customer information based on the userId from the payment data
    if ($payment) {
        $userId = $payment['customer_id'];  // Assuming 'customer_id' in payment table refers to user_id in user table
        $userQuery = "SELECT * FROM user WHERE user_id = $userId";
        $userResult = mysqli_query($connection, $userQuery);
        if (!$userResult) {
            echo 'Error fetching user data: ' . mysqli_error($connection);
            exit();
        }
        $user = mysqli_fetch_assoc($userResult);
    }
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
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?php include './include/sidebar.php' ?>
        <!--  Sidebar End -->

        <div class="body-wrapper">

            <!-- Header Start -->
            <?php include './include/header.php' ?>
            <!-- Header End -->

            <div class="container-fluid">
                <!-- Form Start -->
                <form action="./service/editPayment2Service.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header my-3">
                            <h5 class="modal-title" id="editModalLabel">Edit Emi Payments</h5>
                        </div>
                        <div class="modal-body">
                            <!-- Pre-fill existing EMI data -->
                            <div class="mb-3">

                                <input type="hidden" class="form-control" name="payment_id"  value="<?php echo isset($payment['id']) ? $payment['id'] : ''; ?>"
                                    required>

                                <label for="paymentDate" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="paymentDate" name="paymentDate"
                                    value="<?php echo isset($payment['payment_date']) ? $payment['payment_date'] : ''; ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="searchCustomer" class="form-label">Customer Name:</label>
                                <input type="text" id="searchCustomer" class="form-control"
                                    value="<?php echo isset($user['userName']) ? $user['userName'] : ''; ?>"
                                    placeholder="Search for Customer..." onkeyup="myApp.searchCustomer()">
                                <div id="customerLoading" style="display:none;">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                                <ul id="customerSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                <input type="hidden" id="selectedBookingId" name="bookingId"
                                    value="<?php echo isset($payment['booking_id']) ? $payment['booking_id'] : ''; ?>">
                            </div>

                            <input type="hidden" id="siteId" name="siteId"
                                value="<?php echo isset($payment['site_id']) ? $payment['site_id'] : ''; ?>">
                            <input type="hidden" id="emiId" name="emiId"
                                value="<?php echo isset($payment['emi_id']) ? $payment['emi_id'] : ''; ?>">
                            <input type="hidden" id="userId" name="userId"
                                value="<?php echo isset($user['user_id']) ? $user['user_id'] : ''; ?>">
                            <input type="hidden" id="isEmi" name="isEmi"
                                value="<?php echo isset($payment['isEmi']) ? $payment['isEmi'] : ''; ?>">

                            <div class="mb-3">
                                <label for="totalAmount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="totalAmount" name="totalAmount" min="0"
                                    value="<?php echo isset($payment['total_amount']) ? $payment['total_amount'] : ''; ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="paymentMode" class="form-label">Mode of Payment</label>
                                <select class="form-select" id="paymentMode" name="paymentMode" required>
                                    <option value="" disabled selected>Select payment mode</option>
                                    <option value="cash"
                                        <?php echo (isset($payment['payment_mode']) && $payment['payment_mode'] == 'cash') ? 'selected' : ''; ?>>
                                        Cash</option>
                                    <option value="card"
                                        <?php echo (isset($payment['payment_mode']) && $payment['payment_mode'] == 'card') ? 'selected' : ''; ?>>
                                        Card</option>
                                    <option value="upi"
                                        <?php echo (isset($payment['payment_mode']) && $payment['payment_mode'] == 'upi') ? 'selected' : ''; ?>>
                                        UPI</option>
                                    <option value="net_banking"
                                        <?php echo (isset($payment['payment_mode']) && $payment['payment_mode'] == 'net_banking') ? 'selected' : ''; ?>>
                                        Net Banking</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Emi Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="0"
                                    value="<?php echo isset($payment['amount']) ? $payment['amount'] : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="penalty" class="form-label">Penalty in Rs</label>
                                <input type="number" class="form-control" id="penalty" name="penalty" min="0"
                                    value="<?php echo isset($payment['penalty']) ? $payment['penalty'] : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="totalAmountAndPenalty" class="form-label">Total Amount (Amount +
                                    Penalty)</label>
                                <input type="number" class="form-control" id="totalAmountAndPenalty"
                                    name="totalAmountAndPenalty"
                                    value="<?php echo isset($payment['totalAmountAndPenalty']) ? $payment['totalAmountAndPenalty'] : ''; ?>"
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label for="paidAmount" class="form-label">Paid Emi Amount</label>
                                <input type="number" class="form-control" id="paidAmount" name="paidAmount" min="0"
                                    value="<?php echo isset($payment['paid_amount']) ? $payment['paid_amount'] : ''; ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="balanceAmount" class="form-label">Balance Emi Amount</label>
                                <input type="number" class="form-control" id="balanceAmount" name="balanceAmount"
                                    min="0"
                                    value="<?php echo isset($payment['balance_amount']) ? $payment['balance_amount'] : ''; ?>"
                                    required>
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
            document.getElementById("paidAmount").value = paidAmount;
            document.getElementById("balanceAmount").value = balanceAmount;
            document.getElementById("siteId").value = siteId;
            document.getElementById("emiId").value = emiId;
            document.getElementById("userId").value = userId;
            document.getElementById("isEmi").value = isEmi;
            document.getElementById("amount").value = monthlyPayable;

            if (isEmi == 1) {
                myApp.showEmiFields();
            } else {
                myApp.hideEmiFields();
            }

            myApp.calculateTotalAmountAndPenalty();
            document.getElementById("customerSuggestions").style.display = "none";
        },

        showEmiFields: function() {
            document.getElementById("amount").parentElement.style.display = "block";
            document.getElementById("penalty").parentElement.style.display = "block";
            document.getElementById("totalAmountAndPenalty").parentElement.style.display = "block";
            document.getElementById("paidAmount").parentElement.style.display = "block";
            document.getElementById("balanceAmount").parentElement.style.display = "block";
        },

        hideEmiFields: function() {
            document.getElementById("amount").parentElement.style.display = "none";
            document.getElementById("penalty").parentElement.style.display = "none";
            document.getElementById("totalAmountAndPenalty").parentElement.style.display = "none";
            document.getElementById("paidAmount").parentElement.style.display = "none";
            document.getElementById("balanceAmount").parentElement.style.display = "none";
        },

        setCurrentDate: function() {
            const paymentDateInput = document.getElementById("paymentDate");
            const today = new Date().toISOString().split("T")[0];
            paymentDateInput.value = today;
        },

        calculateTotalAmountAndPenalty: function() {
            const amount = parseFloat(document.getElementById("amount").value) || 0;
            const penalty = parseFloat(document.getElementById("penalty").value) || 0;
            const totalAmountAndPenalty = amount + penalty;

            document.getElementById("totalAmountAndPenalty").value = totalAmountAndPenalty.toFixed(2);
        }
    };

    // Set current date on load
    document.addEventListener("DOMContentLoaded", () => {
        myApp.setCurrentDate();
    });

    // Listen to input changes on amount and penalty to calculate the total
    document.getElementById("amount").addEventListener("input", myApp.calculateTotalAmountAndPenalty);
    document.getElementById("penalty").addEventListener("input", myApp.calculateTotalAmountAndPenalty);
    </script>

</body>

</html>