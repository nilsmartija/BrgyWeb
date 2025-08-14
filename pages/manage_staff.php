<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Manage Residents</title>
    <!-- Fonts and Icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="script.js"></script>
    <style>
        body {
            background-color: #f4f7fa; /* Soft background color */
        }
        .card {
            overflow: hidden; /* Prevent scrollbar */
            border-radius: 12px; /* Round the corners */
            transition: transform 0.3s; /* Animation on hover */
        }
        .card:hover {
            transform: scale(1.02); /* Slight zoom effect */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        }
        .icon {
            width: 40px; /* Ensure icon size is fixed */
            height: 40px; /* Ensure icon size is fixed */
            border-radius: 50%; /* Circular icons */
            background: linear-gradient(135deg, #6e7ff3, #4a5eea); /* Gradient background */
            display: flex; /* Center icon */
            align-items: center;
            justify-content: center;
            color: #ffffff; /* Icon color */
        }

        .table-container {
            margin-top: 20px; /* Space above table */
            overflow-x: auto; /* Enable horizontal scrolling for small screens */
        }
        .table th, .table td {
            vertical-align: middle; /* Center align text vertically */
        }
    </style>
    <script>
        // JavaScript functions for managing staff and SweetAlert notifications
        function openUpdateModal(staff) {
            document.getElementById('updateId').value = staff.id;
            document.getElementById('updateLastName').value = staff.last_name;
            document.getElementById('updateFirstName').value = staff.first_name;
            document.getElementById('updateMiddleName').value = staff.middle_name;
            document.getElementById('updateContactNumber').value = staff.contact_number;
            document.getElementById('updateEmail').value = staff.email;
            document.getElementById('updatePosition').value = staff.position;
            const modal = new bootstrap.Modal(document.getElementById('updateModal'));
            modal.show();
        }

        function confirmAddStaff(event) {
            event.preventDefault(); // Prevent form submission

            const form = document.getElementById('addStaffForm');
            const requiredFields = ['last_name', 'first_name', 'contact_number', 'email', 'password'];
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
                title: 'Add Staff',
                text: "Are you sure you want to add this staff?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add staff!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        }

        function confirmUpdateStaff() {
            const form = document.getElementById('updateModal').querySelector('form');
            const requiredFields = ['last_name', 'first_name', 'contact_number', 'email'];
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
                title: 'Update Staff',
                text: "Are you sure you want to update this Staff?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update Staff!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        }

        function confirmDeleteStaff(id) {
            Swal.fire({
                title: 'Delete Staff',
                text: "Are you sure you want to delete this Staff?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete staff!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_staff.php?delete=' + id;
                }
            });
        }

        // Show SweetAlert notifications based on session message
        document.addEventListener('DOMContentLoaded', function() {
            let title = 'Notification';
            let icon = 'success';
            
            if (message.includes('added')) {
                title = 'Staff added';
            } else if (message.includes('updated')) {
                title = 'Staff updated';
            } else if (message.includes('deleted')) {
                title = 'Staff deleted';
            } else {
                icon = 'error';
            }

            Swal.fire({
                icon: icon,
                title: title,
                text: message,
            });
        });
    </script>
</head>
<body class="g-sidenav-show bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../includes/navbar.php'; ?>
        <div id="content" class="container-fluid">
            <h1>Manage Staff</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Staff</button>

            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Replace with dynamic staff data -->
                        <tr>
                            <td>Sample Name</td>
                            <td>sample@example.com</td>
                            <td>1234567890</td>
                            <td>
                                <button class="btn btn-warning" onclick='openUpdateModal({"id": 1, "last_name": "Doe", "first_name": "John", "middle_name": "A", "contact_number": "1234567890", "email": "john.doe@example.com", "position": "Manager"})'>Update</button>
                                <button class="btn btn-danger" onclick="confirmDeleteStaff(1)">Delete</button>
                            </td>
                        </tr>
                        <!-- Add more staff rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addStaffForm" method="POST">
                        <input type="hidden" name="create" value="1">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Add New Staff</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h7>Last Name:</h7>
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                            <h7>First Name:</h7>
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                            <h7>Middle Name:</h7>
                            <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                            <h7>Contact Number:</h7>
                            <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                            <h7>Email:</h7>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <h7>Password:</h7>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                            <h7>Position:</h7>
                            <input type="text" name="position" class="form-control" placeholder="Position" required>
                            <h7>Sex:</h7>
                            <select name="sex" class="form-control" required>
                                <option value="" disabled selected>Sex</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="confirmAddStaff(event)">Add Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update User Modal -->
        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="update" value="1">
                        <input type="hidden" name="id" id="updateId">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel">Update Staff</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h7>Last Name:</h7>
                            <input type="text" name="last_name" id="updateLastName" class="form-control" required>
                            <h7>First Name:</h7>
                            <input type="text" name="first_name" id="updateFirstName" class="form-control">
                            <h7>Middle Name:</h7>
                            <input type="text" name="middle_name" id="updateMiddleName" class="form-control" required>
                            <h7>Contact Number:</h7>
                            <input type="text" name="contact_number" id="updateContactNumber" class="form-control">
                            <h7>Email:</h7>
                            <input type="email" name="email" id="updateEmail" class="form-control" required>
                            <h7>Position:</h7>
                            <input type="text" name="position" id="updatePosition" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="confirmUpdateStaff()">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    </main>
</body>
</html>
