<!-- Sidebar Start -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <!-- Logo section -->
            <a href="./index.php" class="text-nowrap logo-img d-flex align-items-center">
                <img src="./assets/logo.jpg" width="60" alt="SR Promoters Logo" class="logo-img">
                <span class="ms-2 fs-4 fw-bold">SR Promoters</span> <!-- Text with styling -->
            </a>

            <!-- Close button for sidebar on smaller screens -->
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">MENU</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./index.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./agents.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Agents</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./users.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Customer</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./site.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Site Details</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="allocate.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Allocate Site to Customer</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./emi.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">EMI</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./payment.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Payments</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="./report.php" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Report</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!--  Sidebar End -->


<style>
    /* Styling for the logo container */
    .logo-img {
        height: 60px;
        /* Ensuring the logo has a fixed height */
        object-fit: contain;
        /* Maintain aspect ratio without stretching */
    }

    /* Styling for the "SR Promoters" text */
    .brand-logo a span {
        color: #000;
        /* Black text color */
        font-family: 'Arial', sans-serif;
        /* You can change this to your preferred font */
        font-size: 24px;
        /* Adjust the text size */
        font-weight: bold;
        /* Bold text */
        margin-left: 10px;
        /* Add some space between the logo and the text */
    }

    /* Optional: Add hover effect on the logo and text */
    .brand-logo a:hover span {
        color: #007bff;
        /* Color change on hover */
    }

    .brand-logo a:hover .logo-img {
        opacity: 0.8;
        /* Slight opacity change on hover */
    }

    /* Sidebar Close Button Styling */
    .close-btn {
        cursor: pointer;
    }

    /* Responsive: Close button visible only on smaller screens */
    .d-xl-none {
        display: none;
    }

    @media (max-width: 1200px) {
        .d-xl-none {
            display: block;
        }
    }
</style>