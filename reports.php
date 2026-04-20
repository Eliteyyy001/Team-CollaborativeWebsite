<?php
session_start();
require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';

if (!isset($_SESSION['userID']) || !isset($_SESSION['userName'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - FreshFold POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: #1f2933;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #f39c12;
        }
        .nav a {
            color: white;
            margin: 0 12px;
            text-decoration: none;
            font-size: 14px;
        }
        .nav a.active {
            font-weight: bold;
            color: #f39c12;
        }
        .panel {
            background: #FFF8DC;
            border: 2px solid #d4b56a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            max-width: 1100px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .panel h2 {
            background: #e6d5a8;
            color: #2c3e50;
            padding: 12px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f4d88c;
        }
        tr:hover {
            background-color: #f5e9d3;
        }
        .report-card {
            background: white;
            border: 1px solid #d4b56a;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div>FreshFold POS</div>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Make Sale</a>
        <a href="products.php">Products</a>
        <a href="reports.php" class="active">Reports</a>
        <a href="sales.php">Sales</a>
        <a href="audit_logs.php">Audit Logs</a>
    </div>
    <div>
        Cashier: <strong><?= htmlspecialchars($_SESSION['userName']) ?></strong> 
        | <a href="logout.php" style="color:#ff9999;">Logout</a>
    </div>
</div>

<div class="panel">
    <h2>Reports</h2>

    <div class="report-card">
        <h3>Daily Sales Summary</h3>
        <p><strong>Date:</strong> <?= date("m/d/Y") ?></p>
        <p>Total Sales: <strong>$0.00</strong></p>
        <p>Total Transactions: <strong>0</strong></p>
        <button onclick="alert('Daily Sales Report - Coming Soon')">Generate Report</button>
    </div>

    <div class="report-card">
        <h3>Top Selling Products</h3>
        <p>This Month</p>
        <button onclick="alert('Top Selling Products Report - Coming Soon')">View Report</button>
    </div>

    <div class="report-card">
        <h3>Low Stock Items</h3>
        <p>Items with stock ≤ 25</p>
        <button onclick="alert('Low Stock Report - Coming Soon')">View Low Stock</button>
    </div>

    <div class="report-card">
        <h3>Monthly Sales Trend</h3>
        <p>Summary of last 30 days</p>
        <button onclick="alert('Monthly Sales Trend - Coming Soon')">Generate Trend</button>
    </div>

</div>

</body>
</html>