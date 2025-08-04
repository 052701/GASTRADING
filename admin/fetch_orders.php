<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_trading";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

if (isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    
    $sql = "SELECT gas_type, capacity, quantity, price, (quantity * price) AS amount 
            FROM orders 
            WHERE customer_id = $customer_id AND status != 'Paid'";
    
    $result = $conn->query($sql);

    $orders = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    echo json_encode(['orders' => $orders]);
} else {
    echo json_encode(['error' => 'Invalid customer ID']);
}

$conn->close();
?>
