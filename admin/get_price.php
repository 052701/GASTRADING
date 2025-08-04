<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_trading";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['gas_type']) && isset($_POST['capacity'])) {
    $gas_type = $_POST['gas_type'];
    $capacity = $_POST['capacity'];

    $sql = "SELECT price FROM cylinders WHERE gas_type = ? AND capacity = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $gas_type, $capacity);
    $stmt->execute();
    $stmt->bind_result($price);

    if ($stmt->fetch()) {
        echo $price;
    } else {
        echo "0";
    }

    $stmt->close();
}

$conn->close();
?>
