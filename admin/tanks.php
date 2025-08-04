<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';
$query = "SELECT id, cylinder_number, gas_type, capacity, price, status FROM cylinders";
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
                <button type="button" class="btn btn-primary mb-3" style="font-size: 11px;" data-bs-toggle="modal"
                    data-bs-target="#addCylinderModal">
                    <i class='bx bx-plus-medical'></i> ADD/RESTOCK CYLINDER
                </button>

                <div class="table-responsive">
                    <table id="tanktable" class="table table-striped">
                        <thead>
                            <tr>
                                <th style="display: none;">ID</th>
                                <th scope="col">Cylinder Number</th>
                                <th scope="col">Gas Type</th>
                                <th scope="col">Capacity (Liters)</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th> <!-- New Actions Column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td style="display: none;"><?php echo htmlspecialchars($row['id']); ?></td>

                                        <td><?php echo htmlspecialchars($row['cylinder_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gas_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm edit-button"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-cylinder_number="<?php echo $row['cylinder_number']; ?>"
                                                data-gas_type="<?php echo $row['gas_type']; ?>"
                                                data-capacity="<?php echo htmlspecialchars($row['capacity']); ?>"
                                                data-price="<?php echo $row['price']; ?>"
                                                data-status="<?php echo $row['status']; ?>" data-bs-toggle="modal"
                                                data-bs-target="#editCylinderModal">
                                                <i class='bx bx-edit'></i> Edit
                                            </button>


                                            <button class="btn btn-danger btn-sm delete-button"
                                                data-id="<?php echo $row['id']; ?>" style="min-width: 80px;">
                                                <i class='bx bx-trash'></i> Delete
                                            </button>
                        </div>
                        </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No cylinders found</td>
                    </tr>
                <?php endif; ?>
                </tbody>

                </table>

                <div class="modal fade" id="addCylinderModal" tabindex="-1" aria-labelledby="addCylinderModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCylinderModalLabel">Add Cylinder Information</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <div id="cylinder-group">
                                        <div class="cylinder-row mb-3">
                                            <label for="cylinderNumber[]" class="form-label">Cylinder Number</label>
                                            <input type="text" class="form-control" name="cylinder_number[]" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gasType" class="form-label">Gas Type</label>
                                            <select id="gasType" class="form-control" name="gas_type[]" required>
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
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity (Kilogram)</label>
                                            <select id="capacity" class="form-control" name="capacity[]" required>
                                                <option value="" disabled selected>Select Capacity</option>
                                                <option value="5">5 Kilogram</option>
                                                <option value="10">10 Kilogram</option>
                                                <option value="15">15 Kilogram</option>
                                                <option value="20">20 Kilogram</option>
                                                <optZion value="25">25 Kilogram</optZion>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" class="form-control" name="price[]" required>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary mb-3" onclick="addCylinderRow()">Add
                                        Another Cylinder</button>
                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editCylinderModal" tabindex="-1" aria-labelledby="editCylinderModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCylinderModalLabel">Edit Cylinder Information</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="update_cylinder.php" id="updateForm">
                                    <input type="hidden" name="id" id="cylinder_id">
                                    <div class="mb-3">
                                        <label for="cylinder_number" class="form-label">Cylinder Number</label>
                                        <input type="text" class="form-control" name="cylinder_number"
                                            id="cylinder_number" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gasType" class="form-label">Gas Type</label>
                                        <select class="form-control" name="gas_type" id="gas_type" required>
                                        <option value="" disabled selected>Select</option>
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
                                    <div class="mb-3">
                                        <label for="capacity" class="form-label">Capacity (Kilogram)</label>
                                        <select class="form-control" name="capacity" id="capacity" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="5">5 Kilogram</option>
                                            <option value="10">10 Kilogram</option>
                                            <option value="15">15 Kilogram</option>
                                            <option value="20">20 Kilogram</option>
                                            <option value="25">25 Kilogram</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" class="form-control" name="price" id="price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" class="form-control" name="status" id="status" required>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="confirmUpdate">Update
                                        Cylinder</button>
                                </form>

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
        document.getElementById('confirmUpdate').addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this cylinder?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $("#updateForm").serialize(); // Get form data

                    $.ajax({
                        type: "POST",
                        url: "update_cylinder.php",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Reload page after success
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong!',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $(".edit-button").click(function () {
                let id = $(this).data("id");
                let cylinder_number = $(this).data("cylinder_number");
                let gas_type = $(this).data("gas_type");
                let capacity = $(this).data("capacity");
                let price = $(this).data("price");
                let status = $(this).data("status");

                $("#cylinder_id").val(id);
                $("#cylinder_number").val(cylinder_number);
                $("#gas_type").val(gas_type);
                $("#price").val(price);
                $("#status").val(status);

                // ✅ Ensure the dropdown selects the correct value
                $("#capacity").val(capacity).prop("selected", true).change();

                // ✅ Debugging: Log the values to the console
                console.log("Selected Capacity:", capacity);

                $("#editCylinderModal").modal("show");
            });
        });

    </script>


    <script>
        document.getElementById('logoutBtn').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default link behavior

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
                    window.location.href = "logout.php"; // Redirect to logout page
                }
            });
        });
    </script>

    <script>
        function addCylinderRow() {
            var cylinderRow = document.createElement('div');
            cylinderRow.classList.add('cylinder-row', 'mb-3');

            // Get the price from the last added row, if exists
            var lastPrice = document.querySelector('input[name="price[]"]:last-of-type');
            var priceValue = lastPrice ? lastPrice.value : '';

            cylinderRow.innerHTML = `
                <label for="cylinderNumber[]" class="form-label">Cylinder Number</label>
                <input type="text" class="form-control" name="cylinder_number[]" required>
                
                <label for="gasType[]" class="form-label">Gas Type</label>
                <select class="form-control" name="gas_type[]" required>
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

                <label for="capacity[]" class="form-label">Capacity (Liters)</label>
                <select class="form-control" name="capacity[]" required>
                    <option value="" disabled selected>Select Capacity</option>
                    <option value="5">5 Kilogram</option>
                    <option value="10">10 Kilogram</option>
                    <option value="15">15 Kilogram</option>
                    <option value="20">20 Kilogram</option>
                    <option value="25">25 Kilogram</option>
                </select>

                <label for="price[]" class="form-label">Price</label>
                <input type="number" class="form-control" name="price[]" value="${priceValue}" required>

                <button type="button" class="btn btn-danger remove-cylinder" onclick="removeCylinderRow(this)">Remove</button>
            `;

            document.getElementById('cylinder-group').appendChild(cylinderRow);
        }

        function removeCylinderRow(button) {
            var row = button.closest('.cylinder-row');
            row.remove();
        }
    </script>

    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                const cylinderNumber = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You won't be able to revert this!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_cylinder.php',
                            type: 'POST',
                            data: { cylinder_number: cylinderNumber },
                            success: function (response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your cylinder has been deleted.',
                                    'success'
                                );
                                location.reload(); // Reload the page to see changes
                            },
                            error: function () {
                                Swal.fire(
                                    'Error!',
                                    'There was a problem deleting the cylinder.',
                                    'error'
                                );
                            }
                        });
                    }
                });
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

</body>

</html>