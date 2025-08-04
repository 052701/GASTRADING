<?php
include 'database.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $sql_fetch = "SELECT * FROM expenses WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows > 0) {
        $record = $result_fetch->fetch_assoc();

        // Archive the record to deleted_expenses table
        $sql_insert_deleted = "INSERT INTO deleted_expenses (expense_type, amount, date, notes, file_path, webcam_path)
                               VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_deleted = $conn->prepare($sql_insert_deleted);
        $stmt_insert_deleted->bind_param(
            "sdssss",
            $record['expense_type'],
            $record['amount'],
            $record['date'],
            $record['notes'],
            $record['file_path'],
            $record['webcam_path']
        );

        if ($stmt_insert_deleted->execute()) {
            // Delete the picture file from the folder
            if (!empty($record['file_path']) && file_exists($record['file_path'])) {
                unlink($record['file_path']);
            }
            if (!empty($record['webcam_path']) && file_exists($record['webcam_path'])) {
                unlink($record['webcam_path']);
            }

            // Delete the record from the expenses table
            $sql_delete = "DELETE FROM expenses WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id);
            if ($stmt_delete->execute()) {
                header("Location: expenses.php?status=success");
                exit();
            } else {
                header("Location: expenses.php?status=deleteError");
                exit();
            }
        } else {
            header("Location: expenses.php?status=archiveError");
            exit();
        }
    } else {
        header("Location: expenses.php?status=notFound");
        exit();
    }
} else {
    header("Location: expenses.php?status=invalidRequest");
    exit();
}
?>
