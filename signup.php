<?php
session_start();
include('database.php'); // Include your database connection

$admin_password = "admin123"; // Replace with your actual admin password

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $accountType = $_POST['account_type'];
    $adminPassInput = isset($_POST['admin_password']) ? $_POST['admin_password'] : null;

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($accountType)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($accountType == "employee" && $adminPassInput !== $admin_password) {
        $error = "Invalid Admin Password.";
    } else {
        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email is already taken.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert the user into the database
            $stmt = $conn->prepare("INSERT INTO accounts (username, email, password, account_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $accountType);

            if ($stmt->execute()) {
                $success = "Signup successful. You can now <a href='index.php'>log in</a>.";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
    <script>
        function toggleAdminPassword() {
            var accountType = document.getElementById("account_type").value;
            var adminPassField = document.getElementById("admin_password_field");

            if (accountType === "employee") {
                adminPassField.style.display = "block";
            } else {
                adminPassField.style.display = "none";
            }
        }
    </script>
</head>
<style>body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.form-container {
    background: white;
    padding: 20px;
    width: 350px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

label {
    font-size: 14px;
    color: #555;
}

input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 5px;
}

.btn {
    width: 100%;
    padding: 10px;
    background: #007bff;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

.btn:hover {
    background: #0056b3;
}

.alert {
    padding: 10px;
    color: white;
    text-align: center;
    margin-bottom: 10px;
    border-radius: 5px;
}

.alert-danger {
    background: #dc3545;
}

.alert-success {
    background: #28a745;
}

.login-link {
    margin-top: 15px;
    font-size: 14px;
}
.btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 5px;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }


        .btn-secondary:hover {
            background-color: #545b62;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 60px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
</style>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="account_type">Account Type:</label>
            <select id="account_type" name="account_type" onchange="toggleAdminPassword()" required>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
            </select>

            <!-- Admin Password Field (only for employee registration) -->
            <div id="admin_password_field" style="display: none;">
                <label for="admin_password">Admin Password:</label>
                <input type="password" id="admin_password" name="admin_password">
            </div>

            <button type="submit" class="btn btn-primary">Sign Up</button>
            <a href="index.php"F>Login</a> 
        </form>
    </div>
</body>

</html>
