<?php
if (isset($_POST['expense_id'])) {
    require 'database.php'; // Include your DB connection

    $expense_id = $_POST['expense_id'];
    $sql = "SELECT * FROM expenses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $expense = $result->fetch_assoc();
        echo json_encode($expense);
    } else {
        echo json_encode(['error' => 'Expense not found.']);
    }
}
?>