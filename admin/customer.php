<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';

$sql = "SELECT gas_type, capacity, price, COUNT(id) AS qty
        FROM cylinders
        GROUP BY gas_type, capacity, price";

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

    <title>Records</title>
</head>
<style>
    .info-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 20%;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .info-button:hover {
        transform: translateY(-2px);
        box-shadow: 0px 6px 8px rgba(0, 123, 255, 0.6);
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
            <li class="active">
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
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Records</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->

            <div class="container">
                <h2>Customer Information</h2>
                <table id="cylinderTable" class="display">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT GROUP_CONCAT(id) as ids, name, location FROM customers GROUP BY name";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $ids = htmlspecialchars($row["ids"]);
                                echo "<tr>
                            <td>" . htmlspecialchars(explode(',', $row["ids"])[0]) . "</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td>" . htmlspecialchars($row["location"]) . "</td>
                            <td>
                                <a href='view_customer_info.php?ids=" . $ids . "' title='View More info.'>
                                    <button class='info-button'>i</button>
                                </a>
                            </td>
                        </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No data available</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>

                </table>
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