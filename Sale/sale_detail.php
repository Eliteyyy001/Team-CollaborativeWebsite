<?php

//this script allows users to click onto a sale detail from the sales.php page and view the details of a single sale
include __DIR__ . '/dbconnect.php';

$saleID = isset($_GET['saleID']) ? (int) $_GET['saleID'] : 0;

// Load the sale header
$sale = null;
if ($saleID > 0) {
    $result = $conn->query("
        SELECT saleID, saleDateTime, totalAmount, userID
        FROM Sale
        WHERE saleID = " . $saleID . "
        LIMIT 1
    ");
    if ($result && $result->num_rows > 0) {
        $sale = $result->fetch_assoc();
    }
}

// Load the sale items
$items = [];
if ($saleID > 0) {
    $itemsResult = $conn->query("
        SELECT si.quantity,
               si.itemPrice,
               p.prodName
        FROM SaleItem si
        JOIN Product p ON si.prodID = p.prodID
        WHERE si.saleID = " . $saleID . "
    ");
    if ($itemsResult) {
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold - Sale Detail</title>
    <link rel="stylesheet" href="sale.css">
</head>
<body>

<h1>Sale Detail</h1>

<p><a href="sales.php">← Back to Sales</a></p>

<?php if (!$sale): ?>
    <p>Sale not found.</p>
<?php else: ?>
    <h2>Sale #<?php echo (int) $sale['saleID']; ?></h2>
    <p><strong>Date / Time:</strong> <?php echo htmlspecialchars($sale['saleDateTime']); ?></p>
    <p><strong>Total Amount:</strong> $<?php echo number_format((float) $sale['totalAmount'], 2); ?></p>
    <p><strong>Cashier (userID):</strong> <?php echo htmlspecialchars((string) $sale['userID']); ?></p>

    <h3>Line Items</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Item Price</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="4">No line items found for this sale.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['prodName']); ?></td>
                        <td><?php echo (int) $item['quantity']; ?></td>
                        <td>$<?php echo number_format((float) $item['itemPrice'], 2); ?></td>
                        <td>$<?php echo number_format((float) $item['itemPrice'] * (int) $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>

