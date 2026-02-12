<?php
//start session 
session_start();
//connect database
include __DIR__ . '/dbconnect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');


function sendCartJson($conn) {
    header('Content-Type: application/json');
    $cartItems = [];
    $cartTotal = 0;
    if (!empty($_SESSION['cart'])) {
        $ids = implode(",", array_map('intval', array_keys($_SESSION['cart'])));
        $result = $conn->query("SELECT prodID, prodName, prodCost FROM `Product` WHERE prodID IN ($ids)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['quantity'] = (int)$_SESSION['cart'][(int)$row['prodID']];
                $row['subtotal'] = (float)$row['prodCost'] * $row['quantity'];
                $cartTotal += $row['subtotal'];
                $cartItems[] = $row;
            }
        }
    }
    echo json_encode(['success' => true, 'items' => $cartItems, 'total' => round($cartTotal, 2)]);
    exit;
}

// Add item to cart
if (isset($_POST['add'])) {
    $prodID = (int)$_POST['add'];
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
    if (!isset($_SESSION['cart'][$prodID])) {
        $_SESSION['cart'][$prodID] = 0;
    }
    $_SESSION['cart'][$prodID] += $qty;
    if ($isAjax) sendCartJson($conn);
    header('Location: cart.php');
    exit;
}

// Remove item from cart
if (isset($_POST['remove'])) {
    $prodID = (int)$_POST['remove'];
    if ($prodID > 0) unset($_SESSION['cart'][$prodID]);
    if ($isAjax) sendCartJson($conn);
    header('Location: cart.php');
    exit;
}

// Clear entire cart
if (isset($_POST['clear'])) {
    $_SESSION['cart'] = [];
    if ($isAjax) sendCartJson($conn);
    header('Location: cart.php');
    exit;
}

// Update quantity
if (isset($_POST['update']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $prodID => $qty) {
        $prodID = (int)$prodID;
        $qty = max(0, (int)$qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$prodID]);
        } else {
            $_SESSION['cart'][$prodID] = $qty;
        }
    }
    header('Location: cart.php');
    exit;
}

// Checkout 
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $message = 'Your cart is empty. Add items before checking out.';
    } else {
        
    }
}

// Build cart items for display
$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $idsArray = array_map('intval', array_keys($_SESSION['cart']));
    $ids = implode(",", $idsArray);
    $result = $conn->query("SELECT prodID, prodName, prodCost FROM `Product` WHERE prodID IN ($ids)");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = (int)$_SESSION['cart'][(int)$row['prodID']];
            $total += (float)$row['prodCost'] * $row['quantity'];
            $cartItems[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cart.css">
    <title>FreshFold Cart</title>
</head>
<body>

<h1>FreshFold Cart</h1>
<h2>Cart</h2>

<p id="message" class="message<?= $message ? ' warning' : '' ?>"><?= htmlspecialchars($message) ?></p>

<form method="post" action="cart.php">

    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody id="cartBody">
            <?php if (empty($cartItems)): ?>
                <tr>
                    <td colspan="4">Your cart is empty. <a href="pos.php">Add items in POS</a></td>
                </tr>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['prodName']) ?></td>
                        <td>
                            <input type="number"
                                  name="qty[<?= (int)$item['prodID'] ?>]"
                                  value="<?= (int)$item['quantity'] ?>"
                                  min="1"
                                  onchange="validateQuantity(this)">
                        </td>
                        <td>$<?= number_format((float)$item['prodCost'] * (int)$item['quantity'], 2) ?></td>
                        <td>
                            <button type="submit"
                                    class="btn-remove"
                                    name="remove"
                                    value="<?= (int)$item['prodID'] ?>">
                                Remove
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="cart-total">
        <strong>Total: $<?= number_format($total, 2) ?></strong>
    </div>

    <div class="cart-actions">
        <button type="submit" name="update" class="btn-submit"><a href="pos.php">Update Cart</a></button>
        <button type="submit" name="checkout" class="btn-cancel" onclick="return validateCheckout();">Checkout</button>
    </div>

    <p><a href="pos.php">← Back to POS</a></p>
</form>

<script src="cart.js"></script>
</body>
</html>
