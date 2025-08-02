<?php
include ('database/database.php');
session_start();
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$sql = 'SELECT * FROM booking';
$result = mysqli_query($connection, $sql);
if (mysqli_num_rows($result) > 0) {
    $sites = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $sites = [];
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookings | SR Promoters</title>
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
                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus me-2"></i>Add Site Details</button>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">User Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form starts here -->
                                <form method="post" action="./service/addAllocateService.php">

                                    <!-- Customer Name -->
                                    <div class="mb-3">
                                        <label for="searchCustomer" class="form-label">Customer Name:</label>
                                        <input type="text" id="searchCustomer" class="form-control"
                                            placeholder="Search for Customer..." onkeyup="myApp.searchCustomer()">
                                        <div id="customerLoading" class="spinner-border text-primary" role="status"
                                            style="display:none;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <ul id="customerSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedCustomerId" name="customer_id">
                                    </div>

                                    <!-- Site Name -->
                                    <div class="mb-3">
                                        <label for="searchSite" class="form-label">Site Name:</label>
                                        <input type="text" id="searchSite" class="form-control"
                                            placeholder="Search for Site..." onkeyup="myApp.searchSite()">
                                        <div id="siteLoading" class="spinner-border text-primary" role="status"
                                            style="display:none;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <ul id="siteSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedSiteId" name="site_id">
                                    </div>
                                    
                                    <!-- Site Number -->
                                    <div class="mb-3">
                                        <label for="siteno" class="form-label">Site Number</label>
                                        <input type="text" class="form-control" id="siteno" name="siteno"
                                            required>
                                    </div>

                                    <!-- avaliable Sq.Ft -->
                                    <div class="mb-3">
                                        <label for="avalableSqft" class="form-label">avaliable Sq.Ft</label>
                                        <input type="text" class="form-control" id="avalableSqft" name="avalableSqft"
                                            required>
                                    </div>

                                    <!-- Customer buying Sq.Ft -->
                                    <div class="mb-3">
                                        <label for="cBuySqft" class="form-label">Customer buying Sq.Ft</label>
                                        <input type="text" class="form-control" id="cBuySqft" name="cBuySqft" required
                                            oninput="myApp.calculateTotalPrice()">
                                    </div>

                                    <!-- Price per Square Feet -->
                                    <div class="mb-3">
                                        <label for="pricePerSquareFeet" class="form-label">Price Per Square Feet</label>
                                        <input type="number" class="form-control" id="pricePerSquareFeet"
                                            name="pricePerSquareFeet" min="1" required
                                            oninput="myApp.calculateTotalPrice()">
                                    </div>

                                    <!-- Total Buying Price -->
                                    <div class="mb-3">
                                        <label for="totalBuyingPrice" class="form-label">Total Buying Price</label>
                                        <input type="number" class="form-control" id="totalBuyingPrice"
                                            name="totalBuyingPrice" min="1" required readonly>
                                    </div>

                                    <!-- Customer buying Date -->
                                    <div class="mb-3">
                                        <label for="buyingDate" class="form-label">Buying Date</label>
                                        <input type="date" class="form-control" id="buyingDate" name="buyingDate"
                                            required>
                                    </div>

                                    <!-- Agent Name -->
                                    <div class="mb-3">
                                        <label for="searchAgent" class="form-label">Search for Agent:</label>
                                        <input type="text" id="searchAgent" class="form-control"
                                            placeholder="Search for Agent..." onkeyup="myApp.searchAgents()">
                                        <ul id="agentSuggestions" class="list-group mt-2" style="display:none;"></ul>
                                        <input type="hidden" id="selectedAgentId" name="agent_id">
                                    </div>

                                    <!-- persantage -->
                                    <div class="mb-3">
                                        <label for="persantage" class="form-label">Agent persantage</label>
                                        <input type="text" class="form-control" id="persantage" name="persantage"
                                            required>
                                    </div>
                                    <!-- persantage Amount -->
                                    <div class="mb-3">
                                        <label for=" persantageAmount" class="form-label"> persantage Amount</label>
                                        <input type="number" class="form-control" id="persantageAmount"
                                            name="persantageAmount" min="1" required readonly>
                                    </div>

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
                                Are you sure you want to delete this Booking ?
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
                    const bookingid = button.getAttribute(
                        'data-bookingid'); // Extract siteId from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

                    // Update the link with the siteId
                    confirmDeleteButton.href = `./service/deleteBookingService.php?bookingid=${bookingid}`;
                });
                </script>

                <!-- Table Display -->
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Booking Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">Booking ID</th>
                                        <th class="border-bottom-0">Customer Name</th>
                                        <th class="border-bottom-0">Site Name</th>
                                        <th class="border-bottom-0">Site no</th>
                                        <th class="border-bottom-0">Buying Sqft</th>
                                        <th class="border-bottom-0">Price/Sqft</th>
                                        <th class="border-bottom-0">Total Buying Price</th>
                                        <th class="border-bottom-0">Buying Date</th>
                                        <th class="border-bottom-0">Agent Name</th>
                                        <th class="border-bottom-0">Percentage</th>
                                        <th class="border-bottom-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($sites)): ?>
                                    <?php foreach ($sites as $site): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($site['bookingid']); ?></td>
                                        <td>
                                            <?php
                                            $customer_id = $site['customer_id'];
                                            $sql = "SELECT userName FROM user WHERE user_id = $customer_id";
                                            $result = mysqli_query($connection, $sql);
                                            $customer = mysqli_fetch_assoc($result);
                                            echo htmlspecialchars($customer['userName']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $site_id = $site['site_id'];
                                            $sql = "SELECT site_name FROM site WHERE id = $site_id";
                                            $result = mysqli_query($connection, $sql);
                                            $siteName = mysqli_fetch_assoc($result);
                                            echo htmlspecialchars($siteName['site_name']);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($site['siteno']); ?></td>
                                        <td><?php echo htmlspecialchars($site['Buying_Sqft']); ?></td>
                                        <td><?php echo htmlspecialchars($site['pricePerSquareFeet']); ?></td>
                                        <td><?php echo htmlspecialchars($site['totalBuyingPrice']); ?></td>
                                        <td><?php echo htmlspecialchars($site['buyingDate']); ?></td>
                                        <td>
                                            <?php
                                            $agent_id = $site['agent_id'];
                                            $sql = "SELECT agent_name FROM agent WHERE agent_id = $agent_id";
                                            $result = mysqli_query($connection, $sql);
                                            $agent = mysqli_fetch_assoc($result);
                                            echo htmlspecialchars($agent['agent_name']);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($site['persantage']); ?>%</td>
                                        <td>
                                            <a href="editallocate.php?id=<?php echo $site['bookingid']; ?>"
                                                class="text-primary me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-bookingid="<?php echo $site['bookingid']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No booking details found</td>
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
                        class="pe-1 text-primary text-decoration-underline">Crapersoft</a></p>
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

    <script>
    const myApp = {
        searchCustomer: function() {
            let searchTerm = document.getElementById("searchCustomer").value;
            let suggestionsList = document.getElementById("customerSuggestions");
            let loadingSpinner = document.getElementById("customerLoading");

            if (searchTerm.length === 0) {
                suggestionsList.style.display = 'none';
                loadingSpinner.style.display = 'none';
                return;
            }

            loadingSpinner.style.display = 'block';

            fetch(`search_customer.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Customer data:', data); // Log the response data
                    suggestionsList.innerHTML = ''; // Clear existing suggestions
                    data.forEach(customer => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = customer.userName; // Display customer name
                        listItem.onclick = () => myApp.selectCustomer(customer.userName, customer
                            .user_id); // Set up selection
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                    loadingSpinner.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching customer data:', error); // Log any errors
                    loadingSpinner.style.display = 'none';
                });
        },

        selectCustomer: function(customerName, customerId) {
            document.getElementById("searchCustomer").value = customerName;
            document.getElementById("selectedCustomerId").value = customerId;
            document.getElementById("customerSuggestions").style.display = 'none';
        },

        searchSite: function() {
            let searchTerm = document.getElementById("searchSite").value;
            let suggestionsList = document.getElementById("siteSuggestions");
            let loadingSpinner = document.getElementById("siteLoading");

            if (searchTerm.length === 0) {
                suggestionsList.style.display = 'none';
                loadingSpinner.style.display = 'none';
                return;
            }

            loadingSpinner.style.display = 'block';

            fetch(`search_site.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Site data:', data); // Log the response data
                    suggestionsList.innerHTML = ''; // Clear existing suggestions
                    data.forEach(site => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = site.site_name; // Display site name
                        listItem.onclick = () => myApp.selectSite(site.site_name, site.id, site
                            .avaliableSqft, site.price_per_sqft); // Set up selection
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                    loadingSpinner.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching site data:', error); // Log any errors
                    loadingSpinner.style.display = 'none';
                });
        },

        selectSite: function(siteName, siteId, avaliableSqft, pricePerSqft) {
            document.getElementById("searchSite").value = siteName;
            document.getElementById("selectedSiteId").value = siteId;
            document.getElementById("siteSuggestions").style.display = 'none';
            document.getElementById("avalableSqft").value = avaliableSqft;
            document.getElementById("pricePerSquareFeet").value = pricePerSqft;
            myApp.calculateTotalPrice();
        },

        searchAgents: function() {
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
                        listItem.onclick = () => myApp.selectAgent(agent.name, agent
                            .id); // Set up selection
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                });
        },

        selectAgent: function(agentName, agentId) {
            // Set the selected agent name in the input field
            document.getElementById("searchAgent").value = agentName;

            // Set the selected agent ID in the hidden input field
            document.getElementById("selectedAgentId").value = agentId;

            // Hide the suggestions dropdown
            document.getElementById("agentSuggestions").style.display = 'none';
        },

        calculateTotalPrice: function() {
            const cBuySqft = parseFloat(document.getElementById("cBuySqft").value) || 0;
            const pricePerSquareFeet = parseFloat(document.getElementById("pricePerSquareFeet").value) || 0;
            const totalBuyingPrice = cBuySqft * pricePerSquareFeet;
            document.getElementById("totalBuyingPrice").value = totalBuyingPrice.toFixed(2);
        },

        calculateTotalPrice: function() {
            const cBuySqft = parseFloat(document.getElementById("cBuySqft").value) || 0;
            const pricePerSquareFeet = parseFloat(document.getElementById("pricePerSquareFeet").value) || 0;
            const totalBuyingPrice = cBuySqft * pricePerSquareFeet;
            document.getElementById("totalBuyingPrice").value = totalBuyingPrice.toFixed(2);

            // Update percentage amount dynamically
            myApp.calculatePersantageAmount();
        },

        calculatePersantageAmount: function() {
            const totalBuyingPrice = parseFloat(document.getElementById("totalBuyingPrice").value) || 0;
            const persantage = parseFloat(document.getElementById("persantage").value) || 0;
            const persantageAmount = (totalBuyingPrice * persantage) / 100;
            document.getElementById("persantageAmount").value = persantageAmount.toFixed(2);
        },

        setCurrentDate: function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("buyingDate").value = today;
        }
    };

    // Set the current date on page load
    document.addEventListener('DOMContentLoaded', (event) => {
        myApp.setCurrentDate();
        // Add event listener for persantage input
        document.getElementById("persantage").addEventListener('input', myApp.calculatePersantageAmount);
    });
    </script>
</body>

</html>