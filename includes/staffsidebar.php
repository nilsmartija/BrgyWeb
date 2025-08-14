<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main" style="background: linear-gradient(180deg, #6a11cb 0%, #2575fc 100%); overflow: hidden; height: 100vh;">
    <div class="logo" style="display: flex; justify-content: center; align-items: center; max-width: 150px; margin: 0 auto;">
        <img src="../brgylogo.png" alt="Logo" style="width: 100%; height: auto;">
    </div>
    <p class="text-white" style="text-align: center; font-weight: bold;">Brgy Management System</p>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active bg-gradient-dark text-white' : 'text-white'; ?>" href="../staff/dashboard.php">
                    <i class='bx bxs-dashboard opacity-5'></i>
                    <span class="nav-link-text ms-1 text-white">Staff Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'announcement.php' ? 'active bg-gradient-dark text-white' : 'text-white'; ?>" href="../staff/announcement.php">
                    <i class='bx bxs-dashboard opacity-5'></i>
                    <span class="nav-link-text ms-1 text-white">Manage Announcement</span>
                </a>
            </li>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_residents.php' ? 'active bg-gradient-dark text-white' : 'text-white'; ?>" href="../staff/manage_residents.php">
                    <i class='bx bx-group'></i>
                    <span class="nav-link-text ms-1 text-white">Manage Residents</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_blotter_report.php' ? 'active bg-gradient-dark text-white' : 'text-white'; ?>" href="../staff/manage_blotter_report.php">
                    <i class='bx bx-group'></i>
                    <span class="nav-link-text ms-1 text-white">Manage Blotter Report</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'logout.php' ? 'active bg-gradient-dark text-white' : 'text-white'; ?>" href="../staff/logout.php">
                    <i class='bx bx-log-out opacity-5'></i>
                    <span class="nav-link-text ms-1 text-white">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
