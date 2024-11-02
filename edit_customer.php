<?php
session_start();
define("PAGE_TITLE", "Edit Customer");
require "assets/includes/header.php";
require "assets/dbh/connector.php";

// Fetch customer details for editing
$customerId = $_GET['id'];
$sql = "SELECT * FROM customer WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

$showSuccessMessage = false; // Variable to control the display of success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_no = $_POST['contact_no'];
    $district = $_POST['district'];

    $updateSql = "UPDATE customer SET title=?, first_name=?, last_name=?, contact_no=?, district=? WHERE id=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssi", $title, $first_name, $last_name, $contact_no, $district, $customerId);

    if ($updateStmt->execute()) {
        $showSuccessMessage = true; // Set the flag to true to show the success message
    } else {
        echo "<p class='error-message'>Error updating customer.</p>";
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

    input, select {
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
    <br><br><br>
    <h1>Edit Customer</h1>
    <br>
    <hr>
    <br><br>
    <?php if ($showSuccessMessage): ?>
        <p class="success-message">Customer updated successfully!</p>
        <script>
            // Redirect after 2 seconds
            setTimeout(function() {
                window.location.href = "customer_management.php";
            }, 2000);
        </script>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($customer['title']); ?>" required>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>

        <label for="contact_no">Contact Number:</label>
        <input type="text" name="contact_no" id="contact_no" value="<?php echo htmlspecialchars($customer['contact_no']); ?>" required>

        <label for="district">District:</label>
        <input type="text" name="district" id="district" value="<?php echo htmlspecialchars($customer['district']); ?>" required>

        <button type="submit">Update Customer</button>
    </form>
</div>
