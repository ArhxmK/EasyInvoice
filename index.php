<?php
session_start();
define("PAGE_TITLE", "Home");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php";

// Fetch counts from the database
$total_customers = $conn->query("SELECT COUNT(*) as count FROM customer")->fetch_assoc()['count'];
$total_items = $conn->query("SELECT COUNT(*) as count FROM item")->fetch_assoc()['count'];
$total_invoices = $conn->query("SELECT COUNT(*) as count FROM invoice")->fetch_assoc()['count'];

// Fetch item quantities for the line chart
$item_quantities = $conn->query("SELECT item_name, quantity FROM item");
$item_data = [];
while ($row = $item_quantities->fetch_assoc()) {
    $item_data[] = $row;
}
?>

<style>
     .dashboard {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .card {
        flex: 1;
        padding: 20px;
        border-radius: 8px;
        background-color: #f7f7f7;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-align: center;
        margin-bottom: 20px;
        min-width: 200px;
    }

    .card h4 {
        margin-bottom: 10px;
    }

    .chart-container {
        width: 100%;
        max-width: 900px;
        height: 400px;
        margin: auto;
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 24px;
        }

        .dashboard {
            flex-direction: column;
            align-items: center;
        }

        .chart-container {
            height: 300px;
        }
    }

    @media (max-width: 480px) {
        h1 {
            font-size: 20px;
        }

        .card {
            width: 100%;
            max-width: 300px;
        }

        .chart-container {
            height: 250px;
        }
    }
</style>

<div class="wrapper">
    <button id="sidebarToggle">&#9776;</button>
    <br><br>
    <div id="content">
        <br>
        <h1><strong>Dashboard Overview</strong></h1>
        <br>
        <hr>
        <br><br>
        <div class="dashboard">
            <div class="card card-1">
                <h4>Total Customers</h4>
                <p id="totalCustomers" data-count="<?php echo $total_customers; ?>">0</p>
            </div>
            <div class="card card-2">
                <h4>Total Items</h4>
                <p id="totalItems" data-count="<?php echo $total_items; ?>">0</p>
            </div>
            <div class="card card-3">
                <h4>Total Invoices</h4>
                <p id="totalInvoices" data-count="<?php echo $total_invoices; ?>">0</p>
            </div>
        </div>

        <br><br>
        <!-- Line chart for item quantities -->
        <div class="chart-container">
            <canvas id="itemQuantityChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Counting effect for the dashboard numbers
    function countUp(element, target) {
        let start = 0;
        const increment = Math.ceil(target / 100); // speed of counting
        const interval = setInterval(() => {
            start += increment;
            if (start > target) start = target;
            element.textContent = start;
            if (start === target) clearInterval(interval);
        }, 20); // update every 20ms for smooth counting
    }

    document.addEventListener("DOMContentLoaded", () => {
        const totalCustomers = document.getElementById('totalCustomers');
        const totalItems = document.getElementById('totalItems');
        const totalInvoices = document.getElementById('totalInvoices');

        countUp(totalCustomers, parseInt(totalCustomers.getAttribute('data-count')));
        countUp(totalItems, parseInt(totalItems.getAttribute('data-count')));
        countUp(totalInvoices, parseInt(totalInvoices.getAttribute('data-count')));
    });

    // Data for the line chart
    const itemLabels = <?php echo json_encode(array_column($item_data, 'item_name')); ?>;
    const itemQuantities = <?php echo json_encode(array_column($item_data, 'quantity')); ?>;

    // Initialize line chart for item quantities
    const ctx = document.getElementById('itemQuantityChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: itemLabels,
            datasets: [{
                label: 'Item Quantity Remaining',
                data: itemQuantities,
                fill: false,
                borderColor: 'rgba(149, 41, 144, 0.8)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Item Names'
                    }
                }
            }
        }
    });
</script>

<script src="assets/js/script.js"></script> 