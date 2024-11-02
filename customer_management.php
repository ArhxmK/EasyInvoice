<?php
session_start();
define("PAGE_TITLE", "Customer Management");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php";

// customers for display
function fetchCustomers($conn, $search = "") {
    $sql = "SELECT * FROM customer";
    if ($search) {
        $sql .= " WHERE CONCAT(title, ' ', first_name, ' ', last_name) LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchParam = '%' . $search . '%';
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}

$search = $_GET['search'] ?? '';
$customers = fetchCustomers($conn, $search);
?>

<style>
    h1{
        text-align: center;
    }
    .container {
        max-width: 1500px;
        margin: auto;
        padding: 20px;
    }
    .search-container {
        display: flex;
        align-items: center;
        gap: 10px; /* space between input and button */
        margin-bottom: 20px;
    }

    .search-container input[type="text"] {
        flex: 1; /* Allows the input to take up available space */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .search-container button {
        padding: 10px 20px;
        background-color: #952990;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .search-container button:hover {
        background-color: #631B60;
    }
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #952990;
        color: white;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    .btn-update, .btn-delete {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-update {
        background-color: #4CAF50;
        color: white;
    }
    .btn-delete {
        background-color: #f44336;
        color: white;
    }
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .search-container {
            flex-direction: column;
            align-items: stretch;
        }

        .search-container input[type="text"],
        .search-container button {
            width: 100%;
        }

        .table-container {
            overflow-x: auto;
        }

        /* Responsive table styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: none; /* Hide table header */
        }

        tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background-color: #fff;
        }

        td {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }

        td:last-child {
            border-bottom: none;
        }

        .action-buttons {
            flex-direction: row;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-update, .btn-delete {
            flex: 1;
        }
    }
</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <div id="content" class="container">
        <br>
        <h1>Customer Management</h1>
        <br>
        <hr>
        <br><br>
        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="customer_management.php">
            <div class="search-container">
    <input type="text" name="search" placeholder="Search by customer name" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</div>

            </form>
        </div>

        <!-- Customer Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact No</th>
                        <th>District</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['title']); ?></td>
                            <td><?php echo htmlspecialchars($customer['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['contact_no']); ?></td>
                            <td><?php echo htmlspecialchars($customer['district']); ?></td>
                            <td class="action-buttons">
                                <button class="btn-update" onclick="editCustomer(<?php echo $customer['id']; ?>)">Update</button>
                                <button class="btn-delete" onclick="deleteCustomer(<?php echo $customer['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editCustomer(customerId) {
        window.location.href = `edit_customer.php?id=${customerId}`;
    }

    function deleteCustomer(customerId) {
        if (confirm("Are you sure you want to delete this customer?")) {
            window.location.href = `delete_customer.php?id=${customerId}`;
        }
    }
</script>
<script src="assets/js/script.js"></script>