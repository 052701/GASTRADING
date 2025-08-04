<?php 
if (isset($_POST['submit'])) {
    $cylinder_numbers = $_POST['cylinder_number'];
    $gas_types = $_POST['gas_type'];
    $capacities = $_POST['capacity'];
    $prices = $_POST['price'];

    foreach ($cylinder_numbers as $index => $cylinder_number) {
        $cylinder_number = filter_var($cylinder_number, FILTER_SANITIZE_STRING);
        $gas_type = filter_var($gas_types[$index], FILTER_SANITIZE_STRING);
        $capacity = filter_var($capacities[$index], FILTER_SANITIZE_NUMBER_INT);
        $price = filter_var($prices[$index], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Check if the cylinder already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM cylinders WHERE cylinder_number = ?");
        $stmt->bind_param("s", $cylinder_number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If count is greater than 0, the cylinder already exists
        if ($count > 0) {
            $_SESSION['error'] = "Cylinder number $cylinder_number already exists.";
            header('Location: tanks.php'); // Redirect to the tanks page
            exit();
        }

        // Check if the maximum count for this capacity has been reached
        $stmt = $conn->prepare("SELECT COUNT(*) FROM cylinders WHERE capacity = ?");
        $stmt->bind_param("i", $capacity);
        $stmt->execute();
        $stmt->bind_result($current_count);
        $stmt->fetch();
        $stmt->close();

        // Get the max count for this capacity
        $stmt = $conn->prepare("SELECT max_count FROM inventory_limits WHERE capacity = ?");
        $stmt->bind_param("i", $capacity);
        $stmt->execute();
        $stmt->bind_result($max_count);
        $stmt->fetch();
        $stmt->close();

        // If the current count meets or exceeds the max count, do not insert
        if ($current_count >= $max_count) {
            $_SESSION['error'] = "Cannot add cylinder. Maximum count for capacity $capacity reached.";
            header('Location: tanks.php'); // Redirect to the tanks page
            exit();
        }

        // Prepare the SQL statement to include status and timestamp
        $stmt = $conn->prepare("INSERT INTO cylinders (cylinder_number, gas_type, capacity, price, status) VALUES (?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        // Set default status to 'Available'
        $status = 'Available';
        $stmt->bind_param("ssdss", $cylinder_number, $gas_type, $capacity, $price, $status);

        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }

        $stmt->close();
    }

    $_SESSION['success'] = "Tanks added successfully!";
    header('Location: tanks.php');
    exit();
}

?>