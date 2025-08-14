<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Material Dashboard 3 by Creative Tim</title>
  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
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
    .card-header,
    .card-footer {
      padding: 1rem; /* Adjust padding */
      background-color: #ffffff; /* White background */
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
    .text-capitalize {
      text-transform: capitalize; /* Capitalize text */
    }
    .text-success {
      color: #28a745; /* Green for success */
    }
    .text-danger {
      color: #dc3545; /* Red for danger */
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <?php include '../includes/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid py-2">
    gsgsg

    <!-- Core JS Files -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard -->
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
  </main>
</body>

</html>
