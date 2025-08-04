<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</head>

<body>

    <?php
    include 'database.php';

    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];

        $order_query = "SELECT customer_id FROM orders WHERE id = ?";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $order_data = $result->fetch_assoc();
            $customer_id = $order_data['customer_id'];

            $customer_name = $_POST['customer_name'];
            $customer_location = $_POST['customer_location'];
            $delivery_truck = $_POST['delivery_truck'];
            $delivery_date = $_POST['delivery_date'];
            $gas_type = $_POST['gas_type'];
            $capacity = $_POST['capacity'];
            $quantity = $_POST['quantity'];

            // Update the customer details in the customers table
            $update_customer_query = "UPDATE customers SET 
                                    name = ?,
                                    location = ?,
                                    delivery_truck = ?,
                                    delivery_date = ?
                                  WHERE id = ?";
            $stmt = $conn->prepare($update_customer_query);
            $stmt->bind_param("ssssi", $customer_name, $customer_location, $delivery_truck, $delivery_date, $customer_id);

            if ($stmt->execute()) {
                // Now update the order in the orders table
                $update_order_query = "UPDATE orders SET 
                                    gas_type = ?,
                                    capacity = ?,
                                    quantity = ?
                                  WHERE id = ?";
                $stmt = $conn->prepare($update_order_query);
                $stmt->bind_param("siii", $gas_type, $capacity, $quantity, $order_id);

                if ($stmt->execute()) {
                    echo "<script>
                        swal({
                            title: 'Success!',
                            text: 'Order updated successfully.',
                            type: 'success'
                        }, function() {
                            window.location.href = 'delivery.php';
                        });
                    </script>";
                } else {
                    error_log("Error updating order: " . $stmt->error);
                    echo "<script>
                        swal('Error', 'Error updating order: " . $stmt->error . "', 'error');
                    </script>";
                }
            } else {
                error_log("Error updating customer: " . $stmt->error);
                echo "<script>
                    swal('Error', 'Error updating customer: " . $stmt->error . "', 'error');
                </script>";
            }
        } else {
            echo "<script>
                swal('Error', 'Order not found or invalid order ID.', 'error');
            </script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        die("Order ID is not set.");
    }
    ?>

</body>

</html>
