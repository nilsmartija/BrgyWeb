<?php
session_start();

// Access for User Account only
if (!isset($_SESSION["user_id"]) || $_SESSION["account_type"] != "3") {

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Access Denied',
                    text: 'Normal Acc lang ang may access dito',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.back(); // Redirects back to the previous page
                    }
                });
            });
        </script>
    </head>
    <body>
    </body>
    </html>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Admin Dashboard - Announcements</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

    <!-- Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

    <!-- Bootstrap (for layout and icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        #announcementList {
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .announcement-item {
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .list-group-item {
            padding: 15px;
            border-radius: 8px;
        }

        .list-group-item h5 {
            font-weight: bold;
        }

        .container-fluid {
            padding: 30px;
        }

        .announcement-item p {
            margin-bottom: 10px;
        }

        .announcement-item small {
            color: #6c757d;
        }

        .announcement-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">

    <!-- Sidebar -->
    <?php include '../includes/usersidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid py-4">
            <!-- Announcement Section -->
            <div class="announcement-header mb-4">
                <h3><i class="fas fa-bullhorn"></i> Current Announcements</h3>
            </div>

            <div class="list-group" id="announcementList">
                <?php
                // Include database connection
                include '../connections.php';

                // Fetch and display announcements (read-only)
                $result = mysqli_query($connections, "SELECT * FROM announcement ORDER BY date DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='list-group-item announcement-item mb-3'>";
                    
                    // Format the date to 'F j, Y' (e.g., August 23, 2023)
                    $formatted_date = date("F j, Y", strtotime($row['date']));
                    
                    echo "<h5><i class='fas fa-info-circle'></i> Announcement</h5>";
                    echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>"; // Line breaks for the message
                    echo "<small class='text-muted'>Posted on: " . $formatted_date . "</small>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        
        <!-- Scripts -->
        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>
        <script src="../assets/js/plugins/chartjs.min.js"></script>
    </main>

</body>
</html>

