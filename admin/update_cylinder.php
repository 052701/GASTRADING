<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cylinder_number = $_POST['cylinder_number'];
    $gas_type = $_POST['gas_type'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $query = "UPDATE cylinders SET cylinder_number=?, gas_type=?, capacity=?, price=?, status=? WHERE id=?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssddsi", $cylinder_number, $gas_type, $capacity, $price, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Cylinder updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
