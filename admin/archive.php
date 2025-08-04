<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
include 'database.php';

$deleted_cylinders = [];
$sql = "SELECT id, cylinder_number, gas_type, capacity, price, deleted_at FROM deleted_cylinders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deleted_cylinders[] = $row;
    }
} else {
    $deleted_cylinders = [];
}

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
            <li>
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
            <li class="active">
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
            <a href="#" class="nav-link">Archive</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->

            <div class="container mt-5">
                <h2>Deleted Cylinders</h2>
                <?php if (empty($deleted_cylinders)): ?>
                    <div class="alert alert-info">No deleted cylinders found.</div>
                <?php else: ?>
                    <table id="cylinderTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cylinder Number</th>
                                <th>Gas Type</th>
                                <th>Capacity</th>
                                <th>Price</th>
                                <th>Deleted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deleted_cylinders as $cylinder): ?>
                                <tr>
                                    <td><?php echo $cylinder['id']; ?></td>
                                    <td><?php echo $cylinder['cylinder_number']; ?></td>
                                    <td><?php echo $cylinder['gas_type']; ?></td>
                                    <td><?php echo $cylinder['capacity']; ?></td>
                                    <td><?php echo $cylinder['price']; ?></td>
                                    <td><?php echo $cylinder['deleted_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Table for deleted expenses -->
            <div class="container mt-5">
                <?php
                // Ensure a valid connection exists
                if (!$conn) {
                    die("<div class='alert alert-danger'>Database connection error.</div>");
                }

                // Fetch deleted expenses
                $sql = "SELECT * FROM deleted_expenses";
                $result = $conn->query($sql);

                $deleted_expenses = [];
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $deleted_expenses[] = $row;
                    }
                }
                ?>

                <h2>Deleted Expenses</h2>

                <?php if (empty($deleted_expenses)): ?>
                    <div class="alert alert-info">No deleted expenses found.</div>
                <?php else: ?>
                    <table id="expenseTable" class="table table-bordered table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Expense Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th>Uploaded Image</th>
                                <th>Captured Image</th>
                                <th>Deleted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deleted_expenses as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense['id']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['expense_type']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['date']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['notes']); ?></td>
                                    <td>
                                        <?php if (!empty($expense['file_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($expense['file_path']); ?>" alt="Uploaded Image"
                                                width="50">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($expense['webcam_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($expense['webcam_path']); ?>" alt="Captured Image"
                                                width="50">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($expense['deleted_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>


            <div class="container mt-5">
                <?php
                include 'database.php';

                $sql = "
                SELECT 
                    d.id, 
                    d.customer_id AS order_id, 
                    c.name AS customer_name, 
                    d.location, 
                    d.delivery_date, 
                    d.delivery_truck, 
                    d.cylinder_number, 
                    d.gas_type, 
                    d.capacity, 
                    d.quantity, 
                    d.price, 
                    (d.price * d.quantity) AS total_amount, 
                    d.status, 
                    d.deleted_at
                FROM deleted_orders d
                LEFT JOIN customers c ON d.customer_id = c.id
            ";


                $result = $conn->query($sql);

                $deleted_orders = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $deleted_orders[] = $row;
                    }
                }
                ?>

                <h2>Deleted/Canceled Orders</h2>
                <?php if (empty($deleted_orders)): ?>
                    <div class="alert alert-info">No deleted orders found.</div>
                <?php else: ?>
                    <table id="deletedOrderTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Gas Type</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Delivery Date</th>
                                <th>Deletion Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deleted_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo $order['customer_name']; ?></td>
                                    <td><?php echo $order['gas_type']; ?></td>
                                    <td><?php echo number_format($order['price'], 2); ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo $order['delivery_date']; ?></td>
                                    <td><?php echo $order['deleted_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
            $('#cylinderTable').DataTable();

            $('#expenseTable').DataTable();

            $('#deletedOrderTable').DataTable();
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
</body>

</html>