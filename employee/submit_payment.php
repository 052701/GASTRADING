<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_trading";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['customer_id'], $data['payment_amount'], $data['total_amount'])) {
    $customer_id = intval($data['customer_id']);
    $payment_amount = floatval($data['payment_amount']);
    $total_amount = floatval($data['total_amount']);
    $change = $payment_amount - $total_amount;

    // Insert payment record
    $payment_sql = "INSERT INTO payments (customer_id, amount_paid, total_amount, change_given, payment_date) 
                    VALUES ($customer_id, $payment_amount, $total_amount, $change, NOW())";

    if ($conn->query($payment_sql) === TRUE) {
        // Fetch ordered cylinders
        $cylinders_sql = "SELECT cylinder_number FROM orders WHERE customer_id = $customer_id AND status != 'Paid'";
        $result = $conn->query($cylinders_sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cylinder_number = $row['cylinder_number'];

                // Delete the cylinder from the cylinders table
                $delete_cylinder_sql = "DELETE FROM cylinders WHERE cylinder_number = '$cylinder_number'";
                $conn->query($delete_cylinder_sql);
            }
        }

        // Update the order status to 'Paid'
        $update_sql = "UPDATE orders SET status = 'Paid' WHERE customer_id = $customer_id AND status != 'Paid'";
        if ($conn->query($update_sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update order status: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not save payment: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
}

$conn->close();
?>
