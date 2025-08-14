<?php
session_start();
include '../connections.php'; 

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

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to check if email exists in both users and staff tables
function emailExists($email) {
    global $connections;

    // Check in users table
    $queryUsers = "SELECT * FROM staff WHERE email = '$email'";
    $resultUsers = mysqli_query($connections, $queryUsers);
    if (mysqli_num_rows($resultUsers) > 0) {
        return true;
    }

    // Check in staff table
    $queryStaff = "SELECT * FROM users WHERE email = '$email'";
    $resultStaff = mysqli_query($connections, $queryStaff);
    return mysqli_num_rows($resultStaff) > 0;
}


// Function to handle user addition
function addStaff($data) {
    global $connections;


     // Validate email format
     if (!isValidEmail($data['email'])) {
        return "Invalid email format.";
    }

    // Check if email already exists
    if (emailExists($data['email'])) {
        return "Email already registered.";
    }
    
    $last_name = $data['last_name'];
    $first_name = $data['first_name'];
    $middle_name = $data['middle_name'];
    $contact_number = $data['contact_number'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $position = $data['position'];
    $sex = $data['sex'];
    $account_type = 2;

    

    $query_staff = "INSERT INTO staff (last_name, first_name, middle_name, contact_number, email, password, position, sex, account_type) 
                   VALUES ('$last_name', '$first_name', '$middle_name', '$contact_number', '$email', '$password', '$position', '$sex', '$account_type')";
    
    if (mysqli_query($connections, $query_staff)) {
        return true; // Successfully added staff
    } else {
        return "Error adding Staff: " . mysqli_error($connections);
    }
}

// Function to handle user update
function updateStaff($data) {
    global $connections;
    $staff_id = $data['id'];
    $last_name = $data['last_name'];
    $first_name = $data['first_name'];
    $middle_name = $data['middle_name'];
    $contact_number = $data['contact_number'];
    $email = $data['email'];

    // Corrected query
    $query = "UPDATE staff SET last_name='$last_name', first_name='$first_name', middle_name='$middle_name', contact_number='$contact_number', email='$email' WHERE id=$staff_id";
    
    return mysqli_query($connections, $query);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Validate required fields
        if (empty($_POST['last_name']) || empty($_POST['first_name']) || empty($_POST['contact_number']) || empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['message'] = "Please fill out all required fields.";
            header("Location: manage_staff.php");
            exit();
        }
        
          // Attempt to add user
          $result = addStaff($_POST);
          if ($result === true) {
              $_SESSION['message'] = "Staff added successfully.";
          } else {
              $_SESSION['message'] = $result; // Get the specific error message
          }
          header("Location: manage_staff.php");
          exit();
      } elseif (isset($_POST['update'])) {
          // Validate required fields
          if (empty($_POST['last_name']) || empty($_POST['first_name']) || empty($_POST['contact_number']) || empty($_POST['email'])) {
              $_SESSION['message'] = "Please fill out all required fields.";
              header("Location: manage_staff.php");
              exit();
          }
  
          // Attempt to update user
          if (updateStaff($_POST)) {
              $_SESSION['message'] = "Staff updated successfully.";
          } else {
              $_SESSION['message'] = "Error updating Staff.";
          }
          header("Location: manage_staff.php");
          exit();
      }
  }

// Delete User
if (isset($_GET['delete'])) {
    $staff_id = $_GET['delete'];
    // mysqli_query($connections, "DELETE FROM blotter_report WHERE user_id=$id");
    mysqli_query($connections, "DELETE FROM staff WHERE id=$staff_id");
    $_SESSION['message'] = "Staff deleted successfully.";
    header("Location: manage_staff.php");
    exit();
}

// Read Data
$result = mysqli_query($connections, "SELECT * FROM staff WHERE account_type = 2");
$staff = mysqli_fetch_all($result, MYSQLI_ASSOC);


require_once(__DIR__ . '/../vendor/autoload.php'); // Adjust path to autoload.php

// Export function
if (isset($_GET['export'])) {
    if ($_GET['export'] == 'csv') {
        // Export as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="staff.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Last Name', 'First Name', 'Middle Name', 'Contact Number', 'Email', 'Position']); // Column headers

        foreach ($staff as $staff_member) {
            fputcsv($output, [
                $staff_member['last_name'],
                $staff_member['first_name'],
                $staff_member['middle_name'],
                $staff_member['contact_number'],
                $staff_member['email'],
                $staff_member['position']
            ]);
        }

        fclose($output);
        exit();
    } elseif ($_GET['export'] == 'pdf') {
        // Export as PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Title
        $pdf->Cell(0, 10, 'Staff List', 0, 1, 'C');

        // Column headers
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(30, 10, 'Last Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'First Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Middle Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Contact Number', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Email', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Position', 1, 1, 'C');
        
        // Data
        $pdf->SetFont('helvetica', '', 10);
        foreach ($staff as $staff_member) {
            $pdf->Cell(30, 10, $staff_member['last_name'], 1, 0, 'C');
            $pdf->Cell(30, 10, $staff_member['first_name'], 1, 0, 'C');
            $pdf->Cell(30, 10, $staff_member['middle_name'], 1, 0, 'C');
            $pdf->Cell(40, 10, $staff_member['contact_number'], 1, 0, 'C');
            $pdf->Cell(60, 10, $staff_member['email'], 1, 0, 'C');
            $pdf->Cell(30, 10, $staff_member['position'], 1, 1, 'C');
        }

        // Output PDF
        $pdf->Output('staff.pdf', 'D'); // Force download as 'residents.pdf'
        exit();
    }
}
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
        /* Styling for Dashboard and Metrics */
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
            <!-- Title of the page -->
            <center>
                <h1>Staff Dashboard</h1>
            </center>

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

            <!-- Manage Staff Content -->
            <h2>Manage Staff</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Staff</button>

            <?php include 'staff_search.php'; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $staff): ?>
                    <tr>
                        <td><?= ucfirst(strtolower($staff['last_name'])) . ', ' . ucfirst(strtolower($staff['first_name'])) . ' ' . ucfirst(substr(strtolower($staff['middle_name']), 0, 1)) . '.' ?></td>
                        <td><?= $staff['email'] ?></td>
                        <td><?= $staff['contact_number'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick='openUpdateModal(<?= json_encode($staff) ?>)'>Update</button>
                            <button class="btn btn-danger" onclick="confirmDeleteStaff(<?= $staff['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <br>
            <a href="manage_staff.php?export=csv" class="btn btn-success">Export to CSV</a> 
            &nbsp; &nbsp; &nbsp;
            <a href="manage_staff.php?export=pdf" class="btn btn-primary">Export to PDF</a>

        </div> <!-- .container-fluid -->

    </main> <!-- .main-content -->

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addStaffForm" method="POST">
                    <input type="hidden" name="create" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add New Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields for Add Staff -->
                        <!-- Add your staff form fields here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmAddStaff(event)">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Staff Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="updateStaffForm" method="POST">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="staff_id" id="updateStaffId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields for Update Staff -->
                        <!-- Add your staff update form fields here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmUpdateStaff(event)">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteStaff(staffId) {
            if (confirm('Are you sure you want to delete this staff member?')) {
                window.location.href = `delete_staff.php?id=${staffId}`;
            }
        }

        function openUpdateModal(staffData) {
            document.getElementById('updateStaffId').value = staffData.id;
            // Populate form fields with existing data
            // Example: document.getElementById('updateName').value = staffData.name;
            // Open the modal
            var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModal.show();
        }
    </script>

    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>

</html>
