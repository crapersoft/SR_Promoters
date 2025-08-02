<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
if ($userId > 0) {
    $query = 'SELECT u.*, a.agent_name 
              FROM user u 
              LEFT JOIN agent a ON u.agent_id = a.agent_id 
              WHERE u.user_id = ?';
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo '<p>User not found.</p>';
        die();
    }
} else {
    echo '<p>Invalid User ID.</p>';
    die();
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Customer | SR Promoters</title>
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

                <form action="./service/editUserService.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header my-3">
                            <h5 class="modal-title" id="editModalLabel">Edit Customer</h5>
                        </div>
                        <div class="modal-body">
                            <!-- Hidden input for User ID -->
                            <input type="hidden" id="customerId" name="userId"
                                value="<?= htmlspecialchars($user['user_id']); ?>">

                            <!-- Name Field -->
                            <div class="mb-3">
                                <label for="userName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="userName" name="userName"
                                    value="<?= htmlspecialchars($user['userName']); ?>" required>
                            </div>

                            <!-- Phone Number Field -->
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                                    value="<?= htmlspecialchars($user['phoneNumber']); ?>" required>
                            </div>

                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <!-- Address Field -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"
                                    required><?= htmlspecialchars($user['address']); ?></textarea>
                            </div>

                            <!-- Aadhar Number Field -->
                            <div class="mb-3">
                                <label for="aadharNumber" class="form-label">Aadhar Number</label>
                                <input type="text" class="form-control" id="aadharNumber" name="aadharNumber"
                                    value="<?= htmlspecialchars($user['aadharNumber']); ?>" required>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : ''; ?>>Active
                                    </option>
                                    <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : ''; ?>>
                                        Inactive</option>
                                </select>
                            </div>

                            <!-- Agent Name -->
                            <div class="mb-3">
                                <label for="searchAgent" class="form-label">Search for Agent:</label>
                                <input type="text" id="searchAgent" class="form-control"
                                    placeholder="Search for Agent..."
                                    value="<?= htmlspecialchars($user['agent_name']); ?>" onkeyup="searchAgents()">
                                <ul id="agentSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                <input type="hidden" id="selectedAgentId" name="agent_id"
                                    value="<?= htmlspecialchars($user['agent_id']); ?>">
                            </div>

                            <!-- Percentage -->
                            <div class="mb-3">
                                <label for="persantage" class="form-label">Percentage</label>
                                <input type="text" class="form-control" id="persantage" name="persantage"
                                    value="<?= htmlspecialchars($user['persantage']); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer gap-3">
                            <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='users.php';">Close</button>
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