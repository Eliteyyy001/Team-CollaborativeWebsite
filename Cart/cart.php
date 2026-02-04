<?php

//start session
session_start();
include "freshfoldDatabase/dbconnect.php";

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

$message = ""; //created for warning message

//add items into cart
if (isset($_POST['add'])) {
  $prodID = (int)$_POST['add'];

  if (isset($_SESSION['cart'][$prodID])) {
    $_SESSION['cart'][$prodID]++;
  } else {
    $_SESSION['cart'][$prodID] = 1;
  }
}

//remove cart item
if (isset($_POST['remove'])) {
  $prodID = (int)$_POST['remove'];
  unset($_SESSION['cart'][$prodID]);
}

//update quantities
if (isset($_POST['update']) && isset($_POST['qty'])) {
  foreach ($_POST['qty'] as $id => $qty) {
    $prodID = (int)$id;
    $quantity = (int)$qty;

    if ($quantity <= 0) $quantity = 1; // quantity > 0 
    $_SESSION['cart'][$prodID] = $quantity;
  }
}

//checkout,clear cart and display empty cart warning if it is empty
if (isset($_POST['checkout'])) {
  if (empty($_SESSION['cart'])) {
    $message = "Your cart is empty. Add items before checking out.";
  } else {
    $_SESSION['cart'] = [];
    $message = "Checkout complete (cart cleared).";
  }
}

//display cart items in cart
$cartItems = [];

if (!empty($_SESSION['cart'])) {
  $idsArray = array_map('intval', array_keys($_SESSION['cart']));
  $ids = implode(",", $idsArray);

  $result = $conn->query("SELECT prodID, prodName, prodCost FROM Product WHERE prodID IN ($ids)");

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $row['quantity'] = $_SESSION['cart'][(int)$row['prodID']];
      $cartItems[] = $row;
    }
  }
}
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="cart.css">
  <title>Freshfold Cart</title>
</head>
<body>

<h1>FreshFold Cart</h1>

<p id="message" class="warning"><?php echo htmlspecialchars($message); ?></p>

<form method="post" action="cart.php">

  <!-- create cart table -->
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
          <td colspan="4">Your cart is empty.</td>
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

            <td>
              <?= number_format(((float)$item['prodCost']) * ((int)$item['quantity']), 2) ?>
            </td>

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
  
  <!--update cart button-->
  
  <button type="submit" name="update">Update Cart</button>
  
  <!--checkout button -->
  
  <button type="submit" name="checkout" onclick="return validateCheckout();">Checkout</button>
</form>

<script src="cart.js"></script>
</body>
</html>
