<?php
session_start();
define("PAGE_TITLE", "Customers");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php"; // Database connection file

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Customer class for customer registration
class Customer {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function register($title, $first_name, $middle_name, $last_name, $contact_no, $district) {
        $sql = "INSERT INTO customer (title, first_name, middle_name, last_name, contact_no, district) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $title, $first_name, $middle_name, $last_name, $contact_no, $district);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

// Initialize variables for form data and message
$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST['title'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $contact_no = $_POST['contact_no'];
    $district = $_POST['district'];

    // Instantiate the Customer class and register the customer
    $customer = new Customer($conn);
    if ($customer->register($title, $first_name, $middle_name, $last_name, $contact_no, $district)) {
        $message = "Customer registered successfully!";
    } else {
        $message = "Error registering customer.";
    }
}
?>

<style>
    .container {
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
    h1, h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
    .customer-form {
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 40px;
    }
    .customer-form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .customer-form input,
    .customer-form select {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .customer-form button {
        width: 100%;
        padding: 10px;
        background-color: #952990;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .customer-form button:hover {
        background-color: #631B60;
    }
</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <div id="content">
        <h1><strong>Register Customers</strong></h1>
        <hr>
        <br><br>

        <!-- Display Success/Error Message -->
        <?php if (!empty($message)): ?>
            <div class="<?php echo ($message == "Customer registered successfully!") ? 'alert-success' : 'alert-error'; ?>">
                <strong><?php echo $message; ?></strong>
            </div>
        <?php endif; ?>

        <!-- Customer Registration Form -->
        <form action="" method="POST" class="customer-form">
            <label for="title">Title:</label>
            <select name="title" id="title" required>
                <option value="">Select Title</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Dr">Dr</option>
            </select>

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required maxlength="50">

            <label for="middle_name">Middle Name:</label>
            <input type="text" name="middle_name" id="middle_name" maxlength="50">

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required maxlength="50">

            <label for="contact_no">Contact Number:</label>
            <input type="text" name="contact_no" id="contact_no" required maxlength="10" pattern="\d{10}" title="Enter a valid 10-digit phone number">

            <label for="district">District:</label>
            <input type="text" name="district" id="district" required>

            <button type="submit">Register Customer</button>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
