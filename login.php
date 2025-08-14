<?php
session_start(); // Start the session

$email = $password = "";
$emailErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Email Validation
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
    }

    // Password Validation
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
    }

    if ($email && $password) {
        include("connections.php");

        // Check if the email exists in the 'users' table
        $check_email = mysqli_query($connections, "SELECT * FROM users WHERE email='$email'");
        $check_email_row = mysqli_num_rows($check_email);

        if ($check_email_row > 0) {
            $row = mysqli_fetch_assoc($check_email);
            $user_id = $row["id"];
            $db_password = $row["password"];
            $db_account_type = $row["account_type"];

            if (password_verify($password, $db_password)) {
                $_SESSION["user_id"] = $user_id;
                $_SESSION["account_type"] = $db_account_type;

                if ($db_account_type == "1") {
                    echo "<script>window.location.href='aadmin/dashboard.php';</script>";
                } elseif ($db_account_type == "2") {
                    echo "<script>window.location.href='staff/dashboard.php';</script>";
                } else {
                    echo "<script>window.location.href='users/dashboard.php';</script>";
                }
            } else {
                $passwordErr = "Invalid password.";
            }
        } else {
            $check_staff_email = mysqli_query($connections, "SELECT * FROM staff WHERE email='$email'");
            $check_staff_row = mysqli_num_rows($check_staff_email);

            if ($check_staff_row > 0) {
                $row = mysqli_fetch_assoc($check_staff_email);
                $staff_id = $row["id"];
                $db_password = $row["password"];
                $db_account_type = $row["account_type"];

                if (password_verify($password, $db_password)) {
                    $_SESSION["staff_id"] = $staff_id;
                    $_SESSION["account_type"] = $db_account_type;

                    if ($db_account_type == "1") {
                        echo "<script>window.location.href='aadmin/dashboard.php';</script>";
                    } elseif ($db_account_type == "2") {
                        echo "<script>window.location.href='staff/dashboard.php';</script>";
                    } else {
                        echo "<script>window.location.href='users/dashboard.php';</script>";
                    }
                } else {
                    $passwordErr = "Invalid password.";
                }
            } else {
                $emailErr = "Email is not registered!";
            }
        }
    }
}
?>

<style>
/* Variables */
:root {
  --base-bgcolor: #354152;
  --base-color: #7e8ba3;
  --base-font-weight: 300;
  --base-font-size: 1rem;
  --base-line-height: 1.5;
  --base-font-family: "Helvetica Neue", sans-serif;
  --input-placeholder-color: #7e8ba3;
  --link-color: #7e8ba3;
  --grid-max-width: 25rem;
  --grid-width: 100%;
}

/* General Styles */
* {
  box-sizing: border-box;
}

html, body {
  height: 100%;
  margin: 0;
}

body {
    background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
  color: var(--base-color);
  font: var(--base-font-weight) var(--base-font-size)/var(--base-line-height) var(--base-font-family);
  display: flex;
  align-items: center;
  justify-content: center;
}

.error {
  color: red;
}

.grid {
  margin: 0 auto;
  max-width: var(--grid-max-width);
  width: var(--grid-width);
}

.form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .form-box {
            width: 80%; /* Adjust to get a medium-sized container */
            max-width: 900px; /* Set a max width for responsiveness */
            min-width: 300px; /* Set a minimum width */
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            box-sizing: border-box;
        }

h2 {
  font-size: 2.75rem;
  font-weight: 100;
  margin: 0 0 1rem;
  text-transform: uppercase;
  text-align: center;
}

input {
  border: 1px solid #242c37;
  border-radius: 999px;
  background-color: transparent;
  padding: 0.5rem 1rem;
  text-align: center;
  width: 100%;
  color: var(--base-color);
  outline: none;
}

input::placeholder {
  color: var(--input-placeholder-color);
}

input[type="email"],

input[type="password"] {
  background-repeat: no-repeat;
  background-size: 2.5rem;
  background-position: 1rem 50%;
}

input[type="email"] {
  background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#242c37"><path d="M256.017 273.436l-205.17-170.029h410.904l-205.734 170.029zm-.034 55.462l-205.983-170.654v250.349h412v-249.94l-206.017 170.245z"/></svg>');
  margin-bottom: 1.5rem;
}

input[type="password"] {
  background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#242c37"><path d="M195.334 223.333h-50v-62.666c0-61.022 49.645-110.667 110.666-110.667 61.022 0 110.667 49.645 110.667 110.667v62.666h-50v-62.666c0-33.452-27.215-60.667-60.667-60.667-33.451 0-60.666 27.215-60.666 60.667v62.666zm208.666 30v208.667h-296v-208.667h296zm-121 87.667c0-14.912-12.088-27-27-27s-27 12.088-27 27c0 7.811 3.317 14.844 8.619 19.773 4.385 4.075 6.881 9.8 6.881 15.785v22.942h23v-22.941c0-5.989 2.494-11.708 6.881-15.785 5.302-4.93 8.619-11.963 8.619-19.774z"/></svg>');
}

input[type="submit"] {
  background-image: linear-gradient(160deg, #8ceabb 0%, #378f7b 100%);
  color: #fff;
  margin-top: 1.5rem;
}

a {
  color: var(--link-color);
  text-decoration: none;
}

.register {
  box-shadow: 0 0 250px #000;
  text-align: center;
  padding: 2rem;
}
</style>

<div class="form-container">
  <div class="form-box register">
    <h2>Login</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <!-- Email Input -->
      <div class="form__field">
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
        <span class="error"><?php echo $emailErr; ?></span>
      </div>

      <!-- Password Input -->
      <div class="form__field">
        <input type="password" name="password" placeholder="Password">
        <span class="error"><?php echo $passwordErr; ?></span>
      </div>

      <!-- Submit Button -->
      <div class="form__field">
        <input type="submit" value="Login">
      </div>

      <!-- Forgot Password Link -->
      <div>
        <a href="forgot_password.php">Forgot Password?</a>
      </div>

    </form>
  </div>
</div>
