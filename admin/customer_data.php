<?php

$conn = new mysqli('localhost', 'root', '', 'gas_trading');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $customerId = $_POST['id'];

    $sql = "SELECT SUM(`price`) AS total_price 
FROM `payments` 
WHERE `customer_id` = ?
";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row); 
    } else {
        echo json_encode([]); 
    }

    $stmt->close(); 
}

$conn->close(); 
?>
