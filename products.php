<?php
session_start();
require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';

if (!isset($_SESSION['userID']) || !isset($_SESSION['userName'])) {
    header("Location: index.php");
    exit;
}

// 12 Products (same as pos.php)
$products = [
    ['id' => 1,  'name' => 'Box Legend Folding Board',      'price' => 11.99, 'stock' => 10],
    ['id' => 2,  'name' => 'Downey Fabric Softner',         'price' => 7.99,  'stock' => 75],
    ['id' => 3,  'name' => 'Downey Sensitive Detergent',    'price' => 8.89,  'stock' => 43],
    ['id' => 4,  'name' => 'FlipFold',                      'price' => 12.99, 'stock' => 8],
    ['id' => 5,  'name' => 'Tide Dryer Sheets',             'price' => 4.99,  'stock' => 246],
    ['id' => 6,  'name' => 'Tide Stain Fighting Detergent', 'price' => 6.69,  'stock' => 51],
    ['id' => 7,  'name' => 'Wool Dryer Balls',              'price' => 7.99,  'stock' => 108],
    ['id' => 8,  'name' => 'Ironing Board',                 'price' => 29.99, 'stock' => 9],
    ['id' => 9,  'name' => 'Clothes Hangers Pack',          'price' => 8.49,  'stock' => 120],
    ['id' => 10, 'name' => 'Laundry Detergent Pods',        'price' => 14.99, 'stock' => 6],
    ['id' => 11, 'name' => 'Fabric Spray',                  'price' => 6.29,  'stock' => 35],
    ['id' => 12, 'name' => 'Shoe Rack',                     'price' => 19.99, 'stock' => 8]
];

function getStatus($stock) {
    if ($stock <= 25) return ['LOW STOCK', '#e67e22', '#fff6e9'];
    return ['IN STOCK', '#27ae60', '#e9fff3'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - FreshFold POS</title>
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
            max-width: 1200px;
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
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <div>FreshFold POS</div>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Make Sale</a>
        <a href="products.php" class="active">Products</a>
        <a href="reports.php">Reports</a>
        <a href="sales.php">Sales</a>
        <a href="audit_logs.php">Audit Logs</a>
    </div>
    <div>
        Cashier: <strong><?= htmlspecialchars($_SESSION['userName']) ?></strong> 
        | <a href="logout.php" style="color:#ff9999;">Logout</a>
    </div>
</div>

<div class="panel">
    <h2>All Products</h2>
    
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <?php 
                $displayStock = (int)$p['stock'];
                [$statusText, $statusColor, $statusBg] = getStatus($displayStock); 
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= $displayStock ?></td>
                    <td>
                        <span class="status-badge" style="color:<?= $statusColor ?>;background:<?= $statusBg ?>;">
                            <?= $statusText ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>