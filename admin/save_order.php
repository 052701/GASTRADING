<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Submission</title>
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_trading";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if (isset($_POST['submit'])) {
    if (isset($_POST['customer_name'], $_POST['customer_location'], $_POST['delivery_date'], $_POST['delivery_truck'])) {
        $customer_name = $_POST['customer_name'];
        $customer_location = $_POST['customer_location'];
        $delivery_date = $_POST['delivery_date'];
        $delivery_truck = $_POST['delivery_truck'];

        // Start transaction
        $conn->begin_transaction();

        if (isset($_POST['gas_type']) && !empty($_POST['gas_type'])) {
            $gas_types = $_POST['gas_type'];
            $capacities = $_POST['capacity'];
            $quantities = $_POST['quantity'];
            $prices = $_POST['price'];
            $status = 'Unpaid'; // Default payment status

            if (count($gas_types) === count($capacities) && count($gas_types) === count($quantities) && count($gas_types) === count($prices)) {
                $allOrdersSuccessful = true;

                // **Check if all gas types and capacities are available before inserting**
                for ($i = 0; $i < count($gas_types); $i++) {
                    $gas_type = $gas_types[$i];
                    $capacity = $capacities[$i];
                    $quantity = (int)$quantities[$i];

                    // **Check if the customer already has this order**
                    $check_existing_order = "SELECT id FROM orders 
                                             WHERE gas_type = '$gas_type' 
                                             AND capacity = '$capacity' 
                                             AND customer_id IN (SELECT id FROM customers WHERE name = '$customer_name' AND location = '$customer_location')";
                    $existing_order_result = $conn->query($check_existing_order);

                    if ($existing_order_result->num_rows > 0) {
                        // **Show SweetAlert and stop execution if order already exists**
                        echo "<script>
                            Swal.fire({
                                icon: 'warning',
                                title: 'Duplicate Order',
                                text: 'You already have an existing order for $gas_type ($capacity).',
                            }).then(() => {
                                window.location.href = 'delivery.php';
                            });
                        </script>";
                        $conn->rollback(); // Cancel transaction
                        exit();
                    }

                    // **Check available cylinders**
                    $check_cylinder_sql = "SELECT id FROM cylinders 
                                           WHERE gas_type = '$gas_type' 
                                           AND capacity = '$capacity' 
                                           AND status = 'Available' 
                                           LIMIT $quantity";
                    $cylinder_result = $conn->query($check_cylinder_sql);

                    if (!$cylinder_result || $cylinder_result->num_rows < $quantity) {
                        // **Show SweetAlert and stop execution if insufficient stock**
                        echo "<script>
                            Swal.fire({
                                icon: 'warning',
                                title: 'Insufficient Stock',
                                text: 'Not enough cylinders available for $gas_type ($capacity).',
                            }).then(() => {
                                window.location.href = 'delivery.php';
                            });
                        </script>";
                        $conn->rollback(); // Cancel transaction
                        exit();
                    }
                }

                // **Insert customer only if stock is available and no duplicate orders**
                $customer_sql = "INSERT INTO customers (name, location, delivery_date, delivery_truck) 
                                 VALUES ('$customer_name', '$customer_location', '$delivery_date', '$delivery_truck')";

                if ($conn->query($customer_sql) === TRUE) {
                    $customer_id = $conn->insert_id; // Get the inserted customer ID

                    // **Proceed with inserting orders**
                    for ($i = 0; $i < count($gas_types); $i++) {
                        $gas_type = $gas_types[$i];
                        $capacity = $capacities[$i];
                        $quantity = (int)$quantities[$i];
                        $price = $prices[$i];

                        // Fetch cylinders again to insert orders
                        $cylinder_result = $conn->query("SELECT id, cylinder_number FROM cylinders 
                                                          WHERE gas_type = '$gas_type' 
                                                          AND capacity = '$capacity' 
                                                          AND status = 'Available' 
                                                          LIMIT $quantity");

                        while ($cylinder_row = $cylinder_result->fetch_assoc()) {
                            $cylinder_number = $cylinder_row['cylinder_number'];

                            // Insert order
                            $order_sql = "INSERT INTO orders (customer_id, gas_type, capacity, quantity, price, status, cylinder_number) 
                                          VALUES ('$customer_id', '$gas_type', '$capacity', 1, '$price', '$status', '$cylinder_number')";

                            if ($conn->query($order_sql) === TRUE) {
                                // Insert into completed cylinders
                                $completed_cylinder_sql = "INSERT INTO completed_cylinders (cylinder_number, gas_type, capacity) 
                                                            VALUES ('$cylinder_number', '$gas_type', '$capacity')";
                                if (!$conn->query($completed_cylinder_sql)) {
                                    $allOrdersSuccessful = false;
                                }
                            } else {
                                $allOrdersSuccessful = false;
                            }

                            if (--$quantity <= 0) break;
                        }
                    }

                    // **Final check: Commit if all orders are successful, otherwise rollback**
                    if ($allOrdersSuccessful) {
                        $conn->commit(); // Save all changes
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Order Successful',
                                text: 'All items have been ordered successfully!',
                            }).then(() => {
                                window.location.href = 'delivery.php';
                            });
                        </script>";
                    } else {
                        $conn->rollback(); // Undo everything if there's an error
                        echo "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong with the order. No data was saved.',
                            }).then(() => {
                                window.location.href = 'delivery.php';
                            });
                        </script>";
                        exit();
                    }
                } else {
                    $conn->rollback(); // Undo customer insert if it failed
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Customer information could not be saved.',
                        }).then(() => {
                            window.location.href = 'delivery.php';
                        });
                    </script>";
                    exit();
                }
            }
        }
    }
}

$conn->close();
?>

</body>
</html>
