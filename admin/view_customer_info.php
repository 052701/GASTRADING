<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gas_trading");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$queryOrders = "
    SELECT 
        customers.id AS customer_id, 
        customers.name, 
        customers.location, 
        MIN(orders.ordered_at) AS first_ordered_at,
        orders.gas_type, 
        orders.capacity, 
        SUM(orders.quantity) AS total_quantity, 
        SUM(orders.price * orders.quantity) AS total_price,
        COALESCE(payments.total_paid, 0) AS total_paid
    FROM orders
    JOIN customers ON orders.customer_id = customers.id
    LEFT JOIN (
        SELECT customer_id, SUM(amount_paid) AS total_paid
        FROM payments
        GROUP BY customer_id
    ) AS payments ON orders.customer_id = payments.customer_id
    GROUP BY customers.id, orders.gas_type, orders.capacity
";

$resultOrders = $conn->query($queryOrders);

if (!$resultOrders) {
    die("Orders query failed: " . $conn->error);
}

$customerId = 1;
$queryCustomer = "SELECT name, location FROM customers WHERE id = ?";
$stmt = $conn->prepare($queryCustomer);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$resultCustomer = $stmt->get_result();
$customer = $resultCustomer->fetch_assoc();

$customerName = htmlspecialchars($customer['name']);
$customerLocation = htmlspecialchars($customer['location']);
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

    <title>View Customer Info</title>
</head>
<style>
    th,
    td {
        padding: 8px 12px;
        text-align: center;
        position: relative;
        border: none;
    }

    tr.separator td {
        padding-top: 5px;
        border-top: 2px solid black;
    }

    #printForm {
        position: sticky;
        top: 2px;
        z-index: 1000;
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    #printForm button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
    }

    #printForm button:hover {
        background-color: #45a049;
    }

    #printForm button:last-child {
        background-color: #008CBA;
    }

    #printForm button:last-child:hover {
        background-color: #007bb5;
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
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
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
            <a href="#" class="nav-link">Dashboard</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->

            <div class="container">
                <h2><?php echo "Name: " . $customerName . " | Location: " . $customerLocation; ?></h2>

                <form id="printForm">
                    <label for="orderDate">Select Date:</label>
                    <input type="date" id="orderDate" name="orderDate">
                    <button type="button" onclick="printOrders()"><i class='bx bx-printer'></i> Print</button>
                    <button type="button" onclick="clearFilter()">Show All</button>
                </form>

                <table border="1" class="table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Ordered At</th>
                            <th>Gas Type</th>
                            <th>Capacity (In Liters)</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentDate = null;
                        $totalForDate = 0;
                        $totalPaidForDate = 0;

                        while ($row = $resultOrders->fetch_assoc()) {
                            $orderedAt = $row['first_ordered_at'];
                            $gasType = $row['gas_type'];
                            $capacity = $row['capacity'];
                            $quantity = $row['total_quantity'];
                            $price = $row['total_price'];
                            $amountPaid = $row['total_paid'];

                            if ($currentDate !== $orderedAt) {
                                if ($currentDate !== null) {
                                    $change_given = max($totalPaidForDate - $totalForDate, 0);
                                    echo "<tr><td colspan='4' style='text-align:right;'><strong>Total:</strong></td><td><strong>₱ " . number_format($totalForDate, 2) . "</strong></td></tr>";
                                    echo "<tr><td colspan='4' style='text-align:right;'><strong>Total Paid:</strong></td><td><strong>₱ " . number_format($totalPaidForDate, 2) . "</strong></td></tr>";
                                    echo "<tr><td colspan='4' style='text-align:right;'><strong>Change:</strong></td><td><strong>₱ " . number_format($change_given, 2) . "</strong></td></tr>";
                                    echo "<tr><td colspan='5' style='border-top: 2px solid black;'></td></tr>";
                                }
                                $totalForDate = 0;
                                $totalPaidForDate = 0;
                                $currentDate = $orderedAt;
                            }

                            $totalForDate += $price;
                            $totalPaidForDate = $amountPaid;

                            $formattedDate = date('Y-m-d', strtotime($orderedAt));

                            echo "<tr data-date='" . $formattedDate . "'>
                            <td>" . date('F j, Y', strtotime($orderedAt)) . "</td>
                            <td>" . htmlspecialchars($gasType) . "</td>
                            <td>" . number_format($capacity, 0) . " </td>
                            <td>" . htmlspecialchars($quantity) . "</td>
                            <td>₱ " . number_format($price, 2) . "</td>
                        </tr>";
                        }

                        if ($currentDate !== null) {
                            $change_given = max($totalPaidForDate - $totalForDate, 0);
                            echo "<tr><td colspan='4' style='text-align:right;'><strong>Total:</strong></td><td><strong>₱ " . number_format($totalForDate, 2) . "</strong></td></tr>";
                            echo "<tr><td colspan='4' style='text-align:right;'><strong>Total Paid:</strong></td><td><strong>₱ " . number_format($totalPaidForDate, 2) . "</strong></td></tr>";
                            echo "<tr><td colspan='4' style='text-align:right;'><strong>Change:</strong></td><td><strong>₱ " . number_format($change_given, 2) . "</strong></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <a href="customer.php" class="btn btn-primary">Back to Customer List</a>
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
        function printOrders() {
            const selectedDate = document.getElementById("orderDate").value;
            const rows = document.querySelectorAll("#ordersTable tbody tr");

            rows.forEach(row => {
                const rowDate = row.getAttribute("data-date");
                row.style.display = selectedDate && rowDate !== selectedDate ? "none" : "";
            });

            // Pass PHP variables into JavaScript
            const customerName = "<?php echo addslashes($customerName); ?>";
            const customerLocation = "<?php echo addslashes($customerLocation); ?>";

            const printWindow = window.open('', '', 'height=500,width=800');
            const printContent = document.createElement("div");

            printContent.innerHTML = `
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                padding: 8px;
                text-align: left;
                border: none;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            @media print {
                table, th, td {
                    border: none;
                    box-shadow: none;
                }
            }
        </style>
        <h3>Order Summary for: ${customerName} | Location: ${customerLocation}</h3>
        ` + document.getElementById("ordersTable").outerHTML;

            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Function to reset the table
        function clearFilter() {
            document.getElementById("orderDate").value = ""; // Clear the date input
            document.querySelectorAll("#ordersTable tbody tr").forEach(row => {
                row.style.display = ""; // Show all rows
            });
        }

    </script>
</body>

</html>