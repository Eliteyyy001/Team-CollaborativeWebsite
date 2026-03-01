<?php
session_start();
include __DIR__ . '/dbconnect.php';

// access control
$isAdmin         = !empty($_SESSION['admin_logged_in']);
$sessionUserID   = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : null;
$isCashierLogin  = !empty($_SESSION['loggedin']);

if (!$isAdmin && !$sessionUserID && !$isCashierLogin) {
    header('Location: ../login.php');
    exit;
}

// validate sale ID
$saleID = isset($_GET['saleID']) ? (int)$_GET['saleID'] : 0;
if ($saleID <= 0) {
    http_response_code(400);
    die('Invalid receipt ID.');
}

// load sale header
$stmt = $conn->prepare(
    "SELECT s.saleID, s.saleDateTime, s.totalAmount, s.userID, u.userName
     FROM Sale s
     LEFT JOIN Users u ON s.userID = u.userID
     WHERE s.saleID = ?
     LIMIT 1"
);
$stmt->bind_param('i', $saleID);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sale) {
    http_response_code(404);
    die('Receipt not found.');
}

// ownership check
if (!$isAdmin) {
    $saleOwner = (int)$sale['userID'];
    $ownedBySessionUser   = $sessionUserID && $saleOwner === $sessionUserID;
    $ownedByHardcodedUser = $isCashierLogin && !$sessionUserID && $saleOwner === 4;

    if (!$ownedBySessionUser && !$ownedByHardcodedUser) {
        http_response_code(403);
        die('Access denied: you are not authorized to view this receipt.');
    }
}

// load line items
$items = [];
$stmt2 = $conn->prepare(
    "SELECT p.prodName, si.quantity, si.itemPrice
     FROM SaleItem si
     JOIN Product p ON si.prodID = p.prodID
     WHERE si.saleID = ?"
);
$stmt2->bind_param('i', $saleID);
$stmt2->execute();
$result = $stmt2->get_result();
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $sale['saleID']; ?> — FreshFold</title>
    <link rel="stylesheet" href="sale.css">
    <style>
        .receipt {
            max-width: 480px;
            margin: 0 auto;
            background: linear-gradient(135deg, #fdf6e3 0%, #f5e6c8 100%);
            border: 2px solid #d4a574;
            border-radius: 16px;
            padding: 30px 28px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .store-name { text-align: center; font-size: 22px; font-weight: bold; color: #2c3e50; margin-bottom: 4px; }
        .receipt-meta { text-align: center; font-size: 13px; color: #5a5a5a; margin-bottom: 16px; }
        hr { border: none; border-top: 1px dashed #d4a574; margin: 14px 0; }
        .total-row td { font-weight: bold; font-size: 15px; padding-top: 10px; border-top: 1px dashed #d4a574; }
        .actions { text-align: center; margin-top: 24px; }
        .btn-print {
            padding: 8px 24px; background: #6b9e78; color: #fff; border: none;
            border-radius: 4px; cursor: pointer; font-size: 14px;
        }
        .btn-print:hover { background: #5a8a66; }

        @media print {
            body { background: none; padding: 0; }
            .receipt { box-shadow: none; border: none; max-width: 100%; background: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="store-name">FreshFold</div>
    <div class="receipt-meta">
        Receipt #<?php echo (int)$sale['saleID']; ?><br>
        <?php echo htmlspecialchars($sale['saleDateTime']); ?><br>
        Cashier: <?php echo htmlspecialchars($sale['userName'] ?? 'N/A'); ?>
    </div>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['prodName']); ?></td>
                <td><?php echo (int)$item['quantity']; ?></td>
                <td>$<?php echo number_format((float)$item['itemPrice'], 2); ?></td>
                <td>$<?php echo number_format((float)$item['itemPrice'] * (int)$item['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td>$<?php echo number_format((float)$sale['totalAmount'], 2); ?></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <div class="receipt-meta">Thank you for your purchase!</div>

    <div class="actions">
        <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        <a class="back-links" href="sales.php">← Back to Sales</a>
    </div>
</div>
</body>
</html>
