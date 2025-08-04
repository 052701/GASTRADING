<?php
session_start();
include('database.php');

$ip_address = $_SERVER['REMOTE_ADDR'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password, account_type FROM accounts WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>"; 

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['account_type'] = $row['account_type'];

            $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
            if ($logStmt) {
                $status = 'success';
                $logStmt->bind_param("ssss", $row['username'], $ip_address, $status, $row['account_type']);
                $logStmt->execute();
                $logStmt->close();
            }

            if ($row['account_type'] === 'admin') {
                $redirectURL = "admin/dashboard.php";
            } elseif ($row['account_type'] === 'employee') {
                $redirectURL = "employee/dashboard.php";
            } else {
                $redirectURL = "dashboard.php";
            }

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome, {$row['username']}!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '$redirectURL';
                });
            </script>";
            exit;
        } else {
            $_SESSION['login_status'] = 'failed';

            $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
            if ($logStmt) {
                $status = 'failed';
                $logStmt->bind_param("ssss", $usernameOrEmail, $ip_address, $status, $row['account_type']);
                $logStmt->execute();
                $logStmt->close();
            }

            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Incorrect password. Please try again.',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        }
    } else {
        $_SESSION['login_status'] = 'failed';

        $logStmt = $conn->prepare("INSERT INTO login_logs (username, ip_address, status, account_type) VALUES (?, ?, ?, ?)");
        if ($logStmt) {
            $status = 'failed';
            $account_type = 'unknown';
            $logStmt->bind_param("ssss", $usernameOrEmail, $ip_address, $status, $account_type);
            $logStmt->execute();
            $logStmt->close();
        }

        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: 'Username or email not found.',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>