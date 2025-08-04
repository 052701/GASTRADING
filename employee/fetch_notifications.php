<?php
include 'database.php';

$notifications = [];

// Low stock alert
$queryLowStock = "
  SELECT gas_type, capacity, COUNT(*) AS cylinder_count
  FROM cylinders
  GROUP BY gas_type, capacity
  HAVING COUNT(*) < 5
";

$lowStockResults = mysqli_query($conn, $queryLowStock);

while ($row = mysqli_fetch_assoc($lowStockResults)) {
    $notifications[] = "Low stock alert: " . $row['gas_type'] . 
                       " (" . $row['capacity'] . "L" . 
                       ", Remaining: " . $row['cylinder_count'] . ")";
}

// Fetch all deliveries that are NOT marked as 'Done'
$queryPendingDeliveries = "
  SELECT customers.name AS customer_name, orders.gas_type, orders.capacity, orders.quantity, 
         customers.delivery_date, customers.id AS customer_id, orders.delivery_status
  FROM orders
  JOIN customers ON orders.customer_id = customers.id
  WHERE orders.delivery_status != 'Done'
  GROUP BY customers.id
";

$pendingResults = mysqli_query($conn, $queryPendingDeliveries);

while ($row = mysqli_fetch_assoc($pendingResults)) {
    $notifications[] = "Pending delivery: " . $row['customer_name'] . 
                       " has an order scheduled for " . $row['delivery_date'] . ".";
}

$notificationCount = count($notifications);
$hasNotifications = $notificationCount > 0;

header('Content-Type: application/json');
echo json_encode([
    'hasNotifications' => $hasNotifications,
    'notificationCount' => $notificationCount,
    'notifications' => $hasNotifications ? array_map(function($notification) {
        return $notification . '!';
    }, $notifications) : $notifications
]);
?>
