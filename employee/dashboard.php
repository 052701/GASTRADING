<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
include 'database.php';

// Fetch existing orders
$sql = "
    SELECT 
        orders.id, orders.customer_id, orders.cylinder_number, orders.gas_type, orders.capacity, orders.quantity, orders.price, orders.status, 
        orders.delivery_status, orders.completed_at,
        customers.name, customers.location, customers.delivery_date, customers.delivery_truck
    FROM 
        orders 
    JOIN 
        customers 
    ON 
        orders.customer_id = customers.id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="bell.css">
    <link rel="stylesheet" href="../style.css">

    <title>Delivery</title>
</head>
<style>

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
                    <i class='bx bx-package'></i>
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
            <a href="#" class="nav-link">Dashboard</a>
        </nav>
        <main>
            <!--MAIN CONTENT HERE-->

            <div class="container">

                <div class="d-flex">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                        data-bs-target="#orderModal"><i class='bx bx-plus-medical'></i>
                        Add Delivery
                    </button>

                    <button type="button" class="btn btn-primary" onclick="window.location.href='orders.php'">
                        <i class='bx bx-money-withdraw'></i> Pay
                    </button>
                </div>

                <!-- Modal Structure -->
                <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="orderModalLabel">New Delivery Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Order Form -->
                                <form method="POST" action="save_order.php">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="customer_location"
                                            name="customer_location" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_truck" class="form-label">Delivery Truck</label>
                                        <input type="text" class="form-control" id="delivery_truck"
                                            name="delivery_truck" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_date" class="form-label">Delivery Date</label>
                                        <input type="date" class="form-control" id="delivery_date" name="delivery_date"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_status">Payment Status:</label>
                                        <input type="text" id="payment_status" name="payment_status" value="Unpaid"
                                            readonly>
                                    </div>

                                    <!-- Order Items -->
                                    <div id="orderItems">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="gasType" class="form-label">Gas Type</label>
                                                <select class="form-control gasType" name="gas_type[]" required>
                                                    <option value="" disabled selected>Select Type</option>
                                                    <option value="ARGON">ARGON</option>
                                                    <option value="ACETYLENE">ACETYLENE</option>
                                                    <option value="CARBON DIOXIDE">CARBON DIOXIDE</option>
                                                    <option value="HELIUM">HELIUM</option>
                                                    <option value="HYDROGEN">HYDROGEN</option>
                                                    <option value="LPG">LPG/PROPANE</option>
                                                    <option value="NITROGEN">NITROGEN</option>
                                                    <option value="OXYGEN">OXYGEN</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="capacity" class="form-label">Capacity (in Liters)</label>
                                                <select class="form-control capacity" name="capacity[]" required>
                                                    <option value="" disabled selected>Select Capacity</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" name="quantity[]"
                                                    placeholder="Quantity" required>
                                            </div>
                                            <div class="col">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="text" class="form-control price" name="price[]"
                                                    placeholder="Price" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-secondary" id="addMoreItems">Add More
                                        Items</button>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary" name="submit">Submit
                                            Order</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editOrderModalLabel">Edit Delivery Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="update_order.php">
                                    <input type="hidden" id="edit_order_id" name="order_id">

                                    <div class="mb-3">
                                        <label for="edit_customer_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="edit_customer_name"
                                            name="customer_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_customer_location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="edit_customer_location"
                                            name="customer_location" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_delivery_truck" class="form-label">Delivery Truck</label>
                                        <input type="text" class="form-control" id="edit_delivery_truck"
                                            name="delivery_truck" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_delivery_date" class="form-label">Delivery Date</label>
                                        <input type="date" class="form-control" id="edit_delivery_date"
                                            name="delivery_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_gas_type" class="form-label">Gas Type</label>
                                        <input type="text" class="form-control" id="edit_gas_type" name="gas_type"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_capacity" class="form-label">Capacity</label>
                                        <input type="text" class="form-control" id="edit_capacity" name="capacity"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="edit_quantity" name="quantity"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_price" class="form-label">Price</label>
                                        <input type="text" class="form-control" id="edit_price" name="price" readonly>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary" name="submit">Update
                                            Order</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-5">
                    <h2>Existing Orders</h2>
                    <table id="ordersTable" class="display">
                        <thead>
                            <tr>
                                <th style="display: none;">ID</th>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Delivery Date</th>
                                <th>Delivery Truck</th>
                                <th>Cylinder Number</th>
                                <th>Gas Type</th>
                                <th>Capacity</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Delivery Status</th>
                                <th>Action</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td style="display: none;"><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['delivery_truck']); ?></td>
                                        <td><?php echo htmlspecialchars($row['cylinder_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gas_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td>
                                            <span class="status-text">
                                                <?php echo htmlspecialchars($row['delivery_status']); ?>
                                            </span>
                                            <?php if ($row['delivery_status'] === 'Done' && !empty($row['completed_at'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    Completed at:
                                                    <?php echo date("F j, Y", strtotime($row['completed_at'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <select class="form-select status-dropdown" style="width: 80px; text-align: center;"
                                                data-customer-id="<?php echo htmlspecialchars($row['customer_id']); ?>">
                                                <option value="Undone" <?php echo ($row['delivery_status'] == 'Undone') ? 'selected' : ''; ?>>❌</option>
                                                <option value="Done" <?php echo ($row['delivery_status'] == 'Done') ? 'selected' : ''; ?>>✅</option>
                                            </select>
                                        </td>


                                        <td>
                                            <div class="btn-group" role="group" aria-label="Order Actions">
                                                <button type='button' class='btn btn-warning btn-sm edit-btn'
                                                    data-bs-toggle='modal' data-bs-target='#editOrderModal'
                                                    data-id='<?php echo htmlspecialchars($row['id']); ?>'
                                                    data-name='<?php echo htmlspecialchars($row['name']); ?>'
                                                    data-location='<?php echo htmlspecialchars($row['location']); ?>'
                                                    data-deliverytruck='<?php echo htmlspecialchars($row['delivery_truck']); ?>'
                                                    data-deliverydate='<?php echo htmlspecialchars($row['delivery_date']); ?>'
                                                    data-gastype='<?php echo htmlspecialchars($row['gas_type']); ?>'
                                                    data-capacity='<?php echo htmlspecialchars($row['capacity']); ?>'
                                                    data-quantity='<?php echo htmlspecialchars($row['quantity']); ?>'
                                                    data-price='<?php echo htmlspecialchars($row['price']); ?>'
                                                    data-totalamount='<?php echo htmlspecialchars($row['total_amount']); ?>'>
                                                    <i class='bx bxs-edit-alt bx-sm' style='color:#0a0808'></i>
                                                </button>

                                                <button class="btn btn-danger btn-sm deleteOrderButton"
                                                    data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                                    <i class='bx bxs-trash-alt bx-sm' style='color:#0a0808'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!----end main--->
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
            $(".deleteOrderButton").click(function () {
                let orderId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This order will be permanently deleted!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "delete_order.php",
                            type: "POST",
                            data: { id: orderId },
                            success: function (response) {
                                if (response.trim() === "success") {
                                    Swal.fire(
                                        "Deleted!",
                                        "The order has been deleted.",
                                        "success"
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("Error!", "Failed to delete the order.", "error");
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".status-dropdown").forEach(dropdown => {
                dropdown.addEventListener("change", function () {
                    let customerId = this.getAttribute("data-customer-id");
                    let newStatus = this.value;

                    // Show confirmation before updating
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to update the delivery status!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, update it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("update_status.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: `customer_id=${customerId}&delivery_status=${newStatus}`
                            })
                                .then(response => response.text())
                                .then(data => {
                                    if (data === "success") {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Updated!",
                                            text: "Delivery status updated successfully.",
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: "Error updating delivery status. It is already marked as Done.",
                                        }).then(() => {
                                            location.reload();
                                        });
                                    }
                                });
                        } else {
                            this.value = this.getAttribute("data-prev-value");
                        }
                    });
                });

                dropdown.addEventListener("focus", function () {
                    this.setAttribute("data-prev-value", this.value);
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $.ajax({
                url: 'get_gastype.php',
                type: 'GET',
                success: function (response) {
                    $('.gasType').html(response);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching gas types: ", error);
                }
            });
        });
    </script>
    <script>
        $(document).on('change', '.gasType', function () {
            var selectedGas = $(this).val();
            var capacitySelect = $(this).closest('.row').find('.capacity');

            capacitySelect.html('<option value="" disabled selected>Select Capacity</option>');

            $.ajax({
                url: 'get_capacity.php',
                type: 'POST',
                data: { gas_type: selectedGas },
                success: function (response) {
                    capacitySelect.html(response);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching capacities: ", error);
                }
            });
        });
    </script>

    <script>//For datatables
        $(document).ready(function () {
            $('#ordersTable').DataTable();
        });
    </script>

    <script>
        $(document).ready(function () {
            function fetchPrice(gasType, capacity, index) {
                $.ajax({
                    url: 'get_price.php',
                    type: 'POST',
                    data: {
                        gas_type: gasType,
                        capacity: capacity
                    },
                    success: function (response) {
                        $('.price').eq(index).val(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching price:', error);
                    }
                });
            }

            $(document).on('change', '.gasType, .capacity', function () {
                const index = $(this).closest('.row').index();
                const gasType = $('.gasType').eq(index).val();
                const capacity = $('.capacity').eq(index).val();
                if (gasType && capacity) {
                    fetchPrice(gasType, capacity, index);
                }
            });


            $(document).on('click', '.deleteItem', function () {
                $(this).closest('.order-item').remove();
            });

            $('#orderModal').on('hidden.bs.modal', function () {
                $('#orderItems .order-item').slice(1).remove();

                const firstRow = $('#orderItems .order-item:first');
                firstRow.find('select').val('');
                firstRow.find('input[type="number"]').val('');
                firstRow.find('.price').val('');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            function fetchGasTypes(selectElement) {
                $.ajax({
                    url: 'get_gastype.php',
                    type: 'GET',
                    success: function (response) {
                        selectElement.html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching gas types: ", error);
                    }
                });
            }

            fetchGasTypes($('.gasType'));

            // Add More Items 
            $('#addMoreItems').click(function () {
                const newRow = $(`
                <div class="row mb-3 order-item">
                    <div class="col">
                        <select class="form-control gasType" name="gas_type[]" required>
                            <option value="" disabled selected>Select Type</option>
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-control capacity" name="capacity[]" required>
                            <option value="" disabled selected>Select Capacity</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control price" name="price[]" placeholder="Price" readonly>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-danger deleteItem">Delete</button>
                    </div>
                </div>
            `);

                $('#orderItems').append(newRow);

                fetchGasTypes(newRow.find('.gasType'));
            });

            $(document).on('click', '.deleteItem', function () {
                $(this).closest('.order-item').remove();
            });

            $('#orderModal').on('hidden.bs.modal', function () {
                $('#orderItems .order-item').slice(1).remove();
                const firstRow = $('#orderItems .order-item:first');
                firstRow.find('select').val('');
                firstRow.find('input[type="number"]').val('');
                firstRow.find('.price').val('');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#editOrderModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var location = button.data('location');
                var deliveryTruck = button.data('deliverytruck');
                var deliveryDate = button.data('deliverydate');
                var gasType = button.data('gastype');
                var capacity = button.data('capacity');
                var quantity = button.data('quantity');
                var price = button.data('price');

                var modal = $(this);

                modal.find('#edit_order_id').val(id);
                modal.find('#edit_customer_name').val(name);
                modal.find('#edit_customer_location').val(location);
                modal.find('#edit_delivery_truck').val(deliveryTruck);
                modal.find('#edit_delivery_date').val(deliveryDate);
                modal.find('#edit_gas_type').val(gasType);
                modal.find('#edit_capacity').val(capacity);
                modal.find('#edit_quantity').val(quantity);
                modal.find('#edit_price').val(price);
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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