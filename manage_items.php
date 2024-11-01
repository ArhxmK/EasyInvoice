<?php
session_start();
define("PAGE_TITLE", "Manage Items");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
?>

<style>
    h1 {
        text-align: center;
    }
    
    .container {
        padding: 20px;
        max-width: 1500px;
        margin: auto;
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    
    .button-group {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 30px;
        align-items: center;
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
        max-width: 250px;
    }

    .button-group button:hover {
        background-color: #631B60;
    }

</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <div id="content" class="container">
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
</div>

<script src="assets/js/script.js"></script>
