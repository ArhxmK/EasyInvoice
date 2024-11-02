<?php
session_start();
define("PAGE_TITLE", "Item Management");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php"; // Ensure this path is correct

// Fetch items for display
function fetchItems($conn) {
    $sql = "SELECT * FROM item";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$items = fetchItems($conn);
?>

<style>
    h1 {
        text-align: center;
        color: #333;
    }

    .container {
        max-width: 1500px;
        margin: auto;
        padding: 20px;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
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
            display: none;
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
    <br>
    <div id="content" class="container">
        <br>
        <h1>Item Management</h1>
        <br>
        <hr>
        <br><br>

        <!-- Item Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['unit_price']); ?></td>
                            <td class="action-buttons">
                                <button class="btn-update" onclick="editItem(<?php echo $item['id']; ?>)">Update</button>
                                <button class="btn-delete" onclick="deleteItem(<?php echo $item['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editItem(itemId) {
        window.location.href = `edit_item.php?id=${itemId}`;
    }

    function deleteItem(itemId) {
        if (confirm("Are you sure you want to delete this item?")) {
            window.location.href = `delete_item.php?id=${itemId}`;
        }
    }
</script>
<script src="assets/js/script.js"></script>