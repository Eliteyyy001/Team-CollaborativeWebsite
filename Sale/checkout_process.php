<?php
// This script completes a checkout using the  Session cart

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
        $saleSql = "INSERT INTO Sale (userID, saleDateTime, totalAmount)
                    VALUES (" . $userID . ", NOW(), " . $cartTotal . ")";
        if (!$conn->query($saleSql)) {
            $ok = false;
        } else {
            $saleID = $conn->insert_id;

            // For each cart item, add SaleItem, update Product stock, and log InventoryMovement
            foreach ($checkoutItems as $item) {
                $prodID = (int) $item['prodID'];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unitPrice'];

                // Check stock count
                $stockResult = $conn->query("SELECT quantityStocked FROM Product WHERE prodID = " . $prodID . " FOR UPDATE");
                if (!$stockResult || $stockResult->num_rows === 0) {
                    $ok = false;
                    $message = 'Product not found while checking stock.';
                    break;
                }
                $stockRow = $stockResult->fetch_assoc();
                $currentStock = (int) $stockRow['quantityStocked'];
                if ($currentStock < $quantity) {
                    $ok = false;
                    $message = 'Not enough stock for ' . htmlspecialchars($item['prodName']) . '.';
                    break;
                }

                // a) Insert into SaleItem 
                $saleItemSql = "INSERT INTO SaleItem (saleID, prodID, quantity, itemPrice)
                                VALUES (" . $saleID . ", " . $prodID . ", " . $quantity . ", " . $unitPrice . ")";
                if (!$conn->query($saleItemSql)) {
                    $ok = false;
                    break;
                }

                // b) Deduct inventory from Product
                $updateSql = "UPDATE Product
                              SET quantityStocked = quantityStocked - " . $quantity . "
                              WHERE prodID = " . $prodID;
                if (!$conn->query($updateSql)) {
                    $ok = false;
                    break;
                }

                // c) Log inventory movement using the InventoryMovement table
             
                $movementSql = "INSERT INTO InventoryMovement
                                (prodID, transType, transID, quantityChange, unitCost, movedAt, movedBy, prodActivityStatus)
                                VALUES (" . $prodID . ", 'Sale', " . $saleID . ", -" . $quantity . ", " . $unitPrice . ", NOW(), " . $userID . ", TRUE)";
                if (!$conn->query($movementSql)) {
                    $ok = false;
                    break;
                }
            }
        }

        if ($ok) {
            $conn->commit();
            // Clear cart after successful sale
            $_SESSION['cart'] = [];
            $message = 'Sale completed successfully. Sale ID: ' . $saleID;
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

<p class="message"><?php echo htmlspecialchars($message); ?></p>

<p><a href="pos.php">← Back to POS</a></p>
<p><a href="sales.php">View Sales History</a></p>

</body>
</html>
