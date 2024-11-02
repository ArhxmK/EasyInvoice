<?php
session_start();
require "assets/dbh/connector.php";

// Get the customer ID from the URL
$customerId = $_GET['id'];

// Delete the customer from the database
$sql = "DELETE FROM customer WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);

if ($stmt->execute()) {
    echo "<script>alert('Customer deleted successfully!'); window.location.href = 'customer_management.php';</script>";
} else {
    echo "<p style='color: red;'>Error deleting customer.</p>";
}
