<?php
session_start();
define("PAGE_TITLE", "Create Invoice");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php"; 

// Function to get the next invoice number sequentially based on the last invoice number
function getNextInvoiceNumber($conn) {
    $sql = "SELECT invoice_no FROM invoice ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    // If there's an existing invoice number, increment it; otherwise, start from 1001
    return ($row ? (int)$row['invoice_no'] + 1 : 1001);
}

// Fetch customers for dropdown
function getCustomers($conn) {
    $sql = "SELECT id, CONCAT(title, ' ', first_name, ' ', last_name) AS name FROM customer";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch items for selection
function getItems($conn) {
    $sql = "SELECT id, item_name, unit_price, quantity FROM item";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$customers = getCustomers($conn);
$items = getItems($conn);
$message = "";
$invoice_no = getNextInvoiceNumber($conn); // Set the new invoice number

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer'];
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $total_amount = 0;
    $item_count = count($_POST['item_id']);

    // Calculate total amount
    foreach ($_POST['quantity'] as $index => $quantity) {
        $total_amount += (float)$_POST['unit_price'][$index] * (int)$quantity;
    }

    // Insert into invoice table
    $sql = "INSERT INTO invoice (date, time, invoice_no, customer, item_count, amount) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $date, $time, $invoice_no, $customer_id, $item_count, $total_amount);

        if ($stmt->execute()) {
            // Insert items into invoice_master and update inventory
            foreach ($_POST['item_id'] as $index => $item_id) {
                $quantity = (int)$_POST['quantity'][$index];
                $unit_price = (float)$_POST['unit_price'][$index];
                $amount = $unit_price * $quantity;

                // Insert into invoice_master
                $sql_item = "INSERT INTO invoice_master (invoice_no, item_id, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)";
                $stmt_item = $conn->prepare($sql_item);
                if ($stmt_item) {
                    $stmt_item->bind_param("sssss", $invoice_no, $item_id, $quantity, $unit_price, $amount);
                    $stmt_item->execute();

                    // Update inventory in item table
                    $sql_update = "UPDATE item SET quantity = quantity - ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    if ($stmt_update) {
                        $stmt_update->bind_param("ii", $quantity, $item_id);
                        $stmt_update->execute();
                    } else {
                        echo "Error preparing inventory update statement: " . $conn->error;
                    }
                } else {
                    echo "Error preparing item insertion statement: " . $conn->error;
                }
            }
            $message = "Invoice created successfully!";
        } else {
            $message = "Error creating invoice: " . $stmt->error;
        }
    } else {
        echo "Error preparing invoice statement: " . $conn->error;
    }
}
?>
   <style>
    .container {
        padding: 20px;
        max-width: 1500px; /* Narrower max width */
        margin: auto;
        
    }
    h1 {
        text-align: center; /* Left-align heading */
        margin-bottom: 15px;
    }
    .alert-success, .alert-error {
        padding: 10px;
        font-size: 14px;
        border-radius: 4px;
        margin-bottom: 15px;
        text-align: center;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Form Styles */
    .invoice-form, .invoice-item {
        padding: 15px; /* Reduced padding for compact form */
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 20px;
    }
    .invoice-form label, .invoice-item label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .invoice-form input, .invoice-form select, .invoice-item input {
        width: 100%;
        padding: 6px; /* Smaller padding for inputs */
        margin-bottom: 10px; /* Reduced spacing between fields */
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    /* Button Styling */
    .add-item-btn, .submit {
        margin-top: 10px;
        background-color: #952990;
        color: white;
        padding: 8px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }
    .add-item-btn:hover, .submit:hover {
        background-color: #631B60;
    }
</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <div id="content" class="container">
    <br>
    <h1>Create Invoice</h1>
    <br>
    <hr>
    <br><br>
    <?php if (!empty($message)): ?>
        <div class="<?php echo ($message == "Invoice created successfully!") ? 'alert-success' : 'alert-error'; ?>">
            <strong><?php echo $message; ?></strong>
        </div>
    <?php endif; ?>

    <!-- Invoice Form -->
    <form action="" method="POST" class="invoice-form">
        <label for="customer">Customer:</label>
        <select name="customer" id="customer" required>
            <option value="">Select Customer</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <h3>Items</h3>
        <div id="items-container">
            <div class="invoice-item">
                <label for="item_id[]">Item:</label>
                <select name="item_id[]" required onchange="updateUnitPrice(this)">
                    <option value="">Select Item</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item['unit_price']; ?>">
                            <?php echo htmlspecialchars($item['item_name']) . " - $" . $item['unit_price']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity[]">Quantity:</label>
                <input type="number" name="quantity[]" min="1" required>

                <label for="unit_price[]">Unit Price:</label>
                <input type="text" name="unit_price[]" readonly>
            </div>
        </div>

        <button type="button" class="add-item-btn" onclick="addItem()">Add Another Item</button>
        <br><br>
        <button type="submit" class="submit">Create Invoice</button>
    </form>
</div>
</div>

<script>
    // Updates the unit price based on the selected item
    function updateUnitPrice(select) {
        const unitPriceInput = select.closest('.invoice-item').querySelector('input[name="unit_price[]"]');
        const selectedOption = select.options[select.selectedIndex];
        unitPriceInput.value = selectedOption.getAttribute('data-price');
    }

    // Add a new item selection block
    function addItem() {
        const itemsContainer = document.getElementById("items-container");
        const itemDiv = document.createElement("div");
        itemDiv.className = "invoice-item";
        itemDiv.innerHTML = `
            <label for="item_id[]">Item:</label>
            <select name="item_id[]" required onchange="updateUnitPrice(this)">
                <option value="">Select Item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item['unit_price']; ?>">
                        <?php echo htmlspecialchars($item['item_name']) . " - $" . $item['unit_price']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="quantity[]">Quantity:</label>
            <input type="number" name="quantity[]" min="1" required>

            <label for="unit_price[]">Unit Price:</label>
            <input type="text" name="unit_price[]" readonly>
        `;
        itemsContainer.appendChild(itemDiv);
    }
</script>
<script src="assets/js/script.js"></script>
