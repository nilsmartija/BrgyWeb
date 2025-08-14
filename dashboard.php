<?php
session_start(); 

// Access for Admin Account only
if (!isset($_SESSION["user_id"]) || $_SESSION["account_type"] != "1") {
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
                    text: 'Admin lang ang may access dito',
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

// Include database connection
include '../connections.php';

// Queries
$total_residents_query = "SELECT COUNT(id) as total FROM users WHERE account_type != '1'"; // Exclude admin
$total_residents_result = $connections->query($total_residents_query);
$total_residents = $total_residents_result->fetch_assoc()['total'];

$pending_reports_query = "SELECT COUNT(*) as pending FROM blotter_report WHERE status = 'pending'";
$pending_reports_result = $connections->query($pending_reports_query);
$pending_reports = $pending_reports_result->fetch_assoc()['pending'];

$scheduled_meetings_query = "SELECT COUNT(*) as finished FROM blotter_report WHERE status = 'finished'";
$scheduled_meetings_result = $connections->query($scheduled_meetings_query);
$scheduled_meetings = $scheduled_meetings_result->fetch_assoc()['finished'];

// Fetch data for the pie chart
$purok_counts_query = "SELECT purok, COUNT(*) as count FROM users WHERE account_type != '1' GROUP BY purok ORDER BY purok ASC";
$purok_counts_result = $connections->query($purok_counts_query);

$purok_data = [];
$total_count = 0;

// Initialize counts for purok 1 to 7
for ($i = 1; $i <= 7; $i++) {
    $purok_data[$i] = 0;
}

while ($row = $purok_counts_result->fetch_assoc()) {
    $purok = (int)$row['purok'];
    $purok_data[$purok] = (int)$row['count'];
    $total_count += $row['count'];
}

$connections->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-container {
            border: 1px solid #ccc; 
            border-radius: 5px; 
            padding: 15px; 
            margin-bottom: 15px; 
            background-color: #f9f9f9; 
            width: calc(30% - 20px); 
        }

        .dashboard-metrics {
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            justify-content: center;
            text-align: center;
        }

        .metric-container h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .metric-container p {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .metric-container .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        #content {
            padding: 20px; 
        }

        #pie-chart {
            max-width: 600px; 
            margin: 0 auto; 
        }

        #total-residents {
            color: #3498db;
        }

        #pending-reports {
            color: #f39c12;
        }

        #scheduled-meetings {
            color: #2ecc71;
        }

    </style>
</head>
<body class="g-sidenav-show bg-gray-100">

    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid py-2">
            <center><h1>Staff Dashboard</h1></center>

            <hr>
            
            <!-- Dashboard content -->
            <div class="dashboard-metrics">
                <div class="metric-container">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>Total Residents</h2>
                    <p><span id="total-residents"><?php echo $total_residents; ?></span></p>
                </div>
                <div class="metric-container">
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>Pending Blotter Reports</h2>
                    <p><span id="pending-reports"><?php echo $pending_reports; ?></span></p>
                </div>
                <div class="metric-container">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h2>Scheduled Meetings</h2>
                    <p><span id="scheduled-meetings"><?php echo $scheduled_meetings; ?></span></p>
                </div>
            </div>

            <!-- Pie Chart -->
            <div id="pie-chart">
                <canvas id="purokPieChart"></canvas>
            </div>

            <script>
                const ctx = document.getElementById('purokPieChart').getContext('2d');
                const totalResidents = <?php echo $total_count; ?>;
                const purokCounts = [<?php echo implode(',', $purok_data); ?>];
                const purokLabels = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6', 'Purok 7'];

                const pieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: purokLabels,
                        datasets: [{
                            data: purokCounts,
                            backgroundColor: [
                                '#B0C4DE', // Light Steel Blue
                                '#ADD8E6', // Light Blue
                                '#87CEFA', // Light Sky Blue
                                '#4682B4', // Steel Blue
                                '#6495ED', // Cornflower Blue
                                '#5F9EA0', // Cadet Blue
                                '#6A5ACD'  // Slate Blue
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const count = tooltipItem.raw;
                                        const percentage = totalResidents ? ((count / totalResidents) * 100).toFixed(2) : 0;
                                        return `${tooltipItem.label}: ${count} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>

            <script src="../assets/js/core/popper.min.js"></script>
            <script src="../assets/js/core/bootstrap.min.js"></script>
            <script src="../assets/js/plugins/chartjs.min.js"></script>
        </div>
    </main>

</body>
</html>

