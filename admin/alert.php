<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';
$duplicates = isset($_GET['duplicates']) ? $_GET['duplicates'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>AdminHub</title>
</head>

<body>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($status == 'success'): ?>
                Swal.fire({
                    title: 'Success!',
                    text: 'Cylinder(s) added successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'tanks.php';
                });
            <?php elseif ($status == 'duplicate'): ?>
                Swal.fire({
                    title: 'Duplicate Entry!',
                    text: 'The following cylinder already exist: <?= $duplicates ?>',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'tanks.php';
                });
            <?php elseif ($status == 'limit_exceeded'): ?>
                Swal.fire({
                    title: 'Inventory Limit Exceeded!',
                    text: 'You have reached the maximum allowed cylinders for this gas type.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'tanks.php';
                });
            <?php elseif ($status == 'failure'): ?>
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an issue saving the cylinder information.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                }).then(() => {
                    window.location.href = 'tanks.php';
                });
            <?php endif; ?>
        });
    </script>

</body>

</html>
