<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
include 'database.php';
$query = "SELECT cylinder_number, gas_type, capacity, price, status FROM cylinders";
$result = $conn->query($query);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cylinder_numbers = $_POST['cylinder_number'];
    $gas_types = $_POST['gas_type'];
    $capacities = $_POST['capacity'];
    $prices = $_POST['price'];

    $duplicates = [];
    $inserted = 0;

    for ($i = 0; $i < count($cylinder_numbers); $i++) {
        $cylinder_number = $cylinder_numbers[$i];
        $gas_type = $gas_types[$i];
        $capacity = $capacities[$i];
        $price = $prices[$i];

        $check_query = "SELECT COUNT(*) as count FROM cylinders WHERE cylinder_number = '$cylinder_number'";
        $result = $conn->query($check_query);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            $duplicates[] = $cylinder_number;
        } else {
            $insert_query = "INSERT INTO cylinders (cylinder_number, gas_type, capacity, price) 
                             VALUES ('$cylinder_number', '$gas_type', '$capacity', '$price')";

            if ($conn->query($insert_query) === TRUE) {
                $inserted++;
            }
        }
    }

    $conn->close();

    if (!empty($duplicates)) {
        $duplicate_numbers = implode(', ', $duplicates);
        echo "<script>window.location.href='alert.php?status=duplicate&duplicates=$duplicate_numbers';</script>";
    } elseif ($inserted > 0) {
        echo "<script>window.location.href='alert.php?status=success';</script>";
    } else {
        echo "<script>window.location.href='alert.php?status=failure';</script>";
    }
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

    <title>Tanks</title>
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
            <li class="active">
                <a href="tanks.php">
                    <i class='bx bxs-cylinder'></i>
                    <span class="text">Tanks</span>
                </a>
            </li>
            <li>
                <a href="expenses.php">
                    <i class='bx bx-wallet-alt'></i>
                    <span class="text">Expenses</span>
                </a>
            </li>

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
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Tank Cylinders</a>
        </nav>
        <main>

            <!--MAIN CONTENT HERE-->

            <div class="container">

                <div class="table-responsive">
                    <table id="tanktable" class="table table">
                        <thead>
                            <tr>
                                <th scope="col">Cylinder Number</th>
                                <th scope="col">Gas Type</th>
                                <th scope="col">Capacity (Liters)</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['cylinder_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gas_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>

                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No cylinders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>


                    </table>

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