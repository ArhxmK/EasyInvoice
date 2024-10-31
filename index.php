<?php
session_start();
define("PAGE_TITLE", "Home");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
?>

<div class="wrapper">
    <!-- Toggle Icon Outside Sidebar -->
    <button id="sidebarToggle">&#9776;</button>
    <br><br>
    <!-- Content -->
    <div id="content">
        <br>
    <h1><strong>Dashboard Overview</strong></h1>
    <br>
    <hr>
        <div class="dashboard">
            <div class="card card-1">
                <h4>Total Employees</h4>
                <p id="totalEmployees" data-count="<?php echo $total_staff; ?>">0</p>
            </div>
            <div class="card card-2">
                <h4>Admission Request</h4>
                <p id="totalAdmissions" data-count="<?php echo $total_admission; ?>">0</p>
            </div>
            <div class="card card-3">
                <h4>Total Courses</h4>
                <p id="totalCourses" data-count="<?php echo $total_courses; ?>">0</p>
            </div>
        </div>
        <br><br><br>
        <!-- Bar chart for monthly profit/loss -->
        <div class="chart-container">
            <canvas id="profitLossChart"></canvas>
        </div>
    </div>

<script src="assets/js/script.js"></script>

