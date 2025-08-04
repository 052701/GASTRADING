<?php
include 'database.php';

if (isset($_POST['customer_id']) && isset($_POST['delivery_status'])) {
    $customer_id = $_POST['customer_id'];
    $delivery_status = $_POST['delivery_status'];

    // Check the current delivery status
    $stmt = $conn->prepare("SELECT delivery_status FROM orders WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close(); // Close the first statement

    // Prevent changing status back from "Done" to anything else
    if ($row && $row['delivery_status'] === "Done" && $delivery_status !== "Done") {
        echo "already_done"; 
    } else {
        // Update the status only if it is not already marked as "Done"
        $stmt = $conn->prepare("UPDATE orders SET delivery_status = ?, completed_at = " . 
            ($delivery_status === "Done" ? "NOW()" : "NULL") . " WHERE customer_id = ?");
        $stmt->bind_param("si", $delivery_status, $customer_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
