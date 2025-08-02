<?php
include ('database/database.php');
session_start();
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}
$sql = 'SELECT * FROM payment';
$sql .= " ORDER BY payment.payment_date DESC";

$result = mysqli_query($connection, $sql);
if (mysqli_num_rows($result) > 0) {
    $payments = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $payments = [];
}

$sqlFullPayment = 'SELECT * FROM fullpayments';
$sqlFullPayment .= " ORDER BY fullpayments.payment_date DESC";
$resultFullPayment = mysqli_query($connection, $sqlFullPayment);
if (mysqli_num_rows($resultFullPayment) > 0) {
    $fullPayments = mysqli_fetch_all($resultFullPayment, MYSQLI_ASSOC);
} else {
    $fullPayments = [];
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment | SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?php include './include/sidebar.php'; ?>

        <div class="body-wrapper">
            <?php include './include/header.php'; ?>
            <div class="container-fluid">

                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus me-2"></i>
                    Add Payment
                </button>

                <!-- Add  Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">EMI | Full Payment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="./service/addPaymentService.php">
                                    <div class="mb-3">
                                        <label for="paymentDate" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" id="paymentDate" name="paymentDate"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="searchCustomer" class="form-label">Customer Name:</label>
                                        <input type="text" id="searchCustomer" class="form-control"
                                            placeholder="Search for Customer..." onkeyup="myApp.searchCustomer()">
                                        <div id="customerLoading" style="display:none;">
                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                        </div>
                                        <ul id="customerSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedBookingId" name="bookingId">
                                    </div>

                                    <input type="hidden" id="siteId" name="siteId">
                                    <input type="hidden" id="emiId" name="emiId">
                                    <input type="hidden" id="userId" name="userId">
                                    <input type="hidden" id="isEmi" name="isEmi"> <!-- New hidden input for isEmi -->

                                    <div class="mb-3">
                                        <label for="totalAmount" class="form-label">Total Amount</label>
                                        <input type="number" class="form-control" id="totalAmount" name="totalAmount"
                                            min="0" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="paymentMode" class="form-label">Mode of Payment</label>
                                        <select class="form-select" id="paymentMode" name="paymentMode" required>
                                            <option value="" disabled selected>Select payment mode</option>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="upi">UPI</option>
                                            <option value="net_banking">Net Banking</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Emi Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount" min="0">
                                    </div>

                                    <div class="mb-3">
                                        <label for="penalty" class="form-label">Penalty in Rs</label>
                                        <input type="number" class="form-control" id="penalty" name="penalty" min="0">
                                    </div>

                                    <div class="mb-3">
                                        <label for="totalAmountAndPenalty" class="form-label">Total Amount (Amount +
                                            Penalty)</label>
                                        <input type="number" class="form-control" id="totalAmountAndPenalty"
                                            name="totalAmountAndPenalty" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="paidAmount" class="form-label">Paid Emi Amount</label>
                                        <input type="number" class="form-control" id="paidAmount" name="paidAmount"
                                            min="0">
                                    </div>

                                    <div class="mb-3">
                                        <label for="balanceAmount" class="form-label">Balance Emi Amount</label>
                                        <input type="number" class="form-control" id="balanceAmount"
                                            name="balanceAmount" min="0">
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Full Payment Modal -->
                <div class="modal fade" id="deleteFullPaymentModal" tabindex="-1"
                    aria-labelledby="deleteFullPaymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteFullPaymentModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this full payment?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a id="confirmDeleteFullPaymentButton" href="#" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                const deleteFullPaymentModal = document.getElementById('deleteFullPaymentModal');
                deleteFullPaymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget; // Button that triggered the modal
                    const fullPaymentId = button.getAttribute(
                        'data-full_payment_id'); // Extract payment ID from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteFullPaymentButton');

                    // Update the link with the payment ID
                    confirmDeleteButton.href =
                        `./service/deleteFullPaymentService.php?paymentId=${fullPaymentId}`;
                });
                </script>

                <!-- Delete EMI Payment Modal -->
                <div class="modal fade" id="deleteEMIPaymentModal" tabindex="-1"
                    aria-labelledby="deleteEMIPaymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteEMIPaymentModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this EMI payment?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a id="confirmDeleteEMIPaymentButton" href="#" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                const deleteEMIPaymentModal = document.getElementById('deleteEMIPaymentModal');
                deleteEMIPaymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget; // Button that triggered the modal
                    const emiPaymentId = button.getAttribute(
                        'data-emi_payment_id'); // Extract payment ID from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteEMIPaymentButton');

                    // Update the link with the payment ID
                    confirmDeleteButton.href = `./service/deletePaymentService.php?paymentId=${emiPaymentId}`;
                });
                </script>

                <!-- Tabs for Reports -->
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="full-payment-tab" data-bs-toggle="tab"
                            data-bs-target="#full-payment" type="button" role="tab" aria-controls="full-payment"
                            aria-selected="true">Full Payment</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="emi-payment-tab" data-bs-toggle="tab" data-bs-target="#emi-payment"
                            type="button" role="tab" aria-controls="emi-payment" aria-selected="false">EMI
                            Payment</button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="reportTabContent">
                    <!-- Full payment table  -->
                    <div class="card w-100 tab-pane fade show active" id="full-payment" role="tabpanel"
                        aria-labelledby="full-payment-tab">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-semibold mb-4">Full Payment Transactions</h5>
                            <!-- Search Bar -->
                            <div class="mb-3">
                                <input type="text" id="searchFullPaymentsTable" class="form-control"
                                    placeholder="Search by Payments Details"
                                    onkeyup="searchTable('searchFullPaymentsTable', 'fullPaymentsTable')">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle"
                                    id="fullPaymentsTable">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Customer Name</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Site Name</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0"> Date</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Payment Mode</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Action</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($fullPayments)): ?>
                                        <?php foreach ($fullPayments as $fullPayment): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $customer_id = $fullPayment['customer_id'];
                                                $sql = "SELECT userName FROM user WHERE user_id = $customer_id";
                                                $result = mysqli_query($connection, $sql);
                                                $customer = mysqli_fetch_assoc($result);
                                                echo htmlspecialchars($customer['userName']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $site_id = $fullPayment['site_id'];
                                                $sql = "SELECT site_name FROM site WHERE id = $site_id";
                                                $result = mysqli_query($connection, $sql);
                                                $siteName = mysqli_fetch_assoc($result);
                                                echo htmlspecialchars($siteName['site_name']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($fullPayment['payment_date']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($fullPayment['payment_amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($fullPayment['payment_method']); ?>
                                            </td>
                                            <td>
                                                <a href="eidtpayment1.php?id=<?php echo $fullPayment['fp_id']; ?>"
                                                    class="text-primary me-2">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a href="generate_pdf1.php?payment_id=<?php echo htmlspecialchars($fullPayment['fp_id']); ?>"
                                                    class="text-primary me-2">
                                                    <i class="bi bi-printer"></i>
                                                </a>

                                                <a href="#" class="text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteFullPaymentModal"
                                                    data-full_payment_id="<?php echo $fullPayment['fp_id']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="13" class="text-center">No transactions found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- EMI payment table  -->
                    <div class="card w-100 tab-pane fade" id="emi-payment" role="tabpanel"
                        aria-labelledby="emi-payment-tab">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-semibold mb-4">EMI Payment Transactions</h5>
                            <!-- Search Bar -->
                            <div class="mb-3">
                                <input type="text" id="searchPaymentsTable" class="form-control"
                                    placeholder="Search by Payments Details"
                                    onkeyup="searchTable('searchPaymentsTable', 'PaymentsTable')">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle" id="PaymentsTable">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Customer Name</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Site Name</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Payment Date</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Penalty</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Total Amount + Penalty</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Total Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Paid Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Balance Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Payment Mode</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Action</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($payments)): ?>
                                        <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $customer_id = $payment['customer_id'];
                                                $sql = "SELECT userName FROM user WHERE user_id = $customer_id";
                                                $result = mysqli_query($connection, $sql);
                                                $customer = mysqli_fetch_assoc($result);
                                                echo htmlspecialchars($customer['userName']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $site_id = $payment['site_id'];
                                                $sql = "SELECT site_name FROM site WHERE id = $site_id";
                                                $result = mysqli_query($connection, $sql);
                                                $siteName = mysqli_fetch_assoc($result);
                                                echo htmlspecialchars($siteName['site_name']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['payment_date']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['penalty']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['totalAmountAndPenalty']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['total_amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['paid_amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['balance_amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($payment['payment_mode']); ?>
                                            </td>
                                            <td>
                                                <a href="eidtpayment2.php?id=<?php echo $payment['id']; ?>"
                                                    class="text-primary me-2">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a href="generate_pdf.php?payment_id=<?php echo htmlspecialchars($payment['id']); ?>"
                                                    class="text-primary me-2">
                                                    <i class="bi bi-printer"></i>
                                                </a>

                                                <a href="#" class="text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteEMIPaymentModal"
                                                    data-emi_payment_id="<?php echo $payment['id']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="13" class="text-center">No transactions found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>

    <script>
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
            document.getElementById("isEmi").value = isEmi; // Set isEmi value in the hidden input
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

    document.getElementById("addModal").addEventListener("shown.bs.modal", () => {
        myApp.setCurrentDate();
    });

    document.getElementById("amount").addEventListener("input", myApp.calculateTotalAmountAndPenalty);
    document.getElementById("penalty").addEventListener("input", myApp.calculateTotalAmountAndPenalty);
    </script>


</body>

</html>