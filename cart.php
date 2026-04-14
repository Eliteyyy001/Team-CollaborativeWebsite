<?php
// start session so we can remember the cart
session_start();
// connect database

require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/audit_helpers.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// If there is no cart yet, start with an empty one
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';
$lastSaleID = null;

// --- When the user clicks "Add" (from POS or a form) ---
if (isset($_POST['add'])) {
    $prodID = (int) $_POST['add'];
    $qty = isset($_POST['qty']) ? max(1, (int) $_POST['qty']) : 1;
    if (!isset($_SESSION['cart'][$prodID])) {
        $_SESSION['cart'][$prodID] = 0;
    }
    $_SESSION['cart'][$prodID] += $qty;
    // If they came from the POS page, send them back there so the cart panel updates
    $goBack = (!empty($_POST['from']) && $_POST['from'] === 'pos') ? 'pos.php' : 'cart.php';
    header('Location: ' . $goBack);
    exit;
}

// --- When the user clicks "Remove" on an item ---
if (isset($_POST['remove'])) {
    $prodID = (int) $_POST['remove'];
    if ($prodID > 0) {
        unset($_SESSION['cart'][$prodID]);
    }
    $goBack = (!empty($_POST['from']) && $_POST['from'] === 'pos') ? 'pos.php' : 'cart.php';
    header('Location: ' . $goBack);
    exit;
}

// --- When the user clicks "Clear" or "Cancel" to empty the cart ---
if (isset($_POST['clear'])) {
    $_SESSION['cart'] = [];
    $goBack = (!empty($_POST['from']) && $_POST['from'] === 'pos') ? 'pos.php' : 'cart.php';
    header('Location: ' . $goBack);
    exit;
}

// --- When the user changes quantities and clicks "Update Cart" ---
if (isset($_POST['update']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $prodID => $qty) {
        $prodID = (int) $prodID;
        $qty = max(0, (int) $qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$prodID]);
        } else {
            $_SESSION['cart'][$prodID] = $qty;
        }
    }
    audit_log($conn, current_user_id(), 'UPDATE_CART', 'Cart quantities updated');
    header('Location: cart.php');
    exit;
}

// --- When the user clicks "Checkout" to complete a sale ---
// Delegate the actual sale and inventory logic to checkout_process.php
if (isset($_POST['checkout'])) {
    // Sync any quantity changes from the form into the session cart first
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $prodID => $qty) {
            $prodID = (int) $prodID;
            $qty = max(0, (int) $qty);
            if ($qty <= 0) {
                unset($_SESSION['cart'][$prodID]);
            } else {
                $_SESSION['cart'][$prodID] = $qty;
            }
        }
    }

    // Optional override total (Manager/Admin/Owner only)
    $overrideTotal = isset($_POST['override_total']) ? trim((string)$_POST['override_total']) : '';
    $overrideReason = isset($_POST['override_reason']) ? trim((string)$_POST['override_reason']) : '';
    $_SESSION['override_total'] = $overrideTotal;
    $_SESSION['override_reason'] = $overrideReason;

    header('Location: checkout_process.php');
    exit;
}

// --- Build the list of cart items for the table (get names and prices from database) ---
$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $idsArray = array_map('intval', array_keys($_SESSION['cart']));
    $ids = implode(",", $idsArray);
    $result = $conn->query("SELECT prodID, prodName, prodCost FROM `Product` WHERE prodID IN ($ids)");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = (int) $_SESSION['cart'][(int) $row['prodID']];
            $total += (float) $row['prodCost'] * $row['quantity'];
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

<?php if ($message): ?>
    <p id="message" class="message warning"><?= htmlspecialchars($message) ?></p>
<?php else: ?>
    <p id="message" class="message"></p>
<?php endif; ?>

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
                                  name="qty[<?= (int) $item['prodID'] ?>]"
                                  value="<?= (int) $item['quantity'] ?>"
                                  min="1"
                                  onchange="validateQuantity(this)">
                        </td>
                        <td>$<?= number_format((float) $item['prodCost'] * (int) $item['quantity'], 2) ?></td>
                        <td>
                            <button type="submit"
                                    class="btn-remove"
                                    name="remove"
                                    value="<?= (int) $item['prodID'] ?>">
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

    <div class="sales-section">
        <p class="summary-text">
            <strong>Override (optional):</strong> Only Manager/Admin/Owner can override the sale total.
        </p>
        <div class="form-group">
            <label>Override Total ($):</label>
            <input type="number" step="0.01" min="0" name="override_total" placeholder="Leave blank for no override">
        </div>
        <div class="form-group">
            <label>Override Reason:</label>
            <input type="text" name="override_reason" placeholder="Example: coupon, discount, customer issue">
        </div>
    </div>

    <div class="cart-actions">
        <button type="submit" name="update" class="btn-submit">Update Cart</button>
        <button type="submit" name="checkout" class="btn-cancel" onclick="return validateCheckout();">Checkout</button>
    </div>

    <p><a href="pos.php">← Back to POS</a></p>
</form>

<script src="cart.js"></script>
</body>
</html>
