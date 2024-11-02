<?php
session_start();
define("PAGE_TITLE", "Home");
require "assets/includes/header.php";
require "assets/components/sidebar.php";
require "assets/dbh/connector.php";

// counts from the database
$total_customers = $conn->query("SELECT COUNT(*) as count FROM customer")->fetch_assoc()['count'];
$total_items = $conn->query("SELECT COUNT(*) as count FROM item")->fetch_assoc()['count'];
$total_invoices = $conn->query("SELECT COUNT(*) as count FROM invoice")->fetch_assoc()['count'];

// item quantities for the line chart
$item_quantities = $conn->query("SELECT item_name, quantity FROM item");
$item_data = [];
while ($row = $item_quantities->fetch_assoc()) {
    $item_data[] = $row;
}
?>

<style>
/* card Styles */
.card-box {
    position: relative;
    color: #fff;
    padding: 20px 15px 35px;
    margin: 15px 0;
    width: 30%; 
    min-width: 300px; 
    height: 150px;
    border-radius: 8px;
    text-align: left;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card-box:hover {
    text-decoration: none;
    color: #f1f1f1;
}
.card-box:hover .icon i {
    font-size: 90px;
    transition: 1s;
}
.card-box .inner {
    padding: 5px 8px 0 8px;
}
.card-box h3 {
    font-size: 22px;
    font-weight: bold;
    margin: 0 0 8px 0;
    white-space: nowrap;
    padding: 0;
    text-align: left;
}
.card-box p {
    font-size: 14px;
}
.card-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 60px;
    color: rgba(0, 0, 0, 0.15);
}
.card-box .card-box-footer {
    position: absolute;
    left: 0;
    bottom: 0;
    text-align: center;
    padding: 5px 0;
    color: rgba(255, 255, 255, 0.8);
    background: rgba(0, 0, 0, 0.1);
    width: 100%;
    text-decoration: none;
}
.card-box:hover .card-box-footer {
    background: rgba(0, 0, 0, 0.3);
}

/* Dashboard container and row */
.dashboard {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard .container .row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 60px;
}

/* Background colors */
.bg-blue { background-color: #00c0ef !important; }
.bg-green { background-color: #00a65a !important; }
.bg-orange { background-color: #f39c12 !important; }
.bg-red { background-color: #d9534f !important; }

/* Chart Container */
.chart-container {
    width: 100%;
    max-width: 900px;
    height: 400px;
    margin: auto;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .dashboard {
        flex-direction: column;
        align-items: center;
    }
    .card-box {
        width: 100%; 
        height: auto;
    }
}

@media (max-width: 768px) {
    .card-box h3 {
        font-size: 18px;
    }
    .card-box p {
        font-size: 13px;
    }
    .card-box .icon i {
        font-size: 50px;
    }
    .chart-container {
        height: 300px;
    }
}

@media (max-width: 576px) {
    .card-box {
        width: 100%; 
        padding: 15px 10px 30px;
        margin: 10px 0;
    }
    #content h1 {
        font-size: 22px;
    }
    .chart-container {
        height: 250px;
    }
}

    </style>
</head>
<body>
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
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card-box bg-blue">
                    <div class="inner">
                        <h3 id="totalCustomers" data-count="<?php echo $total_customers; ?>">0</h3>
                        <p> Total Customers </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users" aria-hidden="true"></i>
                    </div>
                    <a href="customer_management.php" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card-box bg-green">
                    <div class="inner">
                        <h3 id="totalItems" data-count="<?php echo $total_items; ?>">0</h3>
                        <p> Total Items </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-box" aria-hidden="true"></i>
                    </div>
                    <a href="items_management.php" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card-box bg-orange">
                    <div class="inner">
                        <h3 id="totalInvoices" data-count="<?php echo $total_invoices; ?>">0</h3>
                        <p> Total Invoices </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-file-invoice" aria-hidden="true"></i>
                    </div>
                    <a href="reports.php" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
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
