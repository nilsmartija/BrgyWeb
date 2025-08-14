<?php
session_start();

include '../connections.php'; 

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

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to check if email exists in both users and staff tables
function emailExists($email) {
    global $connections;

    // Check in users table
    $queryUsers = "SELECT * FROM users WHERE email = '$email'";
    $resultUsers = mysqli_query($connections, $queryUsers);
    if (mysqli_num_rows($resultUsers) > 0) {
        return true;
    }

    // Check in staff table
    $queryStaff = "SELECT * FROM staff WHERE email = '$email'";
    $resultStaff = mysqli_query($connections, $queryStaff);
    return mysqli_num_rows($resultStaff) > 0;
}

// Function to handle user addition
function addUser($data) {
    global $connections;

    // Validate email format
    if (!isValidEmail($data['email'])) {
        return "Invalid email format.";
    }

    // Check if email already exists
    if (emailExists($data['email'])) {
        return "Email already registered.";
    }

    $first_name = $data['firstname'];
    $middle_name = $data['middlename'];
    $last_name = $data['lastname'];
    $suffix = $data['suffix'];
    $purok = $data['purok'];
    $contact_number = $data['contact'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $account_type = 3;
    $created_at = date('Y-m-d H:i:s');

    $query_user = "INSERT INTO users (firstname, middlename, lastname, suffix, purok, contact, email, password, account_type, created_at) 
                   VALUES ('$first_name', '$middle_name', '$last_name', '$suffix','$purok', '$contact_number', '$email', '$password', $account_type, '$created_at')";
    
    return mysqli_query($connections, $query_user);
}

// Function to handle user update
function updateUser($data) {
    global $connections;

    // Validate email format
    if (!isValidEmail($data['email'])) {
        return "Invalid email format.";
    }

    // Check if email already exists
    // If it's a different email from the one being updated
    $existingEmailCheck = "SELECT * FROM users WHERE email = '{$data['email']}' AND id != {$data['id']}";
    $result = mysqli_query($connections, $existingEmailCheck);
    if (mysqli_num_rows($result) > 0) {
        return "Email already registered.";
    }

    $id = $data['id'];
    $first_name = $data['firstname'];
    $middle_name = $data['middlename'];
    $last_name = $data['lastname'];
    $suffix = $data['suffix'];
    $purok = $data['purok'];
    $contact_number = $data['contact'];
    $email = $data['email'];

    $query = "UPDATE users SET firstname='$first_name', middlename='$middle_name', lastname='$last_name', suffix='$suffix', purok='$purok', contact='$contact_number', email='$email' WHERE id=$id";
    
    return mysqli_query($connections, $query);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Validate required fields
        if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['purok']) || empty($_POST['contact']) || empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['message'] = "Please fill out all required fields.";
            header("Location: manage_residents.php");
            exit();
        }

        // Attempt to add user
        $addUserResult = addUser($_POST);
        if ($addUserResult === true) {
            $_SESSION['message'] = "User added successfully.";
        } else {
            $_SESSION['message'] = $addUserResult; // Set error message
        }
        header("Location: manage_residents.php");
        exit();
    } elseif (isset($_POST['update'])) {
        // Validate required fields
        if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['purok']) || empty($_POST['contact']) || empty($_POST['email'])) {
            $_SESSION['message'] = "Please fill out all required fields.";
            header("Location: manage_residents.php");
            exit();
        }

        // Attempt to update user
        $updateUserResult = updateUser($_POST);
        if ($updateUserResult === true) {
            $_SESSION['message'] = "User updated successfully.";
        } else {
            $_SESSION['message'] = $updateUserResult; // Set error message
        }
        header("Location: manage_residents.php");
        exit();
    }
}

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($connections, "DELETE FROM blotter_report WHERE user_id=$id");
    mysqli_query($connections, "DELETE FROM users WHERE id=$id");
    $_SESSION['message'] = "User deleted successfully.";
    header("Location: manage_residents.php");
    exit();
}

// Read Data
$result = mysqli_query($connections, "SELECT * FROM users WHERE account_type = 3");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Export function
if (isset($_GET['export'])) {
    if ($_GET['export'] == 'csv') {
        // Export as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="residents.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['First Name', 'Middle Name', 'Last Name', 'Suffix', 'Purok', 'Contact Number', 'Email']); // Column headers

        foreach ($users as $user) {
            fputcsv($output, [
                $user['firstname'],
                $user['middlename'],
                $user['lastname'],
                $user['suffix'],
                $user['purok'],
                $user['contact'],
                $user['email']
            ]);
        }

        fclose($output);
        exit();
    } elseif ($_GET['export'] == 'pdf') {
        // Export as PDF
        require_once(__DIR__ . '/../vendor/autoload.php'); // Adjust path to include autoload.php from parent directory

        // Create new PDF document
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Title
        $pdf->Cell(0, 10, 'Residents List', 0, 1, 'C');

        // Column headers
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(30, 10, 'First Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Middle Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Last Name', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Suffix', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Purok', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Contact Number', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Email', 1, 1, 'C');
        
        // Data
        $pdf->SetFont('helvetica', '', 10);
        foreach ($users as $user) {
            $pdf->Cell(30, 10, $user['firstname'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['middlename'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['lastname'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['suffix'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['purok'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['contact'], 1, 0, 'C');
            $pdf->Cell(30, 10, $user['email'], 1, 1, 'C');
        }

        // Output PDF
        $pdf->Output('residents.pdf', 'D'); // Force download as 'residents.pdf'
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
    <title>Admin Dashboard - Manage Residents</title>
    
    <!-- Google Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Function to open the Update Modal
        function openUpdateModal(user) {
            document.getElementById('updateId').value = user.id;
            document.getElementById('updateFirstName').value = user.firstname;
            document.getElementById('updateMiddleName').value = user.middlename;
            document.getElementById('updateLastName').value = user.lastname;
            document.getElementById('updateSuffix').value = user.suffix;
            document.getElementById('updatePurok').value = user.purok;
            document.getElementById('updateContactNumber').value = user.contact;
            document.getElementById('updateEmail').value = user.email;

            const modal = new bootstrap.Modal(document.getElementById('updateModal'));
            modal.show();
        }

        // Confirm User Add
        function confirmAddUser(event) {
            event.preventDefault(); // Prevent form submission

            const form = document.getElementById('addUserForm');
            const requiredFields = ['firstname', 'lastname', 'purok','contact', 'email', 'password'];
            let isValid = true;

            requiredFields.forEach(field => {
                if (!form[field].value) {
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Input',
                    text: 'Please fill out all required fields.',
                });
                return;
            }

            Swal.fire({
                title: 'Add User',
                text: "Are you sure you want to add this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add user!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        }

        // Confirm Update User
        function confirmUpdateUser() {
            const form = document.getElementById('updateModal').querySelector('form');
            const requiredFields = ['firstname', 'lastname', 'purok','contact', 'email'];
            let isValid = true;

            requiredFields.forEach(field => {
                if (!form[field].value) {
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Input',
                    text: 'Please fill out all required fields.',
                });
                return;
            }

            Swal.fire({
                title: 'Update User',
                text: "Are you sure you want to update this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update user!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        }

        // Confirm Delete User
        function confirmDeleteUser(id) {
            Swal.fire({
                title: 'Delete User',
                text: "Are you sure you want to delete this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_residents.php?delete=' + id;
                }
            });
        }

        // Show SweetAlert notifications based on session message
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])): ?>
                const message = <?= json_encode($_SESSION['message']); ?>;
                let title = 'Notification';
                let icon = 'success';
                
                if (message.includes('added')) {
                    title = 'Resident added';
                } else if (message.includes('updated')) {
                    title = 'Resident updated';
                } else if (message.includes('deleted')) {
                    title = 'Resident deleted';
                } else {
                    icon = 'error';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        });
    </script>
</head>
<body class="g-sidenav-show bg-gray-100">
    <?php include '../includes/staffsidebar.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../includes/navbar.php'; ?>

        <div id="content" class="container-fluid py-2">
            <h1 class="mb-4">Manage Residents</h1>
            
            <!-- Add User Button -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-user-plus"></i> Add User
            </button>
            
            <!-- Search Residents -->
            <?php include 'res_search.php'; ?>
            
            <!-- Residents Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Purok</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= ucfirst(strtolower($user['lastname'])) . ', ' . ucfirst(strtolower($user['firstname'])) . ' ' . ucfirst(substr(strtolower($user['middlename']), 0, 1)) . '.' ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['purok'] ?></td>
                        <td><?= $user['contact'] ?></td>
                        <td>
                            <button class="btn btn-warning" onclick='openUpdateModal(<?= json_encode($user) ?>)'>
                                <i class="fas fa-edit"></i> Update
                            </button>
                            <button class="btn btn-danger" onclick="confirmDeleteUser(<?= $user['id'] ?>)">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Export Buttons -->
            <a href="manage_residents.php?export=csv" class="btn btn-success">
                <i class="fas fa-file-csv"></i> Export to CSV
            </a>
            <a href="manage_residents.php?export=pdf" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>
    </main>

    <!-- Add Residents Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addUserForm" method="POST">
                    <input type="hidden" name="create" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="addFirstName" class="form-label">First Name</label>
                                <input type="text" name="firstname" id="addFirstName" class="form-control" placeholder="First Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addMiddleName" class="form-label">Middle Name</label>
                                <input type="text" name="middlename" id="addMiddleName" class="form-control" placeholder="Middle Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="addLastName" class="form-label">Last Name</label>
                                <input type="text" name="lastname" id="addLastName" class="form-control" placeholder="Last Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addSuffix" class="form-label">Suffix</label>
                                <input type="text" name="suffix" id="addSuffix" class="form-control" placeholder="Suffix (if any)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="addPurok" class="form-label">Purok</label>
                                <select name="purok" id="addPurok" class="form-control" required>
                                    <option value="" disabled selected>Purok</option>
                                    <option value="1">Purok 1</option>
                                    <option value="2">Purok 2</option>
                                    <option value="3">Purok 3</option>
                                    <option value="4">Purok 4</option>
                                    <option value="5">Purok 5</option>
                                    <option value="6">Purok 6</option>
                                    <option value="7">Purok 7</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addContact" class="form-label">Contact Number</label>
                                <input type="text" name="contact" id="addContact" class="form-control" placeholder="Contact Number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="addEmail" class="form-label">Email</label>
                                <input type="email" name="email" id="addEmail" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addPassword" class="form-label">Password</label>
                                <input type="password" name="password" id="addPassword" class="form-control" placeholder="Password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmAddUser(event)">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Residents Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" id="updateId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateFirstName" class="form-label">First Name</label>
                                <input type="text" name="firstname" id="updateFirstName" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="updateMiddleName" class="form-label">Middle Name</label>
                                <input type="text" name="middlename" id="updateMiddleName" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateLastName" class="form-label">Last Name</label>
                                <input type="text" name="lastname" id="updateLastName" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="updateSuffix" class="form-label">Suffix</label>
                                <input type="text" name="suffix" id="updateSuffix" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updatePurok" class="form-label">Purok</label>
                                <select name="purok" id="updatePurok" class="form-control" required>
                                    <option value="" disabled selected>Purok</option>
                                    <option value="1">Purok 1</option>
                                    <option value="2">Purok 2</option>
                                    <option value="3">Purok 3</option>
                                    <option value="4">Purok 4</option>
                                    <option value="5">Purok 5</option>
                                    <option value="6">Purok 6</option>
                                    <option value="7">Purok 7</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="updateContactNumber" class="form-label">Contact Number</label>
                                <input type="text" name="contact" id="updateContactNumber" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateEmail" class="form-label">Email</label>
                                <input type="email" name="email" id="updateEmail" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmUpdateUser()">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

