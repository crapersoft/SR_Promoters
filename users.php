<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Fetch all customers
$sql = 'SELECT * FROM user';
$result = mysqli_query($connection, $sql);
if (mysqli_num_rows($result) > 0) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $users = [];
}

$totalCustomers = mysqli_num_rows($result);

// Fetch past customers (assuming there is a condition for past customers, e.g., inactive status)
$sqlPastCustomers = 'SELECT * FROM user WHERE status = "inactive"';
$resultPastCustomers = mysqli_query($connection, $sqlPastCustomers);
$pastCustomers = mysqli_num_rows($resultPastCustomers);

// Fetch recent customers (e.g., customers added in the last 30 days)
$sqlRecentCustomers = 'SELECT * FROM user WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';  // Adjust column name
$resultRecentCustomers = mysqli_query($connection, $sqlRecentCustomers);
$recentCustomers = mysqli_num_rows($resultRecentCustomers);

// Fetch users from the 'user' table
$sql_agents = 'SELECT agent_id, agent_name FROM agent';
$result_agents = mysqli_query($connection, $sql_agents);
$agents = [];
if (mysqli_num_rows($result_agents) > 0) {
    $agents = mysqli_fetch_all($result_agents, MYSQLI_ASSOC);
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer | SR Promoters</title>
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

                            <!-- Total User -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-warning text-center p-3 h-100">
                                    <h1 class="card-title">Total Customer </h1>
                                    <h2 class="card-title h-10"><?php echo $totalCustomers; ?></h2>
                                </div>
                            </div>

                            <!-- Past User -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-secondary text-center p-3 h-100">
                                    <h1 class="card-title">Past Customer</h1>
                                    <h2 class="card-title h-10"><?php echo $pastCustomers; ?></h2>
                                </div>
                            </div>

                            <!-- Recent User -->
                            <div class="col-12 col-md-4">
                                <div class="card bg-danger text-center p-3 h-100">
                                    <h1 class="card-title">Recent Customer</h1>
                                    <h2 class="card-title h-10"><?php echo $recentCustomers; ?></h2>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Add Customer button -->
                <button type="button" class="btn btn-primary mt-3 mb-4" data-bs-toggle="modal"
                    data-bs-target="#addModal">
                    <i class="fa fa-plus me-2"></i>Add Customer
                </button>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Add Customer Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Form starts here -->
                                <form method="post" action="./service/addUserService.php">

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Customer PhoneNumber</label>
                                        <input type="tel" class="form-control" id="phone" pattern="[0-9]{10}"
                                            name="phone" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Customer Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Customer Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"
                                            required></textarea>
                                    </div>

                                    <!-- Aadhar Number -->
                                    <div class="mb-3">
                                        <label for="aadhar" class="form-label">Customer Aadhar Number</label>
                                        <input type="text" class="form-control" id="aadhar" name="aadhar" required>
                                    </div>

                                    <!-- Agent Name -->
                                    <div class="mb-3">
                                        <label for="searchAgent" class="form-label">Search for Agent:</label>
                                        <input type="text" id="searchAgent" class="form-control"
                                            placeholder="Search for Agent..." onkeyup="searchAgents()">
                                        <ul id="agentSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedAgentId" name="agent_id">
                                    </div>

                                    <!-- Agent persantage -->
                                    <div class="mb-3">
                                        <label for="persantage" class="form-label"> persantage</label>
                                        <input type="text" class="form-control" id="persantage" name="persantage"
                                            required>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>

                                </form>
                                <!-- Form ends here -->

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
                                Are you sure you want to delete this Customer?
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
                    const userId = button.getAttribute('data-userid'); // Extract userId from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

                    // Update the link with the userId
                    confirmDeleteButton.href = `./service/deleteUserService.php?userId=${userId}`;
                });
                </script>

                <!--  view the data in the table -->
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Customer Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0" style="width: 40px">
                                            <h6 class="fw-semibold mb-0">Customer Id</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Customer Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0" style="width: 50px">Mobile Number</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Email Id</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Agent Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Agent Persantage</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Status</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Action</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($user['user_id']); ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($user['userName']); ?></h6>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($user['phoneNumber']); ?></p>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0"><?php echo htmlspecialchars($user['email']); ?>
                                            </p>
                                        </td>
                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php
                                                // Prepare the query with MySQLi
                                                $query = 'SELECT agent_name FROM agent WHERE agent_id = ?';
                                                $stmt = $connection->prepare($query);

                                                if ($stmt) {
                                                    // Bind the parameter
                                                    $stmt->bind_param('i', $user['agent_id']);
                                                    // Execute the query
                                                    $stmt->execute();
                                                    // Fetch the result
                                                    $result = $stmt->get_result();
                                                    $agent = $result->fetch_assoc();
                                                    // Check if the agent was found and display the name
                                                    if ($agent) {
                                                        echo htmlspecialchars($agent['agent_name']);
                                                    } else {
                                                        echo 'Agent not found';  // Optional fallback if no agent is found
                                                    }
                                                    // Close the statement
                                                    $stmt->close();
                                                } else {
                                                    echo 'Failed to prepare the query.';
                                                }
                                                ?>
                                            </p>
                                        </td>

                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($user['persantage']); ?> %</p>
                                        </td>

                                        <td class="border-bottom-0">
                                            <p class="fw-semibold mb-0">
                                                <?php echo htmlspecialchars($user['status']); ?></p>
                                        </td>

                                        <td>
                                            <a href="eidtuser.php?id=<?php echo $user['user_id']; ?>"
                                                class="text-primary me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-userid="<?php echo $user['user_id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No Customer found</td>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
    function searchAgents() {
        let searchTerm = document.getElementById("searchAgent").value;
        let suggestionsList = document.getElementById("agentSuggestions");

        if (searchTerm.length === 0) {
            suggestionsList.style.display = 'none';
            return;
        }

        // Fetch agents based on the search term
        fetch(`search_agents.php?query=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = ''; // Clear existing suggestions
                data.forEach(agent => {
                    let listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.textContent = agent.name; // Display agent's name
                    listItem.onclick = () => selectAgent(agent.name, agent.id); // Set up selection
                    suggestionsList.appendChild(listItem);
                });
                suggestionsList.style.display = 'block';
            });
    }

    function selectAgent(agentName, agentId) {
        // Set the selected agent name in the input field
        document.getElementById("searchAgent").value = agentName;

        // Set the selected agent ID in the hidden input field
        document.getElementById("selectedAgentId").value = agentId;

        // Hide the suggestions dropdown
        document.getElementById("agentSuggestions").style.display = 'none';
    }
    </script>



</body>

</html>