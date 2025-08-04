<?php
session_start();
include('database.php'); // Include your database connection

// Capture the user's IP address
$ip_address = $_SERVER['REMOTE_ADDR']; 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST['username']; // Accept username or email
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to find the user
    $stmt = $conn->prepare("SELECT username, email, password, account_type FROM accounts WHERE (username = ? OR email = ?)");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verify the credentials
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Check password securely using password_verify
        if (password_verify($password, $row['password'])) {
            // Successful login
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['account_type'] = $row['account_type'];

            // Log the successful login
            $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
            $status = 'success';
            $account_type = $row['account_type'];
            $logStmt->bind_param("ssss", $row['username'], $ip_address, $status, $account_type);
            $logStmt->execute();
            $logStmt->close();

            // JavaScript alert for login success
            echo "<script>alert('Login successful. Welcome, {$row['username']}!');</script>";

            // Redirect based on account type
            if ($row['account_type'] === 'admin') {
                echo "<script>window.location.href = 'admin/dashboard.php';</script>";
            } elseif ($row['account_type'] === 'employee') {
                echo "<script>window.location.href = 'employee/dashboard.php';</script>";
            } else {
                echo "<script>window.location.href = 'dashboard.php';</script>";
            }
            exit;
        } else {
            // Log the failed login attempt
            $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
            $status = 'failed';
            $account_type = $row['account_type'];
            $logStmt->bind_param("ssss", $usernameOrEmail, $ip_address, $status, $account_type);
            $logStmt->execute();
            $logStmt->close();

            // JavaScript alert for invalid password
            echo "<script>alert('Invalid password.'); window.location.href = 'index.php';</script>";
            exit;
        }
    } else {
        // Log the failed login attempt with 'unknown' account type if not found
        $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
        $status = 'failed';
        $account_type = 'unknown';
        $logStmt->bind_param("ssss", $usernameOrEmail, $ip_address, $status, $account_type);
        $logStmt->execute();
        $logStmt->close();

        // JavaScript alert for invalid username/email
        echo "<script>alert('Invalid username or email.'); window.location.href = 'index.php';</script>";
        exit;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body, html {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
        }

        .container {
            display: flex;
            width: 700px;
            max-width: 900px;
            height: 60%;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .left-side {
            background-image: url('back.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .right-side {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-side h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4A90E2;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #357ABD;
        }

        .form-footer {
            text-align: center;
            margin-top: 10px;
        }

        .form-footer a {
            color: #4A90E2;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-side">
            <!-- Image background will cover this area -->
        </div>
        <div class="right-side">
            <h2>Login</h2>
         
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
                <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
                <div class="form-footer">
                    <p>Don't have an account? <a href="signup.php">Register here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
