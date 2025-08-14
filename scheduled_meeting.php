<?php
include '../connections.php';
session_start();

// Access for Admin Account only
if (!isset($_SESSION["user_id"]) || $_SESSION["account_type"] != "1") {
    header("Location: access_denied.php");
    exit();
}

// Fetch all meetings, including canceled, ordered by created_at for latest reports first
$scheduled_query = "
    SELECT br.*, u.firstname, u.lastname 
    FROM blotter_report br 
    JOIN users u ON br.user_id = u.id 
    ORDER BY br.created_at DESC
";
$scheduled_result = mysqli_query($connections, $scheduled_query);

// Function to format the date
function formatDate($date) {
    return date('F j, Y', strtotime($date)); // e.g., August 21, 2024
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Admin Dashboard - Scheduled Meetings</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .main-content {
            padding: 20px;
        }
        #content {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #343a40;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .table td i {
            margin-right: 10px;
        }
        .sidebar {
            background-color: #343a40;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar a {
            color: #fff;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .navbar {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 999;
        }
        .navbar .title {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style="margin-left: 250px; padding-top: 80px;">
        <!-- Navbar -->
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid py-4">
            <!-- Scheduled Meetings Table -->
            <div id="content">
                <h2 class="mb-4"><i class="fas fa-calendar-check"></i> Scheduled Meetings</h2>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Report</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Meeting Date</th>
                                <th>Meeting Time</th>
                                <th>Date Reported</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($scheduled_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['firstname']) ?></td>
                                <td><?= htmlspecialchars($row['lastname']) ?></td>
                                <td><?= htmlspecialchars($row['report_content']) ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td>
                                    <i class="fas fa-circle <?= $row['status'] == 'completed' ? 'text-success' : ($row['status'] == 'pending' ? 'text-warning' : 'text-danger') ?>"></i>
                                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                </td>
                                <td><?= $row['meeting_date'] ? formatDate($row['meeting_date']) : 'N/A' ?></td>
                                <td><?= $row['meeting_time'] ? date('h:i A', strtotime($row['meeting_time'])) : 'N/A' ?></td>
                                <td>
                                    <?= formatDate($row['created_at']) ?><br>
                                    <?= date('h:i A', strtotime($row['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>
        <script src="../assets/js/plugins/chartjs.min.js"></script>
    </main>
</body>
</html>

