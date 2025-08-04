<?php
include 'database.php'; // Ensure database connection

if (isset($_POST['id'])) {
    $id = (int) $_POST['id']; // Ensure ID is an integer

    // Retrieve the order before deletion
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        // Insert into deleted_orders
        $insertQuery = "INSERT INTO deleted_orders (customer_id, name, location, delivery_date, delivery_truck, cylinder_number, gas_type, capacity, quantity, price, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param(
            "isssssssiis",
            $order['customer_id'],
            $order['name'],
            $order['location'],
            $order['delivery_date'],
            $order['delivery_truck'],
            $order['cylinder_number'],
            $order['gas_type'],
            $order['capacity'],
            $order['quantity'],
            $order['price'],
            $order['status']
        );

        if ($insertStmt->execute()) {
            // Attempt deletion
            $deleteQuery = "DELETE FROM orders WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                echo "success";
            } else {
                echo "Failed to delete order: " . $deleteStmt->error;
            }

            $deleteStmt->close();
        } else {
            echo "Failed to insert into deleted_orders: " . $insertStmt->error;
        }

        $insertStmt->close();
    } else {
        echo "Order not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
