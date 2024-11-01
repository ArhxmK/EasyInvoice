<?php
session_start();
define("PAGE_TITLE", "Add Item Category");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php";

class ItemCategory {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function addCategory($category) {
        $sql = "INSERT INTO item_category (category) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $category);
        return $stmt->execute();
    }
}

// Instantiate the ItemCategory class
$categoryObj = new ItemCategory($conn);
$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];

    if ($categoryObj->addCategory($category)) {
        $message = "Category added successfully!";
    } else {
        $message = "Error adding category.";
    }
}
?>

<style>
    .container { padding: 20px; max-width: 1500px; margin: auto; }
    h1, h2 { text-align: center; margin-bottom: 20px; }
    .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    .alert-error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    .form { padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9; margin-bottom: 40px; }
    .form label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form input { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
    .form button { width: 100%; padding: 10px; background-color: #952990; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .form button:hover { background-color: #631B60 }
</style>
<!-- Sidebar Toggle Button -->
<button id="sidebarToggle">&#9776;</button>
<div id="content" class="container">
    <h1>Add New Item Category</h1>
    <hr>
    <br><br>

    <!-- Display Success/Error Message -->
    <?php if (!empty($message)): ?>
        <div class="<?php echo ($message == "Category added successfully!") ? 'alert-success' : 'alert-error'; ?>">
            <strong><?php echo $message; ?></strong>
        </div>
    <?php endif; ?>

    <!-- Add Item Category Form -->
    <form action="" method="POST" class="form">
        <label for="category">Category:</label>
        <input type="text" name="category" id="category" required maxlength="100">
        <button type="submit">Add Category</button>
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