<?php

//this script gives a list of all sales made and their details

//connect database
include __DIR__ . '/dbconnect.php';

// Load all sales
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
    <title>FreshFold - Sales</title>
    <link rel="stylesheet" href="sale.css">
</head>
<body>

<h1>Sales History</h1>

<p><a href="pos.php">← Back to POS</a></p>

<p><strong>Total of all sales:</strong> $<?php echo number_format($grandTotal, 2); ?></p>

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

</body>
</html>

