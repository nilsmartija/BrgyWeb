<?php
session_start(); 

// Access for Staff Account only
if (!isset($_SESSION["staff_id"]) || $_SESSION["account_type"] != "2") {
    
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
                    text: 'Staff lang ang may access dito',
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
    <title>Admin Dashboard - Manage Announcements</title>
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    
    <!-- Icon Libraries -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Bootstrap and Material Dashboard Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <style>
        /* Additional custom styles can go here */
    </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <?php include '../includes/staffsidebar.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid py-2">
            <h3>Manage Announcements</h3>
            
            <!-- Add Announcement Button -->
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                <i class="fas fa-plus-circle"></i> Add Announcement
            </button>

            <!-- Announcements List -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h4>Announcements List</h4>
                    <div class="list-group" id="announcementList">
                        <?php
                            include '../connections.php';

                            // Insert, delete, update logic
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $message = mysqli_real_escape_string($connections, $_POST['message']);
                                
                                if (isset($_POST['announcementId']) && !empty($_POST['announcementId'])) {
                                    $announcementId = intval($_POST['announcementId']);
                                    
                                    // Fetch existing announcement
                                    $existingQuery = "SELECT message FROM announcement WHERE id = $announcementId";
                                    $existingResult = mysqli_query($connections, $existingQuery);
                                    $existingRow = mysqli_fetch_assoc($existingResult);
                                    
                                    // Prepare the new message
                                    $newMessage = ($existingRow['message'] === $message) ? $message : $message . "\n\n(edited)";
                                    $sql = "UPDATE announcement SET message = '$newMessage', date = NOW() WHERE id = $announcementId";

                                    if (mysqli_query($connections, $sql)) {
                                        echo "<script>Swal.fire('Success!', 'Announcement updated successfully.', 'success');</script>";
                                    } else {
                                        echo "<script>Swal.fire('Error!', 'Error: " . mysqli_error($connections) . "', 'error');</script>";
                                    }
                                } else {
                                    $sql = "INSERT INTO announcement (message, date) VALUES ('$message', NOW())";

                                    if (mysqli_query($connections, $sql)) {
                                        echo "<script>Swal.fire('Success!', 'Announcement added successfully.', 'success');</script>";
                                    } else {
                                        echo "<script>Swal.fire('Error!', 'Error: " . mysqli_error($connections) . "', 'error');</script>";
                                    }
                                }
                            }

                            // Delete Announcement Logic
                            if (isset($_GET['delete_id'])) {
                                $deleteId = intval($_GET['delete_id']);
                                $sql = "DELETE FROM announcement WHERE id = $deleteId";

                                if (mysqli_query($connections, $sql)) {
                                    echo "<script>Swal.fire('Success!', 'Announcement deleted successfully.', 'success');</script>";
                                } else {
                                    echo "<script>Swal.fire('Error!', 'Error: " . mysqli_error($connections) . "', 'error');</script>";
                                }
                            }

                            // Fetch and display announcements
                            $result = mysqli_query($connections, "SELECT * FROM announcement ORDER BY date DESC");
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
                                echo "<div class='me-auto'>";
                                echo nl2br(htmlspecialchars($row['message'])) . "<br>";
                                echo "<small>Posted on: " . $row['date'] . "</small>";
                                echo "</div>";
                                echo "<div class='btn-group'>";
                                echo "<button class='btn btn-warning' onclick='editAnnouncement(" . $row['id'] . ", " . json_encode($row['message']) . ")'><i class='fas fa-edit'></i> Update</button>";
                                echo "<span style='margin: 0 5px;'></span>"; 
                                echo "<a href='announcement.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirmDelete(event)'><i class='fas fa-trash-alt'></i> Delete</a>";
                                echo "</div>";
                                echo "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Add Announcement Modal -->
            <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAnnouncementModalLabel">Add Announcement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modalAnnouncementForm" action="announcement.php" method="POST">
                                <input type="hidden" id="modalAnnouncementId" name="announcementId">
                                <div class="mb-3">
                                    <label for="modalMessage" class="form-label">Announcement Message:</label>
                                    <textarea class="form-control" id="modalMessage" name="message" rows="4" placeholder="Enter announcement message" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success" id="modalSubmitBtn"><i class="fas fa-check-circle"></i> Add Announcement</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- JavaScript Files -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>
    
    <script>
        // Edit Announcement Function
        function editAnnouncement(id, message) {
            document.getElementById('modalAnnouncementId').value = id;
            document.getElementById('modalMessage').value = message.replace(/\n\n\(edited\)/, ''); // Remove "(edited)" if editing
            document.getElementById('addAnnouncementModalLabel').innerText = 'Update Announcement';
            document.getElementById('modalSubmitBtn').innerText = 'Update Announcement';

            var modal = new bootstrap.Modal(document.getElementById('addAnnouncementModal'));
            modal.show();
        }

        // Confirm Delete Function
        function confirmDelete(event) {
            event.preventDefault();
            const link = event.currentTarget;
            Swal.fire({
                title: 'Are you sure?',
                text: 'This announcement will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link.href;
                }
            });
        }

        // Form Submit Confirmation
        document.getElementById('modalAnnouncementForm').onsubmit = function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to save this announcement?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        };
    </script>
</body>
</html>

