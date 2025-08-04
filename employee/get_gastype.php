<?php
include 'database.php';
$sql = "SELECT DISTINCT gas_type FROM cylinders ORDER BY gas_type ASC";
$result = $conn->query($sql);

$options = '<option value="" disabled selected>Select Type</option>';
while ($row = $result->fetch_assoc()) {
    $options .= '<option value="' . htmlspecialchars($row['gas_type']) . '">'
               . htmlspecialchars($row['gas_type']) . '</option>';
}
echo $options;
$conn->close();
?>
