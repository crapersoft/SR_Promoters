<?php
include ('database/database.php');
session_start();

if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookingId <= 0) {
    header('Location: ./error-page.php');  // Redirect to an error page if no valid booking ID
    die();
}

// Fetch booking data
$query = 'SELECT * FROM booking WHERE bookingid = ?';
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header('Location: ./error-page.php');  // Redirect if booking doesn't exist
    die();
}

// Fetch customer data
$customerQuery = 'SELECT user_id, userName FROM user WHERE user_id = ?';
$customerStmt = $connection->prepare($customerQuery);
$customerStmt->bind_param('i', $booking['customer_id']);
$customerStmt->execute();
$customer = $customerStmt->get_result()->fetch_assoc();

// Fetch site data
$siteQuery = 'SELECT * FROM site WHERE id = ?';
$siteStmt = $connection->prepare($siteQuery);
$siteStmt->bind_param('i', $booking['site_id']);
$siteStmt->execute();
$site = $siteStmt->get_result()->fetch_assoc();

// Fetch agent data
$agentQuery = 'SELECT agent_id, agent_name FROM agent WHERE agent_id = ?';
$agentStmt = $connection->prepare($agentQuery);
$agentStmt->bind_param('i', $booking['agent_id']);
$agentStmt->execute();
$agent = $agentStmt->get_result()->fetch_assoc();
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
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include './include/sidebar.php' ?>
        <div class="body-wrapper">
            <?php include './include/header.php' ?>
            <div class="container-fluid">
                <form action="./service/editBookingService.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header my-3">
                            <h5 class="modal-title" id="editModalLabel">Edit Bookings</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="bookingId" value="<?php echo $bookingId; ?>">

                            <!-- Customer Name -->
                            <div class="mb-3">
                                <label for="searchCustomer" class="form-label">Customer Name:</label>
                                <input type="text" id="searchCustomer" class="form-control"
                                    placeholder="Search for Customer..."
                                    value="<?php echo htmlspecialchars($customer['userName']); ?>"
                                    onkeyup="myApp.searchCustomer()">
                                <input type="hidden" id="selectedCustomerId" name="customer_id"
                                    value="<?php echo $booking['customer_id']; ?>">
                                <ul id="customerSuggestions" class="list-group"
                                    style="display:none; position:absolute; z-index:1000;"></ul>
                            </div>

                            <!-- Site Name -->
                            <div class="mb-3">
                                <label for="searchSite" class="form-label">Site Name:</label>
                                <input type="text" id="searchSite" class="form-control" placeholder="Search for Site..."
                                    value="<?php echo htmlspecialchars($site['site_name']); ?>"
                                    onkeyup="myApp.searchSite()">
                                <input type="hidden" id="selectedSiteId" name="site_id"
                                    value="<?php echo $booking['site_id']; ?>">
                                <ul id="siteSuggestions" class="list-group"
                                    style="display:none; position:absolute; z-index:1000;"></ul>
                            </div>
                            <!-- Site Number -->
                            <div class="mb-3">
                                <label for="siteno" class="form-label">Site Number</label>
                                <input type="text" class="form-control" id="siteno" name="siteno"
                                    value="<?php echo $booking['siteno']; ?>" required>
                            </div>

                            <!-- Available Sq.Ft -->
                            <div class="mb-3">
                                <label for="avalableSqft" class="form-label">Available Sq.Ft</label>
                                <input type="text" class="form-control" id="avalableSqft" name="avalableSqft"
                                    value="<?php echo $site['avaliableSqft']; ?>" readonly>
                            </div>

                            <!-- Customer Buying Sq.Ft -->
                            <div class="mb-3">
                                <label for="cBuySqft" class="form-label">Customer Buying Sq.Ft</label>
                                <input type="text" class="form-control" id="cBuySqft" name="cBuySqft"
                                    value="<?php echo $booking['Buying_Sqft']; ?>" required
                                    oninput="myApp.calculateTotalPrice()">
                            </div>

                            <!-- Price Per Square Feet -->
                            <div class="mb-3">
                                <label for="pricePerSquareFeet" class="form-label">Price Per Square Feet</label>
                                <input type="number" class="form-control" id="pricePerSquareFeet"
                                    name="pricePerSquareFeet" value="<?php echo $booking['pricePerSquareFeet']; ?>"
                                    min="1" required oninput="myApp.calculateTotalPrice()">
                            </div>

                            <!-- Total Buying Price -->
                            <div class="mb-3">
                                <label for="totalBuyingPrice" class="form-label">Total Buying Price</label>
                                <input type="number" class="form-control" id="totalBuyingPrice" name="totalBuyingPrice"
                                    value="<?php echo $booking['totalBuyingPrice']; ?>" min="1" required readonly>
                            </div>

                            <!-- Buying Date -->
                            <div class="mb-3">
                                <label for="buyingDate" class="form-label">Buying Date</label>
                                <input type="date" class="form-control" id="buyingDate" name="buyingDate"
                                    value="<?php echo $booking['buyingDate']; ?>" required>
                            </div>

                            <!-- Agent Name -->
                            <div class="mb-3">
                                <label for="searchAgent" class="form-label">Search for Agent:</label>
                                <input type="text" id="searchAgent" class="form-control"
                                    value="<?php echo htmlspecialchars($agent['agent_name']); ?>"
                                    placeholder="Search for Agent..." onkeyup="myApp.searchAgents()">
                                <input type="hidden" id="selectedAgentId" name="agent_id"
                                    value="<?php echo $booking['agent_id']; ?>">
                                <ul id="agentSuggestions" class="list-group"
                                    style="display:none; position:absolute; z-index:1000;"></ul>
                            </div>

                            <!-- Agent Percentage -->
                            <div class="mb-3">
                                <label for="persantage" class="form-label">Agent Percentage</label>
                                <input type="text" class="form-control" id="persantage" name="persantage"
                                    value="<?php echo $booking['persantage']; ?>" required
                                    oninput="myApp.calculatePersantageAmount()">
                            </div>

                            <!-- Agent Percentage Amount -->
                            <div class="mb-3">
                                <label for="persantageAmount" class="form-label">Agent Percentage Amount</label>
                                <input type="number" class="form-control" id="persantageAmount" name="persantageAmount"
                                    value="<?php echo $booking['persantageAmount']; ?>" readonly>
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

    <script>
    const myApp = {
        searchCustomer: function() {
            let searchTerm = document.getElementById("searchCustomer").value;
            let suggestionsList = document.getElementById("customerSuggestions");

            if (searchTerm.length === 0) {
                suggestionsList.style.display = 'none';
                return;
            }

            fetch(`search_customer.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';
                    data.forEach(customer => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = customer.userName;
                        listItem.onclick = () => myApp.selectCustomer(customer.userName, customer
                            .user_id);
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                })
                .catch(error => console.error('Error fetching customer data:', error));
        },

        selectCustomer: function(customerName, customerId) {
            document.getElementById("searchCustomer").value = customerName;
            document.getElementById("selectedCustomerId").value = customerId;
            document.getElementById("customerSuggestions").style.display = 'none';
        },

        searchSite: function() {
            let searchTerm = document.getElementById("searchSite").value;
            let suggestionsList = document.getElementById("siteSuggestions");

            if (searchTerm.length === 0) {
                suggestionsList.style.display = 'none';
                return;
            }

            fetch(`search_site.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';
                    data.forEach(site => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = site.site_name;
                        listItem.onclick = () => myApp.selectSite(site.site_name, site.id, site
                            .avaliableSqft, site.price_per_sqft);
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                })
                .catch(error => console.error('Error fetching site data:', error));
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

            fetch(`search_agents.php?query=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';
                    data.forEach(agent => {
                        let listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = agent.name;
                        listItem.onclick = () => myApp.selectAgent(agent.name, agent.id);
                        suggestionsList.appendChild(listItem);
                    });
                    suggestionsList.style.display = 'block';
                });
        },

        selectAgent: function(agentName, agentId) {
            document.getElementById("searchAgent").value = agentName;
            document.getElementById("selectedAgentId").value = agentId;
            document.getElementById("agentSuggestions").style.display = 'none';
        },

        calculateTotalPrice: function() {
            const cBuySqft = parseFloat(document.getElementById("cBuySqft").value) || 0;
            const pricePerSquareFeet = parseFloat(document.getElementById("pricePerSquareFeet").value) || 0;
            const totalBuyingPrice = cBuySqft * pricePerSquareFeet;
            document.getElementById("totalBuyingPrice").value = totalBuyingPrice.toFixed(2);
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