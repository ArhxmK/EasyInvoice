<?php
session_start();
require "assets/dbh/connector.php"; // Ensure this path is correct

// Check if ID is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $itemId = $_GET['id'];

    // Prepare and execute delete statement
    $sql = "DELETE FROM item WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Item deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting item.";
    }
}

// Redirect back to item management page
header("Location: items_management.php");
exit;
?>
s