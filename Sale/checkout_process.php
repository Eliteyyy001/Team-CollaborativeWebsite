<?php
// This script completes a checkout using the existing Session cart

//start session
session_start();

//include database connection file 
include __DIR__ . '/dbconnect.php';


$userID = isset($_SESSION['userID']) ? (int) $_SESSION['userID'] : 4;

if (empty($_SESSION['cart'])) {
    $message = 'Your cart is empty. Add items before checking out.';
} else {
    $message = '';

 
    $checkoutItems = [];
    $cartTotal = 0;

    $idsArray = array_map('intval', array_keys($_SESSION['cart']));
    $ids = implode(",", $idsArray);
    $result = $conn->query("SELECT prodID, prodName, prodCost, quantityStocked FROM Product WHERE prodID IN ($ids)");

    $checkoutItems = [];
    $cartTotal = 0;

    $idsArray = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
    $stmt = $conn->prepare("SELECT prodID, prodName, prodCost, quantityStocked FROM Product WHERE prodID IN ($placeholders)");
    if ($stmt) {
        $types = str_repeat('i', count($idsArray));
        $stmt->bind_param($types, ...$idsArray);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = false;
    }

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $prodID = (int) $row['prodID'];
            $quantity = (int) $_SESSION['cart'][$prodID];
            $unitPrice = (float) $row['prodCost'];
            $row['quantity'] = $quantity;
            $row['unitPrice'] = $unitPrice;
            $row['subtotal'] = $unitPrice * $quantity;
            $cartTotal += $row['subtotal'];
            $checkoutItems[] = $row;
        }
    }

    if (empty($checkoutItems) || $cartTotal <= 0) {
        $message = 'Could not load cart items or total is 0. Try again.';
    } else {
        $conn->begin_transaction();
        $ok = true;

        // create the sale header 
         $saleStmt = $conn->prepare("INSERT INTO Sale (userID, saleDateTime, totalAmount) VALUES (?, NOW(), ?)");
        if (!$saleStmt || !$saleStmt->bind_param("id", $userID, $cartTotal) || !$saleStmt->execute()) {
            $ok = false;
        } else {
            $saleID = $conn->insert_id;
            $saleStmt->close();

            // For each cart item, add SaleItem, update Product stock, and log InventoryMovement
            foreach ($checkoutItems as $item) {
                $prodID = (int) $item['prodID'];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unitPrice'];

                // Check stock count
                $stockStmt = $conn->prepare("SELECT quantityStocked FROM Product WHERE prodID = ? FOR UPDATE");
                $stockResult = false;
                if ($stockStmt && $stockStmt->bind_param("i", $prodID) && $stockStmt->execute()) {
                    $stockResult = $stockStmt->get_result();
                }
                if (!$stockResult || $stockResult->num_rows === 0) {
                    $ok = false;
                    $message = 'Product not found while checking stock.';
                    break;
                }
                $stockRow = $stockResult->fetch_assoc();
                if ($stockStmt) {
                    $stockStmt->close();
                }
                $currentStock = (int) $stockRow['quantityStocked'];
                if ($currentStock < $quantity) {
                    $ok = false;
                    $message = 'Not enough stock for ' . htmlspecialchars($item['prodName']) . '.';
                    break;
                }


                // a) Insert into SaleItem 
                $saleItemStmt = $conn->prepare("INSERT INTO SaleItem (saleID, prodID, quantity, itemPrice) VALUES (?, ?, ?, ?)");
                if (!$saleItemStmt || !$saleItemStmt->bind_param("iiid", $saleID, $prodID, $quantity, $unitPrice) || !$saleItemStmt->execute()) {
                    $ok = false;
                    break;
                }
                $saleItemStmt->close();

                // b) Deduct inventory from Product
                $updateStmt = $conn->prepare("UPDATE Product SET quantityStocked = quantityStocked - ? WHERE prodID = ?");
                if (!$updateStmt || !$updateStmt->bind_param("ii", $quantity, $prodID) || !$updateStmt->execute()) {
                    $ok = false;
                    break;
                }
                $updateStmt->close();

                //  Log inventory movement using the InventoryMovement table
             
                 $qtyChange = -$quantity;
                $movementStmt = $conn->prepare("INSERT INTO InventoryMovement (prodID, transType, transID, quantityChange, unitCost, movedAt, movedBy, prodActivityStatus) VALUES (?, 'Sale', ?, ?, ?, NOW(), ?, TRUE)");
                if (!$movementStmt || !$movementStmt->bind_param("iiidi", $prodID, $saleID, $qtyChange, $unitPrice, $userID) || !$movementStmt->execute()) {
                    $ok = false;
                    break;
                }
                $movementStmt->close();
            }
        }

        if ($ok) {
            $conn->commit();
            // clear cart
            $_SESSION['cart'] = [];
            header('Location: receipt.php?saleID=' . $saleID);
            exit;
        } else {
            $conn->rollback();
            if (!$message) {
                $message = 'There was a problem saving the sale. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Result</title>
    <link rel="stylesheet" href="sale.css">
</head>
<body>

<h1>Checkout Result</h1>

<div class="sales-section">
    <p class="summary-text message"><?php echo htmlspecialchars($message); ?></p>
</div>

<div class="back-links">
    <a href="pos.php">← Back to POS</a>
    <a href="sales.php">View Sales History</a>
</div>

</body>
</html>

