<?php
session_start();
define("PAGE_TITLE", "Add Item");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php";

class Item {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Method to add an item
    public function addItem($item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price) {
        $sql = "INSERT INTO item (item_code, item_category, item_subcategory, item_name, quantity, unit_price)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price);

        return $stmt->execute();
    }

    // Method to retrieve item categories
    public function getCategories() {
        $sql = "SELECT id, category FROM item_category";
        return $this->conn->query($sql);
    }

    // Method to retrieve item subcategories
    public function getSubcategories() {
        $sql = "SELECT id, sub_category FROM item_subcategory";
        return $this->conn->query($sql);
    }
}

// Instantiate the Item class
$itemObj = new Item($conn);
$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_code = $_POST['item_code'];
    $item_category = $_POST['item_category'];
    $item_subcategory = $_POST['item_subcategory'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    if ($itemObj->addItem($item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price)) {
        $message = "Item added successfully!";
    } else {
        $message = "Error adding item.";
    }
}
?>

<style>
     .container {
        padding: 20px;
        max-width: 1500px;
        margin: auto;
    }
    h1, h2 { 
        text-align: center; 
        margin-bottom: 20px; }
    .alert-success { 
        background-color: #d4edda;
        color: #155724; 
        padding: 15px; border-radius: 5px;
        margin-bottom: 20px; 
        text-align: center; }
    .alert-error {
        background-color: #f8d7da; 
        color: #721c24; 
        padding: 15px; 
        border-radius: 5px; 
        margin-bottom: 20px; 
        text-align: center; }
    .item-form {
         padding: 20px; 
         border: 1px solid #ccc; 
         border-radius: 5px;
         background-color: #f9f9f9;
         margin-bottom: 40px; }
    .item-form label { 
        display: block; 
        margin-bottom: 5px; 
        font-weight: bold; }
    .item-form input, .item-form select { 
        width: 100%; 
        padding: 8px; 
        margin-bottom: 15px; 
        border: 1px solid #ccc; 
        border-radius: 4px; }
    .item-form button { 
        width: 100%; 
        padding: 10px; 
        background-color: #952990; 
        color: white; border: none; 
        border-radius: 5px; c
        ursor: pointer; }
    .item-form button:hover { 
        background-color: #631B60; }

</style>
<div class="wrapper">
<!-- Sidebar Toggle Button -->
<button id="sidebarToggle">&#9776;</button>

<div id="content" class="container">
    <h1>Add New Item</h1>
    <hr>
    <br><br>

    <!-- Display Success/Error Message -->
    <?php if (!empty($message)): ?>
        <div class="<?php echo ($message == "Item added successfully!") ? 'alert-success' : 'alert-error'; ?>">
            <strong><?php echo $message; ?></strong>
        </div>
    <?php endif; ?>

    <!-- Add Item Form -->
    <form action="" method="POST" class="item-form">
        <label for="item_code">Item Code:</label>
        <input type="text" name="item_code" id="item_code" required maxlength="20">

        <label for="item_category">Item Category:</label>
        <select name="item_category" id="item_category" required>
            <option value="">Select Category</option>
            <?php
            $categories = $itemObj->getCategories();
            while ($category = $categories->fetch_assoc()) {
                echo "<option value='{$category['category']}'>{$category['category']}</option>";
            }
            ?>
        </select>

        <label for="item_subcategory">Item Subcategory:</label>
        <select name="item_subcategory" id="item_subcategory" required>
            <option value="">Select Subcategory</option>
            <?php
            $subcategories = $itemObj->getSubcategories();
            while ($subcategory = $subcategories->fetch_assoc()) {
                echo "<option value='{$subcategory['sub_category']}'>{$subcategory['sub_category']}</option>";
            }
            ?>
        </select>

        <label for="item_name">Item Name:</label>
        <input type="text" name="item_name" id="item_name" required maxlength="20">

        <label for="quantity">Quantity:</label>
        <input type="text" name="quantity" id="quantity" required maxlength="20">

        <label for="unit_price">Unit Price:</label>
        <input type="text" name="unit_price" id="unit_price" required maxlength="20">

        <button type="submit">Add Item</button>
    </form>
</div>

<script>
 window.onload = function() {
    // Try to get both possible content div IDs
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content') || document.getElementById('main-content'); // Support both IDs
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Check if the required elements exist
    if (sidebar && content && sidebarToggle) {
        let isSidebarCollapsed = sidebar.classList.contains('collapsed');

        // Add event listener to the sidebar toggle button
        sidebarToggle.addEventListener('click', function(event) {
            event.stopPropagation();  // Prevent event bubbling

            if (isSidebarCollapsed) {
                // Open sidebar
                sidebar.classList.remove('collapsed');
                content.classList.remove('collapsed');
                sidebarToggle.style.color = 'white';
            } else {
                // Close sidebar
                sidebar.classList.add('collapsed');
                content.classList.add('collapsed');
                sidebarToggle.style.color = 'black';
            }

            // Toggle the state
            isSidebarCollapsed = !isSidebarCollapsed;
        });
    } else {
        console.error("Required elements not found (#sidebar, #content or #main-content, #sidebarToggle). Check HTML structure.");
    }
};
</script>
