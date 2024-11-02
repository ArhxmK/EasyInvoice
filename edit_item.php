<?php
session_start();
define("PAGE_TITLE", "Edit Item");
require "assets/includes/header.php";
require "assets/dbh/connector.php";

// item details for editing
$itemId = $_GET['id'];
$sql = "SELECT * FROM item WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

$showSuccessMessage = false; // Variable to control the display of success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    $updateSql = "UPDATE item SET item_name=?, quantity=?, unit_price=? WHERE id=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sdii", $item_name, $quantity, $unit_price, $itemId);

    if ($updateStmt->execute()) {
        $showSuccessMessage = true; // Set the flag to true to show the success message
    } else {
        echo "<p class='error-message'>Error updating item.</p>";
    }
}
?>

<style>
    .container {
        padding: 20px;
        max-width: 1200px;
        margin: auto;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 24px;
        text-align: left;
        margin-bottom: 20px;
        color: #333;
    }

    label {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }

    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #952990;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #631B60;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
            max-width: 100%;
        }
    }
</style>

<div class="container">
    <h1>Edit Item</h1>
    <?php if ($showSuccessMessage): ?>
        <p class="success-message">Item updated successfully!</p>
        <script>
            setTimeout(function() {
                window.location.href = "items_management.php";
            }, 2000);
        </script>
    <?php endif; ?>

    <form method="POST">
        <label for="item_name">Item Name:</label>
        <input type="text" name="item_name" id="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>

        <label for="unit_price">Unit Price:</label>
        <input type="number" step="0.01" name="unit_price" id="unit_price" value="<?php echo htmlspecialchars($item['unit_price']); ?>" required>

        <button type="submit">Update Item</button>
    </form>
</div>
