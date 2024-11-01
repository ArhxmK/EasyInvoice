<?php
session_start();
define("PAGE_TITLE", "Invoice Report");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php"; 

// Function to fetch invoices with optional filters for date and customer
function getInvoices($conn, $startDate = null, $endDate = null, $customerId = null) {
    $sql = "SELECT i.invoice_no, i.date, i.customer, c.first_name, c.last_name, i.item_count, i.amount 
            FROM invoice i
            JOIN customer c ON i.customer = c.id
            WHERE 1";

    if ($startDate && $endDate) {
        $sql .= " AND i.date BETWEEN ? AND ?";
    }
    if ($customerId) {
        $sql .= " AND i.customer = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($startDate && $endDate && $customerId) {
        $stmt->bind_param("ssi", $startDate, $endDate, $customerId);
    } elseif ($startDate && $endDate) {
        $stmt->bind_param("ss", $startDate, $endDate);
    } elseif ($customerId) {
        $stmt->bind_param("i", $customerId);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch customers for filtering
function getCustomers($conn) {
    $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM customer";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$customers = getCustomers($conn);
$invoices = [];

// Process filter form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $customerId = $_POST['customer'] ?? null;
    $invoices = getInvoices($conn, $startDate, $endDate, $customerId);
}
?>

<style>
    .container { 
        padding: 20px; 
        max-width: 1500px; 
        margin: auto; 
    }
    
    h1 {
        text-align: center;
    }

    /* Styling for the form */
    .report-form { 
        padding: 20px; 
        border: 1px solid #ccc; 
        border-radius: 8px; 
        background-color: #f7f7f7; 
        margin-bottom: 20px; 
        display: flex; 
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center; /* Align items vertically center */
    }

    .report-form > div {
        flex: 1 1 200px; 
        min-width: 150px;
    }

    .report-form label {
        font-weight: bold;
        color: #333;
    }

    .report-form input,
    .report-form select,
    .report-form button {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .report-form button {
        background-color: #952990; 
        color: white; 
        border: none;
        margin-top: 15px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .report-form button:hover {
        background-color: #631B60;
    }

    /* Styling for the report table */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 14px;
    }

    .report-table th, 
    .report-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .report-table th {
        background-color: #952990; 
        color: white;
    }

    .report-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .report-table tr:hover {
        background-color: #631B60;
    }

    .total-row {
        font-weight: bold;
        background-color: #952990;
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
            max-width: 100%; 
        }

        .report-form {
            flex-direction: column; 
        }

        .report-table, .report-table th, .report-table td {
            font-size: 12px; 
            padding: 8px;
        }
    }
</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <div id="content" class="container">
    <br>
    <h1>Invoice Report</h1>
    <br>
    <hr>
<br><br>
    <!-- Filter Form -->
    <form method="POST" class="report-form">
        <div>
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date">
        </div>
        
        <div>
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date">
        </div>

        <div>
            <label for="customer">Customer:</label>
            <select name="customer" id="customer">
                <option value="">Select Customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit">Filter</button>
        </div>
    </form>

    <!-- Display Report Table -->
    <?php if (!empty($invoices)): ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Item Count</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAmount = 0;
                foreach ($invoices as $invoice): 
                    $totalAmount += $invoice['amount'];
                ?>
                    <tr>
                        <td><?php echo $invoice['invoice_no']; ?></td>
                        <td><?php echo $invoice['date']; ?></td>
                        <td><?php echo htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                        <td><?php echo $invoice['item_count']; ?></td>
                        <td><?php echo number_format($invoice['amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4">Total Amount</td>
                    <td><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No invoices found for the selected criteria.</p>
    <?php endif; ?>
</div>
 </div>
<script src="assets/js/script.js"></script>