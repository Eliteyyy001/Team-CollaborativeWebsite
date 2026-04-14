<?php
// this script gives a list of all sales made and their details

// connect database
session_start();

require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/audit_helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['roleName'] !== 'Administrator') {
    header("Location: admin-login.php");
    exit();
}

// Load all sales with a simple item count
$sales = [];
$result = $conn->query("
    SELECT s.saleID,
           s.saleDateTime,
           s.totalAmount,
           s.userID,
           COUNT(si.saleItemID) AS itemCount
    FROM Sale s
    LEFT JOIN SaleItem si ON s.saleID = si.saleID
    GROUP BY s.saleID, s.saleDateTime, s.totalAmount, s.userID
    ORDER BY s.saleDateTime DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
}

// Get total of all sales
$totalsResult = $conn->query("SELECT SUM(totalAmount) AS grandTotal FROM Sale");
$grandTotal = 0;
if ($totalsResult && $totalsResult->num_rows > 0) {
    $totalsRow = $totalsResult->fetch_assoc();
    $grandTotal = (float) ($totalsRow['grandTotal'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Freshfold</title>
    <link rel="stylesheet" href="pos.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">Freshfold Admin</div>
        <ul class="nav-links">
        <li><a href="admin-dashboard.php">Users</a></li>
        <li><a href="admin-alerts.php">Alerts</a></li>
        <li><a href="display_charts.php">Charts</a></li>
        <li><a href="top_selling_report.php" >Reports</a></li>
        <li><a href="sales.php"class="active">Sales</a></li>
        <li><a href="audit_logs.php" >Audit Logs</a></li>
        </ul>
        <div class="nav-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin-logout.php" class="btn-exit">Logout</a>
        </div>
    </nav>


<table class="cart-table">
    <thead>
        <tr>
            <th>Sale ID</th>
            <th>Date / Time</th>
            <th>Total Amount</th>
            <th>Items</th>
            <th>Cashier (userID)</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($sales)): ?>
            <tr>
                <td colspan="6">No sales have been recorded yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo (int) $sale['saleID']; ?></td>
                    <td><?php echo htmlspecialchars($sale['saleDateTime']); ?></td>
                    <td>$<?php echo number_format((float) $sale['totalAmount'], 2); ?></td>
                    <td><?php echo (int) $sale['itemCount']; ?></td>
                    <td><?php echo htmlspecialchars((string) $sale['userID']); ?></td>
                    <td><a href="sale_detail.php?saleID=<?php echo (int) $sale['saleID']; ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<p><strong>Total of all sales:</strong> $<?php echo number_format($grandTotal, 2); ?></p>
<p>
    <a href="admin-dashboard.php">← Back to Dashboard</a>
    | <a href="logout.php">Logout</a>
</p>


</body>
</html>

