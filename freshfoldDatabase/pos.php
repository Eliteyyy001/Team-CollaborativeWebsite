<?php
//start session
session_start();

//include database connection
include __DIR__ . '/dbconnect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Load categories for filter dropdown
$categories = [];
$dbError = '';
$catResult = $conn->query("SELECT catID, catName FROM `Category` ORDER BY catName");
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
} elseif ($catResult === false) {
    $dbError = 'Category query failed.';
}

// gather all products from database
$products = [];
$sql = "SELECT p.prodID, p.prodName, p.prodCost, p.catID, p.quantityStocked, c.catName 
        FROM `Product` p 
        LEFT JOIN `Category` c ON p.catID = c.catID 
        ORDER BY p.prodName";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} elseif ($result === false) {
    $dbError = $dbError ?: 'Product query failed.';
}

// Load cart items for display
$cartItems = [];
$cartTotal = 0;
if (!empty($_SESSION['cart'])) {
    $idsArray = array_map('intval', array_keys($_SESSION['cart']));
    $ids = implode(",", $idsArray);
    $cartResult = $conn->query("SELECT prodID, prodName, prodCost FROM `Product` WHERE prodID IN ($ids)");
    if ($cartResult) {
        while ($row = $cartResult->fetch_assoc()) {
            $row['quantity'] = (int)$_SESSION['cart'][(int)$row['prodID']];
            $cartTotal += (float)$row['prodCost'] * $row['quantity'];
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
    <title>FreshFold POS - Make Sale</title>
    <link rel="stylesheet" href="pos.css">

</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-brand">FreshFold POS</div>
        <ul class="nav-links">
            <li><a href="index.html">Dashboard</a></li>
            <li><a href="pos.php" class="active">Make Sale</a></li>
            <li><a href="products.html">Products</a></li>
            <li><a href="reports.html">Reports</a></li>
        </ul>
        <div class="nav-user">
            <span>Cashier: Jane Smith</span>
            <a href="cart.php">View Cart</a>
            <button class="btn-exit">Exit</button>
        </div>
    </nav>

    <!-- Main POS Layout -->
    <main class="pos-main">

        <!-- Product List Panel -->
        <section class="panel product-panel">
            <div class="panel-header">
                <h2>Products</h2>
            </div>
            
            <div class="search-bar">
                <input type="text" id="productSearch" placeholder="Search">
            </div>

            <!-- Filter and Sort -->
            <div class="filter-sort-row">
                <div class="filter-group">
                    <label>Filter:</label>
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['catName']) ?>"><?= htmlspecialchars($cat['catName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sort-group">
                    <label>Sort:</label>
                    <select id="sortBy">
                        <option value="price">Price</option>
                        <option value="name">Name</option>
                        <option value="stock">Stock</option>
                    </select>
                </div>
            </div>

            <!-- Product Table -->
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dbError): ?>
                        <tr><td colspan="4"><?= htmlspecialchars($dbError) ?> Run seed_products.sql if needed.</td></tr>
                    <?php elseif (empty($products)): ?>
                        <tr><td colspan="4">No products in database. Add INSERT statements in seed_products.sql.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr data-product-id="<?= (int)$p['prodID'] ?>"
                                data-price="<?= number_format((float)$p['prodCost'], 2, '.', '') ?>"
                                data-stock="<?= (int)($p['quantityStocked'] ?? 0) ?>"
                                data-category="<?= htmlspecialchars($p['catName'] ?? '') ?>"
                                data-name="<?= htmlspecialchars(strtolower($p['prodName'])) ?>">
                                <td><?= htmlspecialchars($p['prodName']) ?></td>
                                <td>$<?= number_format((float)$p['prodCost'], 2) ?></td>
                                <td><?= (int)($p['quantityStocked'] ?? 0) ?></td>
                                <td>
                                    <form method="post" action="cart.php" style="display:inline;">
                                        <input type="hidden" name="add" value="<?= (int)$p['prodID'] ?>">
                                        <input type="hidden" name="qty" value="1">
                                        <input type="hidden" name="from" value="pos">
                                        <button type="submit" class="btn-add">Add</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Cart Panel -->
        <section class="panel cart-panel">
            <div class="panel-header">
                <h2>Make Sale</h2>
            </div>

            <!-- Add a product to the cart -->
            <form method="post" action="cart.php">
                <input type="hidden" name="from" value="pos">
                <div class="form-group">
                    <label>Product:</label>
                    <select id="selectedProduct" name="add">
                        <option value="">Select</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= (int)$p['prodID'] ?>" data-price="<?= number_format((float)$p['prodCost'], 2, '.', '') ?>"><?= htmlspecialchars($p['prodName']) ?> - $<?= number_format((float)$p['prodCost'], 2) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" id="quantity" name="qty" value="1" min="1">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="text" id="price" value="$0.00" readonly>
                </div>
                <p class="helper-text">Helper: "Enter quantity ≥ 1"</p>
                <button type="submit" class="btn-submit">Submit Sale</button>
            </form>

            <!-- Cart items tabl -->
            <div class="cart-items">
                <h3>Cart Items</h3>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody">
                        <?php if (empty($cartItems)): ?>
                            <tr class="cart-empty-row"><td colspan="4">Your cart is empty. Add items above or from the product list.</td></tr>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <tr data-product-id="<?= (int)$item['prodID'] ?>">
                                    <td><?= htmlspecialchars($item['prodName']) ?></td>
                                    <td><?= (int)$item['quantity'] ?></td>
                                    <td>$<?= number_format((float)$item['prodCost'] * (int)$item['quantity'], 2) ?></td>
                                    <td>
                                        <form method="post" action="cart.php" style="display:inline;">
                                            <input type="hidden" name="from" value="pos">
                                            <button type="submit" class="btn-remove" name="remove" value="<?= (int)$item['prodID'] ?>">X</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-total">
                <strong>Total: $<span id="cartTotalDisplay"><?= number_format($cartTotal, 2) ?></span></strong>
            </div>

            <!-- clear cart -->
            <div class="cart-actions">
                <form method="post" action="cart.php" style="display:inline;">
                    <input type="hidden" name="from" value="pos">
                    <button type="submit" name="clear" value="1" class="btn-cancel">Cancel</button>
                </form>
            </div>

            <div class="panel-footer">
                <button class="btn-exit-small">Exit</button>
            </div>
        </section>

        <!-- Receipt Panel -->
        <section class="panel receipt-panel">
            <div class="panel-header">
                <h2>Receipt</h2>
            </div>

            <div class="receipt-box">
                <h3>Receipt</h3>
                <div class="receipt-row">
                    <span>Product:</span>
                    <span id="receiptProduct">—</span>
                </div>
                <div class="receipt-row">
                    <span>Quantity:</span>
                    <span id="receiptQty">—</span>
                </div>
                <div class="receipt-row">
                    <span>Price:</span>
                    <span id="receiptPrice">—</span>
                </div>
                <div class="receipt-row">
                    <span>Total:</span>
                    <span id="receiptTotal">—</span>
                </div>
                <div class="receipt-row">
                    <span>Date:</span>
                    <span id="receiptDate"><?= date('m/d/Y') ?></span>
                </div>

                <div class="receipt-actions">
                    <button type="button" class="btn-print">Print</button>
                    <button type="button" class="btn-download">Download</button>
                </div>

                <a href="index.html" class="back-link">Back to Dashboard</a>
            </div>
        </section>

    </main>

    <script src="pos.js"></script>
</body>
</html>
