<?php
session_start();

// Database configuration
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'Csquare';

// Create a connection to the database
$conn = mysqli_connect($hostname, $username, $password, $database);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Connection successful, you can now use $conn to perform database operations
?>