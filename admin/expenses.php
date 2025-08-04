<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}


include 'database.php';

define('UPLOAD_DIR', '../uploads/');
$allowed_extensions = ['jpg', 'jpeg', 'png'];
function showAlert($title, $text, $icon)
{
    echo "<script>Swal.fire('$title', '$text', '$icon');</script>";
}

// Add Expense
if (isset($_POST['add_expense'])) {
    $expense_type = trim($_POST['expense_type']);
    $amount = floatval($_POST['amount']);
    $date = trim($_POST['date']);
    $notes = trim($_POST['notes']);
    $file_path = '';
    $webcam_path = $_POST['webcam_image'];

    // Input validation
    if (empty($expense_type) || empty($amount) || empty($date)) {
        showAlert('Error', 'All required fields must be filled.', 'error');
        exit;
    }

    if (!empty($_FILES['file']['name'])) {
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($file_extension, $allowed_extensions)) {
            $target_file = UPLOAD_DIR . uniqid('file_', true) . '.' . $file_extension;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                showAlert('Error', 'File upload failed.', 'error');
                exit;
            }
            $file_path = $target_file;
        } else {
            showAlert('Error', 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.', 'error');
            exit;
        }
    }

    if (!empty($webcam_path)) {
        $image_data = explode(',', $webcam_path)[1] ?? null;
        if ($image_data) {
            $decoded_image = base64_decode($image_data);
            $webcam_file_path = UPLOAD_DIR . uniqid('webcam_', true) . '.png';
            if (!file_put_contents($webcam_file_path, $decoded_image)) {
                showAlert('Error', 'Webcam image save failed.', 'error');
                exit;
            }
            $webcam_path = $webcam_file_path;
        } else {
            showAlert('Error', 'Invalid webcam image data.', 'error');
            exit;
        }
    }

    $check_sql = "SELECT id FROM expenses WHERE expense_type = ? AND amount = ? AND date = ? AND notes = ? LIMIT 1";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param('sdss', $expense_type, $amount, $date, $notes);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        header("Location: expenses.php?add=duplicate");
        exit;
    }

    $sql = "INSERT INTO expenses (expense_type, amount, date, notes, file_path, webcam_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdssss', $expense_type, $amount, $date, $notes, $file_path, $webcam_path);

    if ($stmt->execute()) {
        header("Location: expenses.php?add=success");
        exit;
    } else {
        showAlert('Error', 'Failed to add expense.', 'error');
    }

    $stmt->close();
    $stmt_check->close();
}

if (isset($_POST['update_expense'])) {
    $id = intval($_POST['id']);
    $expense_type = trim($_POST['expense_type']);
    $amount = floatval($_POST['amount']);
    $date = trim($_POST['date']);
    $notes = trim($_POST['notes']);
    $file_path = '';
    $webcam_path = $_POST['webcam_image'];

    if (empty($expense_type) || empty($amount) || empty($date)) {
        showAlert('Error', 'All required fields must be filled.', 'error');
        exit;
    }

    if (!empty($_FILES['file']['name'])) {
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($file_extension, $allowed_extensions)) {
            $target_file = UPLOAD_DIR . uniqid('file_', true) . '.' . $file_extension;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                showAlert('Error', 'File upload failed.', 'error');
                exit;
            }
            $file_path = $target_file;
        }
    }

    if (!empty($webcam_path)) {
        $image_data = explode(',', $webcam_path)[1] ?? null;
        if ($image_data) {
            $decoded_image = base64_decode($image_data);
            $webcam_file_path = UPLOAD_DIR . uniqid('webcam_', true) . '.png';
            if (!file_put_contents($webcam_file_path, $decoded_image)) {
                showAlert('Error', 'Webcam image save failed.', 'error');
                exit;
            }
            $webcam_path = $webcam_file_path;
        }
    }

    $sql = "UPDATE expenses SET expense_type = ?, amount = ?, date = ?, notes = ?, file_path = ?, webcam_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdssssi', $expense_type, $amount, $date, $notes, $file_path, $webcam_path, $id);

    if ($stmt->execute()) {
        header("Location: expenses.php?updateStatus=success");
        exit;
    } else {
        header("Location: expenses.php?updateStatus=error");
        exit;
    }

    $stmt->close();
}

$sql = "SELECT * FROM expenses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="bell.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="expense.css">

    <title>AdminHub</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-cylinder'></i>
            <span class="text">
                <span>SMF</span><br>
                <span>GAS TRADING</span>
            </span>
        </a>

        <ul class="side-menu top">
            <li>
                <a href="dashboard.php">
                    <i class='bx bx-line-chart'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="tanks.php">
                    <i class='bx bxs-cylinder'></i>
                    <span class="text">Tanks</span>
                </a>
            </li>
            <li>
                <a href="delivery.php">
                    <i class='bx bx-plus-medical'></i>
                    <span class="text">Add Delivery</span>
                </a>
            </li>
            <li class="active">
                <a href="expenses.php">
                    <i class='bx bx-wallet-alt'></i>
                    <span class="text">Expenses</span>
                </a>
            </li>
            <li>
                <a href="customer.php">
                    <i class='bx bxs-folder'></i>
                    <span class="text">Records</span>
                </a>
            </li>
            <li>
                <a href="archive.php">
                    <i class='bx bxs-archive-in'></i>
                    <span class="text">Archive</span>
                </a>
            </li>

        </ul>
        <ul class="side-menu">
            <li>
                <a href="#" class="logout" id="logoutBtn">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>

    </section>
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Expenses</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->

            <div class="container">
                <!-- Add Expense Form -->
                <form method="post" action="" id="add-expense-form" enctype="multipart/form-data">
                    <h3>Add New Expense</h3>
                    <div class="form-group">
                        <label for="expense_type">Expense Type:</label>
                        <select name="expense_type" id="expense_type" required class="form-control">
                            <option value="" disabled selected>Select Expense Type</option>
                            <option value="Delivery Fuel">Delivery Fuel</option>
                            <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                            <option value="Meal Allowance">Meal Allowance</option>
                            <option value="Personal Protective Equipment (PPE)">Personal Protective
                                Equipment (PPE)</option>
                            <option value="Miscellaneous Supplies">Miscellaneous Supplies</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount:</label>
                        <input type="number" name="amount" id="amount" step="0.01" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes:</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="file">Upload File:</label>
                        <input type="file" name="file" id="file" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;">
                        <label for="webcam">Capture from Webcam:</label>
                        <video id="webcam" autoplay
                            style="width: 100%; max-width: 400px; height: auto; border: 1px solid #ddd; border-radius: 8px;"></video>
                        <canvas id="canvas"
                            style="display: none; width: 100%; max-width: 400px; height: auto;"></canvas>
                        <button type="button" id="capture" class="btn btn-secondary mt-3">Capture</button>
                        <input type="hidden" name="webcam_image" id="webcam_image">
                    </div>

                    <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
                </form>


                <!-- Edit Expense Modal -->
                <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 1000px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="id" id="edit-id">
                                    <div class="form-group">
                                        <label for="edit-expense_type">Expense Type:</label>
                                        <select name="expense_type" id="edit-expense_type" required
                                            class="form-control">
                                            <option value="" disabled selected>Select Expense Type</option>
                                            <option value="Delivery Fuel">Delivery Fuel</option>
                                            <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                            <option value="Meal Allowance">Meal Allowance</option>
                                            <option value="Personal Protective Equipment (PPE)">Personal Protective
                                                Equipment (PPE)</option>
                                            <option value="Miscellaneous Supplies">Miscellaneous Supplies</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-amount">Amount:</label>
                                        <input type="number" name="amount" id="edit-amount" step="0.01" required
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-date">Date:</label>
                                        <input type="date" name="date" id="edit-date" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-notes">Notes:</label>
                                        <textarea name="notes" id="edit-notes" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-file">Upload File:</label>
                                        <input type="file" name="file" id="edit-file" class="form-control">
                                    </div>
                                    <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;">
                                        <label for="edit-webcam">Capture from Webcam:</label>
                                        <video id="edit-webcam" autoplay
                                            style="width: 100%; max-width: 400px; height: auto; border: 1px solid #ddd; border-radius: 8px;"></video>
                                        <canvas id="edit-canvas"
                                            style="display: none; width: 100%; max-width: 400px; height: auto;"></canvas>
                                        <button type="button" id="edit-capture"
                                            class="btn btn-secondary mt-3">Capture</button>
                                        <input type="hidden" name="webcam_image" id="edit-webcam_image">
                                    </div>

                                    <button type="submit" name="update_expense" class="btn btn-primary">Update
                                        Expense</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table to Display Expenses -->
                <div class="table-responsive">
                    <div class="table-container" style="margin-top: 20px; max-width: 100%; overflow-x: auto;">
                        <table id="expensetable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Expense Type</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Notes</th>
                                    <th scope="col">Uploaded Image</th>
                                    <th scope="col">Webcam Image</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['expense_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                                            <td>
                                                <?php if (!empty($row['file_path'])): ?>
                                                    <img src="<?= htmlspecialchars($row['file_path']); ?>" alt="Uploaded File"
                                                        class="thumbnail">
                                                <?php else: ?>
                                                    No File
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['webcam_path'])): ?>
                                                    <img src="<?= htmlspecialchars($row['webcam_path']); ?>" alt="Webcam Image"
                                                        class="thumbnail">
                                                <?php else: ?>
                                                    No Webcam Image
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-start gap-2">
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editExpenseModal" data-id="<?= $row['id']; ?>"
                                                        data-expense-type="<?= $row['expense_type']; ?>"
                                                        data-amount="<?= $row['amount']; ?>" data-date="<?= $row['date']; ?>"
                                                        data-notes="<?= $row['notes']; ?>">
                                                        <i class='bx bxs-edit-alt bx-sm' style='color:#0a0808'></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                        data-id="<?= $row['id']; ?>" data-toggle="modal"
                                                        data-target="#deleteModal">
                                                        <i class='bx bxs-trash-alt bx-sm' style='color:#0a0808'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">No expenses found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!--END OF MAIN CONTENT-->

            </div>
        </main>
        <!-- MAIN -->
    </section>

    <!-- RIGHT SIDEBAR -->
    <section id="sidebar-right">

        <ul class="side-menu top">
            <header class="notification-header">
                <h2>Notifications</h2>
            </header>
            <section id="notification-container">

            </section>
        </ul>
    </section>


    <script src="../script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tanktable').DataTable();
        });
    </script>

    <script>
        document.getElementById('logoutBtn').addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out of your session.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, logout!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("notification-container");

            fetch("fetch_notifications.php")
                .then((response) => response.json())
                .then((data) => {
                    container.innerHTML = "";

                    if (data.notifications.length === 0) {
                        container.innerHTML = "<div class='notification-item'>No notifications</div>";
                    } else {
                        data.notifications.forEach((notification) => {
                            const item = document.createElement("div");
                            item.className = "notification-item";
                            item.textContent = notification;
                            container.appendChild(item);
                        });
                    }
                })
                .catch((error) => {
                    container.innerHTML = "<div class='notification-item'>Error fetching notifications</div>";
                });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#expensetable').DataTable();
        });
    </script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const recordId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `delete_expense.php?delete=${recordId}`;
                    }
                });
            });
        });

    </script>

    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const captureButton = document.getElementById('capture');
        const webcamImage = document.getElementById('webcam_image');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => {
                console.error("Webcam access denied:", error);
            });

        captureButton.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataURL = canvas.toDataURL('image/png');
            webcamImage.value = dataURL; // Save the captured image as a base64 string
        });
    </script>

    <script>
        async function startWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                document.getElementById('edit-webcam').srcObject = stream;
            } catch (err) {
                console.error("Error accessing the webcam: ", err);
                alert("Unable to access the webcam. Please check your browser permissions.");
            }
        }

        document.getElementById('editExpenseModal').addEventListener('shown.bs.modal', startWebcam);

    </script>
    <script>
        document.getElementById('edit-capture').addEventListener('click', function () {
            const video = document.getElementById('edit-webcam');
            const canvas = document.getElementById('edit-canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL('image/png');
            document.getElementById('edit-webcam_image').value = imageData;
        });
    </script>

    <?php
    include 'database.php';

    if (isset($_GET['add'])) {
        $status = $_GET['add'];

        switch ($status) {
            case 'success':
                echo "<script>Swal.fire('Success', 'Expense added successfully.', 'success');</script>";
                break;
            case 'error':
                echo "<script>Swal.fire('Error', 'Failed to add expense.', 'error');</script>";
                break;
            case 'duplicate':
                echo "<script>Swal.fire('Error', 'Duplicate expense entry detected. Expense not added.', 'error');</script>";
                break;
        }
    }


    if (isset($_GET['updateStatus'])) {
        $updateStatus = $_GET['updateStatus'];

        switch ($updateStatus) {
            case 'success':
                echo "<script>Swal.fire('Success', 'Expense updated successfully.', 'success');</script>";
                break;
            case 'error':
                echo "<script>Swal.fire('Error', 'Failed to update expense.', 'error');</script>";
                break;
        }
    }

    if (isset($_GET['status'])) {
        $status = $_GET['status'];

        switch ($status) {
            case 'success':
                echo "<script>Swal.fire('Success', 'Expense deleted and archived.', 'success');</script>";
                break;
            case 'deleteError':
                echo "<script>Swal.fire('Error', 'Error deleting expense.', 'error');</script>";
                break;
            case 'archiveError':
                echo "<script>Swal.fire('Error', 'Error archiving expense.', 'error');</script>";
                break;
            case 'notFound':
                echo "<script>Swal.fire('Error', 'Expense not found.', 'error');</script>";
                break;
            case 'invalidRequest':
                echo "<script>Swal.fire('Error', 'Invalid request.', 'error');</script>";
                break;
        }
    }
    ?>


    <script>
        document.querySelectorAll('.btn[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-expense_type').value = this.getAttribute('data-expense-type');
                document.getElementById('edit-amount').value = this.getAttribute('data-amount');
                document.getElementById('edit-date').value = this.getAttribute('data-date');
                document.getElementById('edit-notes').value = this.getAttribute('data-notes');
            });
        });

    </script>
</body>

</html>