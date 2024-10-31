<?php
session_start();
define("PAGE_TITLE", "Manage Items");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
?>

<style>
    .container {
        padding: 20px;
        max-width: 800px;
        margin: auto;
        text-align: center;
    }
    .button-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 50px;
    }
    .button-group button {
        padding: 15px 30px;
        font-size: 18px;
        background-color: #952990;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
        max-width: 300px;
        margin: auto;
    }
    .button-group button:hover {
        background-color: #631B60;
    }

</style>
<!-- Sidebar Toggle Button -->
<button id="sidebarToggle">&#9776;</button>
<div class="container">
    <h1>Manage Items</h1>
    <br>
    <hr>
    <br><br>

    <div class="button-group">
        <button onclick="window.location.href='items.php'">Add Item</button>
        <button onclick="window.location.href='item_category.php'">Add Item Category</button>
        <button onclick="window.location.href='item_subcategory.php'">Add Item Subcategory</button>
    </div>
</div>

<script src="assets/js/script.js"></script>
