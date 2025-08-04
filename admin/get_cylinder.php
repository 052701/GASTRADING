<?php
include 'database.php'; 

if (isset($_GET['cylinder_number'])) {
    $cylinder_number = $_GET['cylinder_number'];

    $query = "SELECT cylinder_number, gas_type, capacity, price, status FROM cylinders WHERE cylinder_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cylinder_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cylinder = $result->fetch_assoc();
        echo json_encode($cylinder);
    } else {
        echo json_encode(["error" => "Cylinder not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>
