<?php
include ('database/database.php');
session_start();
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

$sql = 'SELECT * FROM site';
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
    <title>site | SR Promoters</title>
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
                                <h5 class="modal-title" id="addModalLabel">Site Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Form starts here -->
                                <form method="post" action="./service/addSiteService.php">

                                    <!-- Site name -->
                                    <div class="mb-3">
                                        <label for="siteName" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" id="siteName" name="siteName" required>
                                    </div>

                                    <!-- Total Site Square Feet -->
                                    <div class="mb-3">
                                        <label for="totalSquareFeet" class="form-label">Total Site Square Feet</label>
                                        <input type="number" class="form-control" id="totalSquareFeet"
                                            name="totalSquareFeet" required>
                                    </div>

                                    <!-- Price per Square Feet -->
                                    <div class="mb-3">
                                        <label for="pricePerSquareFeet" class="form-label">Price Per Square Feet</label>
                                        <input type="number" class="form-control" id="pricePerSquareFeet"
                                            name="pricePerSquareFeet" min="1" required>
                                    </div>

                                    <!-- Total Price per Square Feet (Calculated) -->
                                    <div class="mb-3">
                                        <label for="totalPricePerSquareFeet" class="form-label">Total Price</label>
                                        <input type="number" class="form-control" id="totalPricePerSquareFeet"
                                            name="totalPricePerSquareFeet" readonly>
                                    </div>

                                    <!-- Site Address -->
                                    <div class="mb-3">
                                        <label for="siteAddress" class="form-label">Site Address</label>
                                        <textarea class="form-control" id="siteAddress" name="siteAddress" rows="4"
                                            required></textarea>
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

                <script>
                // Get references to the input fields
                const totalSquareFeetInput = document.getElementById('totalSquareFeet');
                const pricePerSquareFeetInput = document.getElementById('pricePerSquareFeet');
                const totalPricePerSquareFeetInput = document.getElementById('totalPricePerSquareFeet');

                // Function to calculate total price
                function calculateTotalPrice() {
                    const totalSquareFeet = parseFloat(totalSquareFeetInput.value) || 0;
                    const pricePerSquareFeet = parseFloat(pricePerSquareFeetInput.value) || 0;

                    // Calculate the total price
                    const totalPrice = totalSquareFeet * pricePerSquareFeet;

                    // Update the total price input field
                    totalPricePerSquareFeetInput.value = totalPrice.toFixed(2);
                }

                // Add event listeners to input fields
                totalSquareFeetInput.addEventListener('input', calculateTotalPrice);
                pricePerSquareFeetInput.addEventListener('input', calculateTotalPrice);
                </script>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="./service/editSiteService.php" method="POST">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Site</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Hidden input for Site ID -->
                                    <input type="hidden" id="siteId" name="siteId">

                                    <!-- Site name -->
                                    <div class="mb-3">
                                        <label for="siteName" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" id="siteName" name="siteName" required>
                                    </div>

                                    <!-- Total Site Square Feet -->
                                    <div class="mb-3">
                                        <label for="totalSquareFeet" class="form-label">Total Site Square Feet</label>
                                        <input type="text" class="form-control" id="totalSquareFeet"
                                            name="totalSquareFeet" required>
                                    </div>

                                    <!-- Price per Square Feet -->
                                    <div class="mb-3">
                                        <label for="pricePerSquareFeet" class="form-label">Price Per Square Feet</label>
                                        <input type="number" class="form-control" id="pricePerSquareFeet"
                                            name="pricePerSquareFeet" min="1" required>
                                    </div>

                                    <!-- Total Price per Square Feet -->
                                    <div class="mb-3">
                                        <label for="totalPricePerSquareFeet" class="form-label">Total Price Per Square
                                            Feet</label>
                                        <input type="number" class="form-control" id="totalPricePerSquareFeet"
                                            name="totalPricePerSquareFeet" min="1" required>
                                    </div>

                                    <!-- Site Address -->
                                    <div class="mb-3">
                                        <label for="siteAddress" class="form-label">Site Address</label>
                                        <textarea class="form-control" id="siteAddress" name="siteAddress" rows="4"
                                            required></textarea>
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
                // Get references to the input fields in the Edit Modal
                var editModalTotalSquareFeetInput = document.querySelector('#editModal #totalSquareFeet');
                var editModalPricePerSquareFeetInput = document.querySelector('#editModal #pricePerSquareFeet');
                var editModalTotalPricePerSquareFeetInput = document.querySelector(
                    '#editModal #totalPricePerSquareFeet');

                // Function to calculate total price in the Edit Modal
                function calculateEditModalTotalPrice() {
                    const totalSquareFeet = parseFloat(editModalTotalSquareFeetInput.value) || 0;
                    const pricePerSquareFeet = parseFloat(editModalPricePerSquareFeetInput.value) || 0;

                    // Calculate the total price
                    const totalPrice = totalSquareFeet * pricePerSquareFeet;

                    // Update the total price input field
                    editModalTotalPricePerSquareFeetInput.value = totalPrice.toFixed(2);
                }

                // Add event listeners to input fields in the Edit Modal
                editModalTotalSquareFeetInput.addEventListener('input', calculateEditModalTotalPrice);
                editModalPricePerSquareFeetInput.addEventListener('input', calculateEditModalTotalPrice);

                // Populate Edit Modal fields and ensure calculation is triggered when opened
                var editModal = document.getElementById('editModal');
                editModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget; // Button that triggered the modal

                    // Extract site data from data attributes
                    var siteId = button.getAttribute('data-siteid');
                    var siteName = button.getAttribute('data-site_name');
                    var totalSqft = button.getAttribute('data-total_sqft');
                    var pricePerSqft = button.getAttribute('data-price_per_sqft');
                    var totalPriceSqft = button.getAttribute('data-total_price_sqft');
                    var siteAddress = button.getAttribute('data-site_address');

                    // Update modal fields with extracted data
                    editModal.querySelector('#siteId').value = siteId;
                    editModal.querySelector('#siteName').value = siteName;
                    editModal.querySelector('#totalSquareFeet').value = totalSqft;
                    editModal.querySelector('#pricePerSquareFeet').value = pricePerSqft;
                    editModal.querySelector('#totalPricePerSquareFeet').value = totalPriceSqft;
                    editModal.querySelector('#siteAddress').value = siteAddress;

                    // Trigger the total price calculation
                    calculateEditModalTotalPrice();
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
                                Are you sure you want to delete this site?
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
                    const siteId = button.getAttribute('data-siteid'); // Extract siteId from data-* attribute
                    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

                    // Update the link with the siteId
                    confirmDeleteButton.href = `./service/deleteSiteService.php?siteId=${siteId}`;
                });
                </script>

                <!-- Table Display -->
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Site Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">Site Name</th>
                                        <th class="border-bottom-0">Total Sq.Ft</th>
                                        <th class="border-bottom-0">Price Per Sq.Ft</th>
                                        <th class="border-bottom-0">Total Site Price</th>
                                        <th class="border-bottom-0">Booked Sq.Ft</th>
                                        <th class="border-bottom-0">Avalable Sq.Ft</th>
                                        <th class="border-bottom-0">Site Address</th>
                                        <th class="border-bottom-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($sites)): ?>
                                    <?php foreach ($sites as $site): ?>
                                    <tr>

                                        <td><?php echo htmlspecialchars($site['site_name']); ?></td>
                                        <td><?php echo htmlspecialchars($site['total_sqft']); ?> Sq.Ft</td>
                                        <td>Rs : <?php echo htmlspecialchars($site['price_per_sqft']); ?></td>
                                        <td>Rs : <?php echo htmlspecialchars($site['total_price_sqft']); ?></td>

                                        <td><?php echo htmlspecialchars($site['bookedSqft']); ?> Sq.Ft</td>
                                        <td><?php echo htmlspecialchars($site['avaliableSqft']); ?> Sq.Ft</td>
                                        <td><?php echo htmlspecialchars($site['site_address']); ?></td>

                                        <td>
                                            <a href="#" class="text-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-siteid="<?php echo $site['id']; ?>"
                                                data-site_name="<?php echo $site['site_name']; ?>"
                                                data-total_sqft="<?php echo $site['total_sqft']; ?>"
                                                data-price_per_sqft="<?php echo $site['price_per_sqft']; ?>"
                                                data-total_price_sqft="<?php echo $site['total_price_sqft']; ?>"
                                                data-site_address="<?php echo $site['site_address']; ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="#" class="text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-siteid="<?php echo $site['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No Sites are found</td>
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
</body>

</html>