<?php
// Include your database connection
require_once('../connections.php');

// Initialize the search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare SQL query with a placeholder (?)
$query = "SELECT * FROM staff WHERE CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ? ORDER BY last_name, first_name";

// Prepare the statement using the mysqli connection
$stmt = $connections->prepare($query);

// Bind the search parameter (using % for partial matching)
$searchParam = '%' . $search . '%';
$stmt->bind_param('s', $searchParam); // 's' means the parameter is a string

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();


// Fetch all users that match the search criteria
$staff = []; // Initialize an empty array to hold staff data
while ($row = $result->fetch_assoc()) { // Change variable to $row for each iteration
    $staff[] = $row; // Add each result row to the $staff array
}


// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Form</title>

    <!-- Internal CSS -->
    <style>
        /* Center the form */
        .centered-form-wrapper {
            display: flex;
            justify-content: center;
            margin-left: 250px;
            margin-top: -40px; /* Optional, adjust for spacing */
        }

        .centered-form {
            max-width: 500px; /* Max width for the form */
            width: 100%; /* Make it responsive */
            max-height: 60px; /* Limit height of the form */
            overflow: hidden; /* Hide overflow if content exceeds max height */
        }

        .centered-form input {
            width: 100%; /* Make input fill the available space */
            max-width: 400px; /* Limit the input width */
            height: 40px; /* Limit the height of the input field */
        }

        .centered-form button {
            height: 40px; /* Same height as input */
        }

        /* Optional: Style for input-group */
        .input-group {
            display: flex;
            justify-content: space-between;
        }

        .input-group .form-control {
            border-right: 0; /* Remove right border for a seamless button look */
        }

        .input-group .btn {
            border-left: 0; /* Remove left border to match the input */
        }
    </style>
</head>
<body>

<!-- Centered Search Form with Limited Width and Height -->
<div class="centered-form-wrapper">
    <form action="manage_staff.php" method="get" class="centered-form">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search by Name" name="search" value="<?= htmlspecialchars  ($search) ?>" aria-label="Search by Name">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
</div>



</body>
</html>
