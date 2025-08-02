<?php
include ('database/database.php');
session_start();

// Redirect if the user is not an admin
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Fetch EMI data
$sql = 'SELECT * FROM emis';
$result = mysqli_query($connection, $sql);
$emis = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EMI | SR Promoters</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png">
    <link rel="stylesheet" href="./assets/css/styles.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar -->
        <?php include './include/sidebar.php'; ?>

        <div class="body-wrapper">
            <!-- Header -->
            <?php include './include/header.php'; ?>

            <div class="container-fluid">
                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus me-2"></i>Add EMI
                </button>

                <!-- Add EMI Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Add New EMI </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="./service/addEmiService.php">
                                    <!-- Customer Name Search -->
                                    <div class="mb-3">
                                        <label for="searchCustomer" class="form-label">Customer Name:</label>
                                        <input type="text" id="searchCustomer" class="form-control"
                                            placeholder="Search for Customer..." onkeyup="myApp.searchCustomer()">
                                        <div id="customerLoading" class="spinner-border text-primary" role="status"
                                            style="display:none;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <ul id="customerSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedBookingId" name="bookingId">
                                    </div>

                                    <input type="hidden" id="siteId" name="siteId">



                                    <div class="mb-3">
                                        <label for="emiPlan" class="form-label">EMI Plan in Months</label>
                                        <input type="number" class="form-control" id="emiPlan" name="emiPlan" value="36"
                                            min="2" max="36" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="totalPayable" class="form-label">Total Payable Amount</label>
                                        <input type="number" class="form-control" id="totalPayable" name="totalPayable"
                                            min="0" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="monthlyPayable" class="form-label">Monthly Payable Amount</label>
                                        <input type="number" class="form-control" id="monthlyPayable"
                                            name="monthlyPayable" min="0" required>
                                    </div>

                                    <!-- Agent Commission  -->
                                    <div class="mb-3">
                                        <label for="agentCommission" class="form-label">Agent Commission</label>
                                        <input type="text" class="form-control" id="agentCommission"
                                            name="agentCommission" required>
                                    </div>

                                    <!-- Agent Commission Amount -->
                                    <div class="mb-3">
                                        <label for="agentCommissionAmount" class="form-label">Agent Commission
                                            Amount</label>
                                        <input type="text" class="form-control" id="agentCommissionAmount"
                                            name="agentCommissionAmount" required readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="startDate" class="form-label">EMI Start Date</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="endDate" class="form-label">EMI End Date</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate" readonly>
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

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this Emi ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a id="confirmDeleteButton" href="#" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                const deleteConfirmModal = document.getElementById('deleteConfirmModal');
                deleteConfirmModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget; // Button that triggered the modal
                    const emiIds = button.getAttribute('data-emi_ids'); // Extract siteId from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

                    // Update the link with the siteId
                    confirmDeleteButton.href = `./service/deleteEmiService.php?emiIds=${emiIds}`;
                });
                </script>

                <!--  view table modal -->
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">EMI Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0" style="width: 10px">
                                            <h6 class="fw-semibold mb-0">Booking Id</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Customer Name</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">site Name</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">site No</h6>
                                        </th>

                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Agent name</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Agent percentage</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Agent Commission</h6>
                                        </th>

                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Buyed Sq.Ft</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Total Amount</h6>
                                        </th>

                                        <th class="border-bottom-0" style="width: 20px">
                                            <h6 class="fw-semibold mb-0">EMI startDate</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 20px">
                                            <h6 class="fw-semibold mb-0">EMI endDate</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 20px">
                                            <h6 class="fw-semibold mb-0">EMI Due</h6>
                                        </th>

                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Monthly EMI Amount</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Paid Amount</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 30px">
                                            <h6 class="fw-semibold mb-0">Balance Amount</h6>
                                        </th>
                                        <th class="border-bottom-0" style="width: 20px">
                                            <h6 class="fw-semibold mb-0">Action</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($emis)): ?>
                                    <?php foreach ($emis as $emi): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($emi['booking_id']); ?></td>
                                        <td>
                                            <?php
                                            if (isset($emi['booking_id'])) {
                                                $query = 'SELECT u.userName FROM booking b 
                                                JOIN user u ON b.customer_id = u.user_id 
                                                WHERE b.bookingid = ?';
                                                $stmt = $connection->prepare($query);

                                                if ($stmt) {
                                                    $stmt->bind_param('i', $emi['booking_id']);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    $customer = $result->fetch_assoc();
                                                    echo $customer ? htmlspecialchars($customer['userName']) : 'Customer Name Not Found';
                                                    $stmt->close();
                                                } else {
                                                    echo 'Failed to prepare query.';
                                                }
                                            } else {
                                                echo 'Invalid booking ID.';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            // Prepare the query with MySQLi
                                            $query = 'SELECT site_name FROM site WHERE id = ?';
                                            $stmt = $connection->prepare($query);

                                            if ($stmt) {
                                                // Bind the parameter
                                                $stmt->bind_param('i', $emi['site_id']);
                                                // Execute the query
                                                $stmt->execute();
                                                // Fetch the result
                                                $result = $stmt->get_result();
                                                $site = $result->fetch_assoc();
                                                // Check if the agent was found and display the name
                                                if ($site) {
                                                    echo htmlspecialchars($site['site_name']);
                                                } else {
                                                    echo 'Site Name is not found';  // Optional fallback if no agent is found
                                                }
                                                // Close the statement
                                                $stmt->close();
                                            } else {
                                                echo 'Failed to prepare the query.';
                                            }
                                            ?>
                                        </td>

                                        <!-- site no -->
                                        <td>
                                            <?php
                                            // Prepare the query with MySQLi
                                            $query = 'SELECT siteno FROM booking WHERE bookingid = ?';
                                            $stmt = $connection->prepare($query);

                                            if ($stmt) {
                                                // Bind the parameter
                                                $stmt->bind_param('i', $emi['booking_id']);
                                                // Execute the query
                                                $stmt->execute();
                                                // Fetch the result
                                                $result = $stmt->get_result();
                                                $site = $result->fetch_assoc();
                                                // Check if the agent was found and display the name
                                                if ($site) {
                                                    echo htmlspecialchars($site['siteno']);
                                                } else {
                                                    echo '-';  // Optional fallback if no agent is found
                                                }
                                                // Close the statement
                                                $stmt->close();
                                            } else {
                                                echo 'Failed to prepare the query.';
                                            }
                                            ?>
                                        </td>

                                        <!-- Agent Name -->
                                        <td>
                                            <?php
                                            if (isset($emi['booking_id'])) {
                                                $query = 'SELECT a.agent_name FROM booking b 
                                                JOIN agent a ON b.agent_id = a.agent_id 
                                                WHERE b.bookingid = ?';
                                                $stmt = $connection->prepare($query);

                                                if ($stmt) {
                                                    $stmt->bind_param('i', $emi['booking_id']);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    $customer = $result->fetch_assoc();
                                                    echo $customer ? htmlspecialchars($customer['agent_name']) : 'Customer Name Not Found';
                                                    $stmt->close();
                                                } else {
                                                    echo 'Failed to prepare query.';
                                                }
                                            } else {
                                                echo 'Invalid booking ID.';
                                            }
                                            ?>
                                        </td>

                                        <td><?php echo htmlspecialchars($emi['agentCommission']); ?>%</td>
                                        
                                        <td>Rs <?php echo htmlspecialchars($emi['agentCommissionAmount']); ?></td>

                                        <td>
                                            <?php
                                            // Prepare the query with MySQLi
                                            $query = 'SELECT Buying_Sqft FROM booking WHERE bookingid = ?';
                                            $stmt = $connection->prepare($query);

                                            if ($stmt) {
                                                // Bind the parameter
                                                $stmt->bind_param('i', $emi['booking_id']);
                                                // Execute the query
                                                $stmt->execute();
                                                // Fetch the result
                                                $result = $stmt->get_result();
                                                $site = $result->fetch_assoc();
                                                // Check if the agent was found and display the name
                                                if ($site) {
                                                    echo htmlspecialchars($site['Buying_Sqft']), ' sqft';
                                                } else {
                                                    echo 'Custemer SQft Name is not found';  // Optional fallback if no agent is found
                                                }
                                                // Close the statement
                                                $stmt->close();
                                            } else {
                                                echo 'Failed to prepare the query.';
                                            }
                                            ?>
                                        </td>

                                        <td>Rs <?php echo htmlspecialchars($emi['total_payable']); ?></td>

                                        <td><?php echo htmlspecialchars($emi['startDate']); ?></td>
                                        <td><?php echo htmlspecialchars($emi['endDate']); ?></td>
                                        <td><?php echo htmlspecialchars($emi['emi_plan']); ?></td>

                                        <td>Rs <?php echo htmlspecialchars($emi['monthly_payable']); ?></td>
                                        <td><?php echo htmlspecialchars($emi['paid_amount']); ?></td>
                                        <td><?php echo htmlspecialchars($emi['balance_amount']); ?></td>
                                        <td>
                                            <a href="editemi.php?id=<?php echo $emi['emi_ids']; ?>"
                                                class="text-primary me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-emi_ids="<?php echo $emi['emi_ids']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No Emi are found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="py-6 px-6 text-center">
                    <p class="mb-0 fs-4">Design and Developed by <a href="https://crapersoft.com/" target="_blank"
                            class="pe-1 text-primary text-decoration-underline">Crapersoft</a></p>
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
            // Set the selected customer details
            document.getElementById("searchCustomer").value = customerDisplay;
            document.getElementById("selectedBookingId").value = bookingId;
            document.getElementById("totalPayable").value = totalBuyingPrice;

            // Set the site ID
            document.getElementById("siteId").value = siteId;

            // Hide the customer suggestions dropdown
            document.getElementById("customerSuggestions").style.display = 'none';

            // Trigger monthlyPayable calculation dynamically
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

                // Update the agent commission amount dynamically
                myApp.updateAgentCommissionAmount();
            }
        },

        updateAgentCommissionAmount: function() {
            const monthlyPayable = parseFloat(document.getElementById("monthlyPayable").value) || 0;
            const agentCommission = parseFloat(document.getElementById("agentCommission").value) || 0;

            // Calculate commission amount based on percentage
            const agentCommissionAmount = (monthlyPayable * agentCommission) / 100;

            // Set the calculated value
            document.getElementById("agentCommissionAmount").value = Math.round(agentCommissionAmount);
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