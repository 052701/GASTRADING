<?php
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

    <title>Orders</title>
</head>
<style>
       select,
        input[type="number"],
        button {
            padding: 8px;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        select {
            width: 100%;
            max-width: 300px;
        }

        input[type="number"] {
            width: 100%;
            max-width: 200px;
        }

        .amount-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }

        .amount-display h3 {
            font-size: 20px;
            margin: 0;
            color: #444;
        }

        #change_amount {
            font-weight: bold;
            color: #28a745;
        }


        .container h2 {
            text-align: center;
        }

        .total-section,
        .payment-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            color: #333;
        }

        th {
            background-color: #f1f1f1;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            table {
                font-size: 14px;
                min-width: 500px;
            }

            th,
            td {
                padding: 8px;
            }
        }

        @media screen and (max-width: 480px) {
            table {
                font-size: 12px;
                min-width: 400px;
            }

            th,
            td {
                padding: 6px;
            }
        }
</style>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-smile'></i>
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
                <a href="expenses.php">
                    <i class='bx bx-wallet-alt'></i>
                    <span class="text">Expenses</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="#" class="logout">
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
            <div class="container mt-5">
                <h2>Select Recent Customer</h2>
                <select id="customer_select" onchange="fetchOrders()">
                    <option value="">Select Customer</option>
                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "gas_trading";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $customer_sql = "
                    SELECT DISTINCT customers.id, customers.name 
                    FROM customers customers
                    JOIN orders orders ON customers.id = orders.customer_id
                    WHERE customers.created_at >= NOW() - INTERVAL 1 DAY
                    AND orders.status = 'Unpaid'
                    ORDER BY customers.created_at DESC";

                    $customer_result = $conn->query($customer_sql);

                    if ($customer_result->num_rows > 0) {
                        while ($row = $customer_result->fetch_assoc()) { ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php }
                    } else {
                        echo "<option value=''>No recent customers found</option>";
                    }

                    $customer_result->free();
                    $conn->close();
                    ?>
                </select>

                <h2>Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Gas Type</th>
                            <th>Capacity</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="order_table_body">
                        <!-- Orders will be populated here -->
                    </tbody>
                </table>

                <div class="amount-display">
                    <h3>Total Amount: ₱<span id="total_amount">0.00</span></h3>
                </div>

                <div class="payment-section">
                    <h2>Make Payment</h2>
                    <label for="payment_amount">Payment Amount: </label>
                    <input type="number" id="payment_amount" oninput="calculateChange()" step="0.01">
                    <h3>Change: ₱<span id="change_amount">0.00</span></h3>
                    <button onclick="submitPayment()" class="btn btn-primary">Submit Payment</button>
                </div>

                <div id="receipt-section" style="display: none; margin-top: 20px;">
                    <h3>Receipt</h3>
                    <div id="receipt-content">
                        <!-- Receipt details will be dynamically inserted here -->
                    </div>
                    <button onclick="printReceipt()" class="btn btn-primary">Print Receipt</button>
                </div>
            </div>

            <!--END OF MAIN CONTENT-->

            </div>
        </main>
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
        let totalAmount = 0;

        function fetchOrders() {
            const customerId = document.getElementById("customer_select").value;
            if (customerId) {
                fetch(`fetch_orders.php?customer_id=${customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        let orderTable = document.getElementById("order_table_body");
                        orderTable.innerHTML = "";

                        totalAmount = 0;

                        data.orders.forEach(order => {
                            let row = orderTable.insertRow();
                            row.insertCell(0).innerText = order.gas_type;
                            row.insertCell(1).innerText = order.capacity;
                            row.insertCell(2).innerText = order.quantity;
                            row.insertCell(3).innerText = order.price;
                            row.insertCell(4).innerText = order.amount;

                            totalAmount += parseFloat(order.amount);
                        });

                        document.getElementById("total_amount").innerText = totalAmount.toFixed(2);
                        calculateChange();
                    })
                    .catch(error => {
                        console.error('Error fetching orders:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not fetch orders.',
                        });
                    });
            }
        }

        function calculateChange() {
            const paymentAmount = parseFloat(document.getElementById("payment_amount").value) || 0;
            const change = paymentAmount - totalAmount;
            document.getElementById("change_amount").innerText = change >= 0 ? change.toFixed(2) : "0.00";
        }

        function submitPayment() {
    const customerId = document.getElementById("customer_select").value;
    const paymentAmount = parseFloat(document.getElementById("payment_amount").value) || 0;
    const change = paymentAmount - totalAmount;

    if (paymentAmount >= totalAmount) {
        fetch(`submit_payment.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ customer_id: customerId, payment_amount: paymentAmount, total_amount: totalAmount })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: 'Your payment has been processed successfully.',
                    }).then(() => {
                        location.reload(); // Auto refresh the page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'There was an error processing your payment.',
                    });
                }
            })
            .catch(error => {
                console.error('Error submitting payment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not submit payment.',
                });
            });
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Payment',
            text: 'Please enter a valid payment amount.',
        });
    }
}

    </script>

    <script>
        function printReceipt() {
            const receiptContent = document.getElementById("receipt-content").innerHTML;

            // Create a new window for the receipt
            const printWindow = window.open("", "_blank", "width=600,height=600");
            printWindow.document.open();
            printWindow.document.write(`
        <html>
            <head>
                <title>Receipt</title>
                <style>
                    /* Add styles for printing the receipt */
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h3 { text-align: center; }
                    p { margin: 5px 0; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <h3>Receipt</h3>
                ${receiptContent}
            </body>
        </html>
    `);
            printWindow.document.close();
        }

    </script>
</body>

</html>