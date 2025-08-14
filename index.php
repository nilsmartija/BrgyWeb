<?php
// Include database connection
include("connections.php");

$fName = $mName = $lName = $suffix = $contact = $email = $password = $cpassword = "";
$fnameErr = $mnameErr = $lnameErr = $suffixErr = $contactErr = $emailErr = $passwordErr = $cpasswordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate First Name
    if (empty($_POST["fName"])) {
        $fnameErr = "First name is required";
    } else {
        $fName = $_POST["fName"];
    }

    // Validate Middle Name
    if (empty($_POST["mName"])) {
        $mnameErr = "Middle name is required";
    } else {
        $mName = $_POST["mName"];
    }

    // Validate Last Name
    if (empty($_POST["lName"])) {
        $lnameErr = "Last name is required";
    } else {
        $lName = $_POST["lName"];
    }

    // Validate Suffix
    if (empty($_POST["suffix"])) {
        $suffix = ""; // Suffix is optional
    } else {
        $suffix = $_POST["suffix"];
    }

    // Validate Contact Number
    if (empty($_POST["contact"])) {
        $contactErr = "Contact number is required";
    } else {
        $contact = $_POST["contact"];
    }

    // Validate Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
    }

    // Validate Password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
    }

    // Validate Confirm Password
    if (empty($_POST["cpassword"])) {
        $cpasswordErr = "Confirm Password is required";
    } else {
        $cpassword = $_POST["cpassword"];
    }

    // Check if all required fields are filled and passwords match
    if ($fName && $mName && $lName && $contact && $email && $password && $cpassword && $password === $cpassword) {
        // Check if the email is already registered
        $check_email = mysqli_query($connections, "SELECT * FROM users WHERE email ='$email'");
        $check_email_row = mysqli_num_rows($check_email);

        if ($check_email_row > 0) {
            $emailErr = "Email is already registered";
        } else {
            // Hash the password before inserting (recommended)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = mysqli_query($connections, "INSERT INTO users (firstname, middlename, lastname, suffix, contact, email, password, account_type) 
            VALUES ('$fName', '$mName', '$lName', '$suffix', '$contact', '$email', '$hashed_password', '3')"); // Default account type is 3 (Client)

            // Check if the query was successful
            if (!$query) {
                // Output error if the query fails
                die("Error inserting data: " . mysqli_error($connections));
            } else {
                // Redirect to login page
                header("Location: login.php"); // Change to the correct login page URL
                exit; 
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- Bootstrap and Custom Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            font-family: 'Poppins', sans-serif;
        }

        #form {
            width: 300px;
            margin: 20vh auto 0 auto;
            padding: 20px;
            background-color: whitesmoke;
            border-radius: 4px;
            font-size: 12px;
        }

        #form h1 {
            color: #0f2027;
            text-align: center;
        }

        #form button {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            color: white;
            background-color: rgb(41, 57, 194);
            border: none;
            border-radius: 4px;
        }

        .input-control {
            display: flex;
            flex-direction: column;
        }

        .input-control input {
            border: 2px solid #f0f0f0;
            border-radius: 4px;
            display: block;
            font-size: 12px;
            padding: 10px;
            width: 100%;
        }

        .input-control input:focus {
            outline: 0;
        }

        .input-control.success input {
            border-color: #09c372;
        }

        .input-control.error input {
            border-color: #ff3860;
        }

        .input-control .error {
            color: #ff3860;
            font-size: 9px;
            height: 13px;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<?php include("splash.html"); ?>

<div class="form-container">
    <div class="form-box">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1>Register</h1>

            <div class="row mb-3">
                <div class="col">
                    <div class="input-control">
                        <input type="text" class="form-control" name="fName" placeholder="First Name" value="<?php echo htmlspecialchars($fName); ?>">
                        <span class="error"><?php echo $fnameErr; ?></span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-control">
                        <input type="text" class="form-control" name="mName" placeholder="Middle Name" value="<?php echo htmlspecialchars($mName); ?>">
                        <span class="error"><?php echo $mnameErr; ?></span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-control">
                        <input type="text" class="form-control" name="lName" placeholder="Last Name" value="<?php echo htmlspecialchars($lName); ?>">
                        <span class="error"><?php echo $lnameErr; ?></span>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-control">
                    <input type="text" class="form-control" name="suffix" placeholder="Suffix" value="<?php echo htmlspecialchars($suffix); ?>">
                    <span class="error"><?php echo $suffixErr; ?></span>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-control">
                    <input type="text" class="form-control" name="contact" placeholder="Contact Number" value="<?php echo htmlspecialchars($contact); ?>">
                    <span class="error"><?php echo $contactErr; ?></span>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-control">
                    <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error"><?php echo $emailErr; ?></span>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-control">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <span class="error"><?php echo $passwordErr; ?></span>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-control">
                    <input type="password" class="form-control" name="cpassword" placeholder="Confirm Password">
                    <span class="error"><?php echo $cpasswordErr; ?></span>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

        <hr>
        <div class="login-note">
            <p>Already have an account? <a href="login.php">Login</a>.</p>
        </div>
    </div>
</div>

</body>
</html>
