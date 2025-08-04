<?php
include 'database.php';

if (isset($_POST['customer_id']) && isset($_POST['delivery_status'])) {
    $customer_id = $_POST['customer_id'];
    $delivery_status = $_POST['delivery_status'];

    $stmt = $conn->prepare("SELECT delivery_status FROM orders WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row && $row['delivery_status'] === "Done") {
        echo "already_done"; 
    } else {
        if ($delivery_status === "Done") {
            $stmt = $conn->prepare("UPDATE orders SET delivery_status = ?, completed_at = NOW() WHERE customer_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE orders SET delivery_status = ?, completed_at = NULL WHERE customer_id = ?");
        }

        $stmt->bind_param("si", $delivery_status, $customer_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
