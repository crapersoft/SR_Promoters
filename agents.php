<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Fetch all customers
$sql = 'SELECT * FROM agent';
$result = mysqli_query($connection, $sql);
if (mysqli_num_rows($result) > 0) {
    $agents = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $agents = [];
}

$totalAgents = mysqli_num_rows($result);

// Fetch past customers (assuming there is a condition for past customers, e.g., inactive status)
$sqlPastCustomers = 'SELECT * FROM agent WHERE status = "inactive"';
$resultPastCustomers = mysqli_query($connection, $sqlPastCustomers);
$pastAgents = mysqli_num_rows($resultPastCustomers);

// Fetch recent customers (e.g., customers added in the last 30 days)
$sqlRecentCustomers = 'SELECT * FROM agent WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)'; 
$resultRecentCustomers = mysqli_query($connection, $sqlRecentCustomers);
$recentAgents = mysqli_num_rows($resultRecentCustomers);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agents | SR Promoters</title>
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
                <div class="card bg-dark-subtle">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Total Agent -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-warning text-center p-3 h-100">
                                    <h1 class="card-title">Total Agent</h1>
                                    <h2 class="card-title h-10"><?php echo $totalAgents; ?></h2>
                                </div>
                            </div>

                            <!-- Past Agent -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-secondary text-center p-3 h-100">
                                    <h1 class="card-title">Past Agent</h1>
                                    <h2 class="card-title h-10"><?php echo $pastAgents; ?></h2>
                                </div>
                            </div>

                            <!-- Recent Agent -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-danger text-center p-3 h-100">
                                    <h1 class="card-title">Recent Agent</h1>
                                    <h2 class="card-title h-10"><?php echo $recentAgents; ?></h2>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Removed extra spacing on the button -->
                <button type="button" class="btn btn-primary mt-3 mb-4" data-bs-toggle="modal"
                    data-bs-target="#addModal">
                    <i class="fa fa-plus me-2"></i>Add Agents
                </button>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Agents Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Form starts here -->
                                <form method="post" action="./service/addAgentService.php">

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="agent_name" class="form-label">Agents Name</label>
                                        <input type="text" class="form-control" id="agent_name" name="agent_name"
                                            required>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="mobile_number" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                            pattern="\d{10}" title="Phone number must be exactly 10 digits" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="emil_id" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emil_id" name="emil_id" required>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="agent_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="agent_address" name="agent_address" rows="2"
                                            required></textarea>
                                    </div>

                                    <!-- Aadhaar Number -->
                                    <div class="mb-3">
                                        <label for="agent_aadhar_no" class="form-label">Aadhaar Number</label>
                                        <input type="text" class="form-control" id="agent_aadhar_no"
                                            name="agent_aadhar_no" pattern="\d{12}"
                                            title="Aadhaar number must be exactly 12 digits" required>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Agent</button>
                                    </div>

                                </form>
                                <!-- Form ends here -->

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">

                        <form action="./service/editAgentService.php" method="POST">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Agent Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <!-- Hidden input for User ID -->
                                    <input type="hidden" id="agent_id" name="agent_id">

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="agent_name" class="form-label">Agents Name</label>
                                        <input type="text" class="form-control" id="agent_name" name="agent_name"
                                            required>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="mobile_number" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="mobile_number" pattern="[0-9]{10}"
                                            name="mobile_number" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="emil_id" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emil_id" name="emil_id" required>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="agent_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="agent_address" name="agent_address" rows="2"
                                            required></textarea>
                                    </div>

                                    <!-- Aadhar Number -->
                                    <div class="mb-3">
                                        <label for="agent_aadhar_no" class="form-label">Aadhar Number</label>
                                        <input type="text" class="form-control" id="agent_aadhar_no"
                                            name="agent_aadhar_no" required>
                                    </div>

                                    <!-- Status Dropdown -->
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Changes</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <script>
                // Prefill modal fields with customer data when triggered
                var editModal = document.getElementById('editModal');
                editModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget; // Button that triggered the modal

                    // Extract customer data from data attributes
                    var agent_id = button.getAttribute('data-agent_id');
                    var agent_name = button.getAttribute('data-agent_name');
                    var mobile_number = button.getAttribute('data-mobile_number');
                    var emil_id = button.getAttribute('data-emil_id');
                    var agent_address = button.getAttribute('data-agent_address');
                    var agent_aadhar_no = button.getAttribute('data-agent_aadhar_no');
                    var status = button.getAttribute('data-status');

                    // Update modal fields with extracted data
                    editModal.querySelector('#agent_id').value = agent_id;
                    editModal.querySelector('#agent_name').value = agent_name;
                    editModal.querySelector('#mobile_number').value = mobile_number;
                    editModal.querySelector('#emil_id').value = emil_id;
                    editModal.querySelector('#agent_address').value = agent_address;
                    editModal.querySelector('#agent_aadhar_no').value = agent_aadhar_no;
                    editModal.querySelector('#status').value = status;
                });
                </script>

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
                                Are you sure you want to delete this Agent?
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
                    const button = event.relatedTarget;
                    const agent_id = button.getAttribute('data-agent_id');
                    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

                    confirmDeleteButton.href = `./service/deleteAgentService.php?agentId=${agent_id}`;
                });
                </script>

                <!--  view the data in the table -->
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Agent Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0" style="width: 40px">
                                            <h6 class="fw-semibold mb-0">Agent Id</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0" style="width: 50px"> Mobile No</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Agent Email</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Agent Address</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Agent Aadhar No</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0"> Status </h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Action</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($agents)): ?>
                                    <?php foreach ($agents as $agent): ?>
                                    <tr>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['agent_id']); ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['agent_name']); ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['mobile_number']); ?></p>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['emil_id']); ?>
                                            </p>
                                        </td>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['agent_address']); ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['agent_aadhar_no']); ?></p>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($agent['status']); ?></p>
                                        </td>

                                        <td>

                                            <a href="#" class="text-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                data-agent_id="<?php echo $agent['agent_id']; ?>"
                                                data-agent_name="<?php echo $agent['agent_name']; ?>"
                                                data-mobile_number="<?php echo $agent['mobile_number']; ?>"
                                                data-emil_id="<?php echo $agent['emil_id']; ?>"
                                                data-agent_address="<?php echo $agent['agent_address']; ?>"
                                                data-agent_aadhar_no="<?php echo $agent['agent_aadhar_no']; ?>"
                                                data-status="<?php echo $agent['status']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <script>
                                            var editModal = document.getElementById('editModal');
                                            editModal.addEventListener('show.bs.modal', function(event) {
                                                var button = event.relatedTarget;
                                                var agent_id = button.getAttribute('data-agent_id');
                                                var agent_name = button.getAttribute('data-agent_name');
                                                var mobile_number = button.getAttribute('data-mobile_number');
                                                var emil_id = button.getAttribute('data-emil_id');
                                                var agent_address = button.getAttribute('data-agent_address');
                                                var agent_aadhar_no = button.getAttribute(
                                                    'data-agent_aadhar_no');
                                                var status = button.getAttribute('data-status');

                                                editModal.querySelector('#agent_id').value = agent_id;
                                                editModal.querySelector('#agent_name').value = agent_name;
                                                editModal.querySelector('#mobile_number').value = mobile_number;
                                                editModal.querySelector('#emil_id').value = emil_id;
                                                editModal.querySelector('#agent_address').value = agent_address;
                                                editModal.querySelector('#agent_aadhar_no').value =
                                                    agent_aadhar_no;
                                                editModal.querySelector('#status').value = status;
                                            });
                                            </script>

                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-agent_id="<?php echo $user['agent_id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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
</body>

</html>