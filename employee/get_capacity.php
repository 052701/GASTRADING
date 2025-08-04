<?php
if (isset($_POST['gas_type'])) {
    $gasType = $_POST['gas_type'];

    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "gas_trading";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT DISTINCT capacity FROM cylinders WHERE gas_type = ?");
    $stmt->bind_param("s", $gasType);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = '<option value="" disabled selected>Select Capacity</option>';
    while ($row = $result->fetch_assoc()) {
        $capacity = $row['capacity'];
        $options .= '<option value="' . htmlspecialchars($capacity) . '">' . htmlspecialchars($capacity) . ' Liters</option>';
    }

    echo $options;

    $stmt->close();
    $conn->close();
}
?>
