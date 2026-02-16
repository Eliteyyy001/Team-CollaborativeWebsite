<?php include 'dbconnect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        .card {
            width: 250px;
            padding: 20px;
            margin: 10px;
            background: #f4f4f4;
            border-radius: 10px;
            display: inline-block;
            text-align: center;
            font-size: 20px;
        }
        .low { color: red; }
        .ok { color: green; }
    </style>
</head>
<body>

<h2>Dashboard Overview</h2>

<?php
// TOTAL PRODUCTS
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM inventory")->fetch_assoc()['total'];

// LOW STOCK (<10)
$lowStock = $conn->query("SELECT COUNT(*) AS low FROM inventory WHERE quantity < 10 AND quantity > 0")->fetch_assoc()['low'];

// OUT OF STOCK (=0)
$outStock = $conn->query("SELECT COUNT(*) AS outS FROM inventory WHERE quantity = 0")->fetch_assoc()['outS'];

// TOTAL INVENTORY VALUE 
$valueQuery = $conn->query("SELECT SUM(quantity * price) AS totalValue FROM inventory");
$totalValue = $valueQuery->fetch_assoc()['totalValue'] ?? 0;
?>

<div class="card">
    <strong>Total Products</strong><br>
    <?php echo $totalProducts; ?>
</div>

<div class="card low">
    <strong>Low Stock Items</strong><br>
    <?php echo $lowStock; ?>
</div>

<div class="card low">
    <strong>Out of Stock</strong><br>
    <?php echo $outStock; ?>
</div>

<div class="card ok">
    <strong>Total Inventory Value</strong><br>
    $<?php echo number_format($totalValue, 2); ?>
</div>

</body>
</html>
