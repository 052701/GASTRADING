<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_trading";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$totalOccupants = 0; //3
$total_incidents = 0; //1
$new_reports = 0; //1
$newReport = 0; //b2
$totalReportThisWeek = 0; //b1
$percentageIncidents = 0; //1
$percentageOccupantIncrease = 0; //3
$percentageStallIncrease = 0; //2

// Total occupants
$sql = "SELECT COUNT(*) AS Unpaid FROM orders WHERE status = 'Unpaid'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalOccupants = $row['Unpaid'];
}

// New reports for the last 7 days
$sql = "SELECT COUNT(*) AS new_orders FROM orders WHERE ordered_at >= NOW() - INTERVAL 7 DAY";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newReport = $row['new_orders'];
}

if ($totalOccupants > 0) {
    $percentageOccupantIncrease = ($newReport / $totalOccupants) * 100;
}

// Total buildings
$sql = "SELECT COUNT(*) AS total_orders FROM orders";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_incidents = $row['total_orders'];
}

// New buildings in the last 7 days
$sql = "SELECT COUNT(*) AS new_customers FROM customers WHERE created_at >= NOW() - INTERVAL 7 DAY";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_reports = $row['new_customers'];
}

if ($total_incidents > 0) {
    $percentageIncidents = ($new_reports / $total_incidents) * 100;
}

// Total stalls
$sql = "SELECT COUNT(*) AS Paid FROM orders WHERE status = 'Paid'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_stall = $row['Paid'];
}

// New stalls in the last 7 days
$sql = "SELECT COUNT(*) AS new_done FROM orders WHERE ordered_at >= NOW() - INTERVAL 7 DAY";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newStalls = $row['new_done'];
}

if ($total_stall > 0) {
    $percentageStallIncrease = ($newStalls / $total_stall) * 100;
}

// Total stalls this week
$sql = "SELECT COUNT(*) AS total_customers FROM customers WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalReportThisWeek = $row['total_customers'];
}

$sql = "SELECT SUM(price) as totalIncome FROM orders WHERE status = 'Paid'";

$result = $conn->query($sql);

$totalIncome = 0; // Initialize the total income variable

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalIncome = $row['totalIncome']; // Fetch the total income
    }
}
$conn->close();
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

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="bell.css">
    <link rel="stylesheet" href="../style.css">

    <title>AdminHub</title>
</head>
<style>
    .bg-gradient-dark {
        background: linear-gradient(to left, #3d2a40, #B57EBF);
    }

    .border-radius-lg {
        border-radius: 15px;
    }

    .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .pe-1 {
        padding-right: 0.25rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(to left, #3d2a40, #B57EBF);
    }

    .icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 50px;
        height: 50px;
    }

    .border-radius-md {
        border-radius: 10px;
    }

    .text-lg {
        font-size: 1.5rem;
        color: whitesmoke;
    }
</style>

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
            <li class="active">
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
            <a href="#" class="nav-link">Dashboard</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Total Orders</p>
                                            <h5 class="font-weight-bolder mb-0">
                                                <?php echo $total_incidents; ?>
                                                <span class="text-success text-sm font-weight-bolder">

                                                </span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div
                                            class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="bi bi-people text-lg" aria-hidden="true"></i>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Paid Orders</p>
                                            <h5 class="font-weight-bolder mb-0">
                                                <?php echo number_format($total_stall); ?>

                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div
                                            class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="bi bi-cart-check text-lg opacity-10" aria-hidden="true"></i>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Unpaid Orders</p>
                                            <h5 class="font-weight-bolder mb-0">
                                                <?php echo number_format($totalOccupants); ?>

                                            </h5>
                                        </div>

                                    </div>
                                    <div class="col-4 text-end">
                                        <div
                                            class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="bi bi-cart text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                Income</p>
                                            <h5 class="font-weight-bolder mb-0">
                                                ₱<?php echo number_format($totalIncome); ?>

                                            </h5>
                                        </div>


                                    </div>
                                    <div class="col-4 text-end">
                                        <div
                                            class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="bi bi-currency-peso text-lg opacity-10" aria-hidden="true"> ₱</i>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col-lg-5 mb-lg-0 mb-4">
                        <div class="card z-index-2">
                            <div class="card-body p-3">Order Reports
                                <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
                                    <div class="chart">
                                        <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                                    </div>
                                </div>
                                <br><br><br>
                                <div class="container border-radius-lg">
                                    <div class="row">
                                        <div class="col-3 py-3 ps-0">
                                            <div class="d-flex mb-2">
                                                <div
                                                    class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                                    <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <title>customer</title>
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <g transform="translate(-1870.000000, -591.000000)"
                                                                fill="#FFFFFF" fill-rule="nonzero">
                                                                <g transform="translate(1716.000000, 291.000000)">
                                                                    <g transform="translate(154.000000, 300.000000)">
                                                                        <path class="color-background"
                                                                            d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                                                            opacity="0.603585379">
                                                                        </path>
                                                                        <path class="color-background"
                                                                            d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z">
                                                                        </path>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <p class="text-xs mt-1 mb-0 font-weight-bold">Customer
                                                    This Week</p>
                                            </div>

                                            <h4 class="stall-count"><?php echo $totalReportThisWeek; ?>
                                            </h4>


                                            <div class="progress w-75">
                                                <div class="progress-bar bg-dark" role="progressbar"
                                                    aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0"
                                                    aria-valuemax="<?php echo $maxStalls; ?>">
                                                </div>
                                            </div>

                                        </div>


                                        <div class="col-3 py-3 ps-0">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card z-index-2">
                            <div class="card-header pb-0">
                                <h6>Orders Overview</h6>
                                <p class="text-sm">
                                    <i class="fa fa-arrow-up text-success"></i>
                                </p>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                    <canvas id="chart-line" class="chart-canvas" height="380"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "gas_trading";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Updated SQL query to join with the cylinders table
                $sql = "SELECT `gas_type`, `capacity`, `price`, `status`, COUNT(*) as quantity
            FROM `cylinders` 
            GROUP BY `gas_type`, `capacity`, created_at LIMIT 5";
                $result = $conn->query($sql);
                ?>


                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Available Cylinders</h6>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive">
                            <table class="table mb-4" id="tanks">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Gas Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Capacity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Qty</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Price</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            // Ensure 'ordered_at' exists in your orders table
                                            echo "<td class='text-sm'>" . htmlspecialchars($row['gas_type']) . "</td>";
                                            echo "<td class='text-sm'>" . htmlspecialchars($row['capacity']) . "</td>";
                                            echo "<td class='text-sm'>" . htmlspecialchars($row['quantity']) . "</td>";
                                            echo "<td class='text-sm'>" . htmlspecialchars($row['price']) . "</td>";
                                            echo "<td class='text-sm'>" . htmlspecialchars($row['status']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No recent orders available</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row my-4">
                    <?php

                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "gas_trading";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT orders.*, customers.name, customers.location 
        FROM orders orders 
        JOIN customers customers ON orders.customer_id = customers.id 
        ORDER BY orders.ordered_at DESC";
                    $result = $conn->query($sql);
                    ?>

                    <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6>Recent Orders</h6>
                            </div>
                            <div class="card-body px-0 pb-2">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0" id="stallTable">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Customer Name</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Location</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Order Date</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Gas Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td class='text-sm'>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td class='text-sm'>" . htmlspecialchars($row['location']) . "</td>";
                                                    echo "<td class='text-sm'>" . htmlspecialchars($row['ordered_at']) . "</td>"; // Replace 'order_date' with the actual column name for date in the orders table
                                                    echo "<td class='text-sm'>" . htmlspecialchars($row['gas_type']) . "</td>"; // Adjust this if you need a different field
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>No recent orders available</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <!-- DataTables JS -->
                    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

                    <script>
                        $(document).ready(function () {
                            $('#stallTable').DataTable({
                                responsive: true,
                                paging: true,
                                searching: true,
                                ordering: true
                            });
                        });
                    </script>
                    <script>
                        $(document).ready(function () {
                            $('#tanks').DataTable({
                                responsive: true,
                                paging: true,
                                searching: true,
                                ordering: true
                            });
                        });
                    </script>
                    <script>
                        $(document).ready(function () {
                            $('#logsTable').DataTable({
                                responsive: true,
                                paging: true,
                                searching: true,
                                ordering: true
                            });
                        });
                    </script>



                    <?php

                    $conn->close();
                    ?>


                    <?php

                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "gas_trading";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT username, account_type, login_time FROM login_logs ORDER BY login_time DESC";
                    $result = $conn->query($sql);
                    ?>

                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6>Recent Logins</h6>
                            </div>
                            <div class="card-body px-0 pb-2">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0" id="logsTable">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Account Type</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    username</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Date and Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td class='text-sm'> " . htmlspecialchars($row['account_type']) . "</td>";
                                                    echo "<td class='text-sm'>" . htmlspecialchars($row['username']) . "</td>";
                                                    echo "<td class='text-sm'>" . htmlspecialchars(date("d M h:i A", strtotime($row['login_time']))) . "</td>";

                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center'>No stalls available</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
        const incidentCounts = <?php echo json_encode($incident_counts); ?>;
        const dropdown = document.getElementById('incidentTypeDropdown');
        const incidentCountDisplay = document.getElementById('incidentCount');

        dropdown.addEventListener('change', function () {
            const selectedType = this.value;
            if (selectedType) {
                incidentCountDisplay.textContent = incidentCounts[selectedType] || 0;
            } else {

                incidentCountDisplay.textContent = <?php echo $total_incidents; ?>;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
        </script>
    <script src="script.js"></script>



    <?php
    $connection = mysqli_connect("localhost", "root", "", "gas_trading");

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $incidentTypes = [];
    $monthlyData = [];
    $colors = ['#000', 'rgba(75, 192, 192, 0.5)', '#FF6384', '#36A2EB', '#FFCE56'];

    $query = "
    SELECT 
        MONTH(ordered_at) AS month, 
        gas_type, 
        COUNT(*) AS count 
    FROM 
        orders 
    GROUP BY 
        month, gas_type 
    ORDER BY 
        month, gas_type
";

    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $type = $row['gas_type'];
        $month = (int) $row['month'];
        $count = (int) $row['count'];

        if (!in_array($type, $incidentTypes)) {
            $incidentTypes[] = $type;
        }

        if (!isset($monthlyData[$type])) {
            $monthlyData[$type] = array_fill(0, 12, 0);
        }

        $monthlyData[$type][$month - 1] += $count;
    }

    echo '<script>
        var incidentTypes = ' . json_encode($incidentTypes) . ';
        var monthlyData = ' . json_encode($monthlyData) . ';
        var colors = ' . json_encode($colors) . ';
      </script>';

    mysqli_close($connection);
    ?>

    <script>
        var ctx = document.getElementById("chart-bars").getContext("2d");

        var datasets = incidentTypes.map((type, index) => ({
            label: type,
            tension: 0.4,
            borderWidth: 0,
            borderRadius: 4,
            borderSkipped: false,
            backgroundColor: colors[index % colors.length],
            data: monthlyData[type],
            maxBarThickness: 6
        }));

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: "#9dc1d8"
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: Math.max(...Object.values(monthlyData).flat()),
                            beginAtZero: true,
                            padding: 15,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                            color: "#fff"
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            display: true,
                            color: "#fff"
                        },
                    },
                },
            },
        });
    </script>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gas_trading";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get the demand for each gas_type and capacity
    $sql = "
    SELECT gas_type, capacity, COUNT(*) as total_orders
    FROM orders
    GROUP BY gas_type, capacity
    ORDER BY total_orders DESC
";

    $result = $conn->query($sql);

    $demandData = [];
    while ($row = $result->fetch_assoc()) {
        // Store data in a structured format
        $demandData[] = [
            'gas_type' => $row['gas_type'],
            'capacity' => $row['capacity'],
            'total_orders' => (int) $row['total_orders']
        ];
    }

    $conn->close();
    ?>

    <script>
        // Prepare data for Chart.js
        var demandData = <?php echo json_encode($demandData); ?>;

        var gasTypes = [];
        var capacities = [];
        var totalOrders = [];

        demandData.forEach(function (data) {
            if (!gasTypes.includes(data.gas_type)) {
                gasTypes.push(data.gas_type);
            }
            if (!capacities.includes(data.capacity)) {
                capacities.push(data.capacity);
            }
            totalOrders.push(data.total_orders);
        });

        // Create a chart
        var ctx = document.getElementById("chart-bar").getContext("2d");
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: gasTypes,
                datasets: capacities.map((capacity, index) => ({
                    label: 'Capacity ' + capacity,
                    data: totalOrders.filter((_, i) => demandData[i].capacity === capacity),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                }))
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gas_trading";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get the demand for each gas_type and capacity
    $sql = "
    SELECT gas_type, capacity, COUNT(*) as total_orders
    FROM orders
    GROUP BY gas_type, capacity
    ORDER BY total_orders DESC
";

    $result = $conn->query($sql);

    $demandData = [];
    while ($row = $result->fetch_assoc()) {
        // Store data in a structured format
        $demandData[] = [
            'gas_type' => $row['gas_type'],
            'capacity' => $row['capacity'],
            'total_orders' => (int) $row['total_orders']
        ];
    }

    $conn->close();
    ?>

    <script>
        // Prepare data for Chart.js
        var demandData = <?php echo json_encode($demandData); ?>;

        var gasTypes = [];
        var totalOrdersByGasType = {};

        // Organize data for the chart
        demandData.forEach(function (data) {
            if (!gasTypes.includes(data.gas_type)) {
                gasTypes.push(data.gas_type);
                totalOrdersByGasType[data.gas_type] = [];
            }
            totalOrdersByGasType[data.gas_type][data.capacity] = data.total_orders;
        });

        // Prepare datasets for different capacities
        var datasets = [];
        var capacities = [...new Set(demandData.map(data => data.capacity))];

        capacities.forEach(function (capacity) {
            datasets.push({
                label: 'Capacity ' + capacity,
                data: gasTypes.map(function (gasType) {
                    return totalOrdersByGasType[gasType][capacity] || 0; // Use 0 if no orders for that capacity
                }),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            });
        });

        // Create the chart
        var ctx = document.getElementById("chart-bar").getContext("2d");
        var gradientStroke = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke.addColorStop(1, 'rgba(203,12,159,0.2)');
        gradientStroke.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke.addColorStop(0, 'rgba(203,12,159,0)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: gasTypes,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#9dc1d8'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            suggestedMin: 0,
                            beginAtZero: true,
                            padding: 15,
                            color: "#fff"
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            display: true,
                            color: "#fff"
                        },
                    },
                },
            },
        });
    </script>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gas_trading";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "
    SELECT gas_type, capacity, COUNT(*) as total_orders, SUM(quantity * price) as total_income
    FROM orders
    WHERE status = 'Paid'
    GROUP BY gas_type, capacity
    ORDER BY gas_type, capacity
";

    $result = $conn->query($sql);

    $gasData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $gasData[] = [
                'gas_type' => $row['gas_type'],
                'capacity' => $row['capacity'],
                'total_orders' => (int) $row['total_orders'],
                'total_income' => (float) $row['total_income']
            ];
        }
    } else {
        echo "No data found for paid orders.";
    }

    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var gasData = <?php echo json_encode($gasData); ?>;

        var labels = gasData.map(function (item) {
            return `${item.gas_type} (${parseInt(item.capacity)})`;
        });

        function getRandomColor() {
            return 'hsl(' + Math.floor(Math.random() * 360) + ', 70%, 60%)';
        }

        var datasets = gasData.map(function (item) {
            return {
                label: `${item.gas_type} (${parseInt(item.capacity)})`,
                data: [item.total_income],
                backgroundColor: getRandomColor(),
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            };
        });

        var ctx = document.getElementById("chart-line").getContext("2d");
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Total Income"],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Income'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Gas Type and Capacity'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return `${tooltipItem.dataset.label}: ₱${tooltipItem.raw.toFixed(2)}`;
                            }
                        },
                        titleFont: {
                            size: 14,
                            color: 'green'
                        },
                        bodyFont: {
                            size: 16,
                            color: 'green'
                        },
                        footerFont: {
                            size: 16,
                            color: 'green'
                        }
                    }
                }
            }
        });
    </script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script src="../assetss/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>