<?php
// load products

// include db connections
include "freshfoldDatabase/dbconnect.php";

// gather all products from database
$products = [];
$sql = "SELECT prodID, prodName, catID, prodCost, quantityStocked, prodDiscount FROM Product";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
            <li><a href="pos.html" class="active">Make Sale</a></li>
            <li><a href="products.html">Products</a></li>
            <li><a href="reports.html">Reports</a></li>
        </ul>
        <div class="nav-user">
            <span>Cashier: Jane Smith</span>
            
            <!--link to cart page-->
            <a href="cart.php" class="cart-link">View Cart</a>

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

            <!--search products by name, search bar-->
            <div class="search-bar">
                <input type="text" id="productSearch" placeholder="Search">
            </div>

            <!-- Filter and Sort -->
            <div class="filter-sort-row">
                <div class="filter-group">
                    <label>Filter:</label>
                    <select>
                        <option>Category</option>
                        <option>Clothing</option>
                        <option>Accessories</option>
                        <option>Footwear</option>
                    </select>
                </div>
                <div class="sort-group">
                    <label>Sort:</label>
                    <select>
                        <option>Price</option>
                        <option>Name</option>
                        <option>Stock</option>
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
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr
                                data-product-id="<?= (int)$product['prodID'] ?>"
                                data-price="<?= number_format((float)$product['prodCost'], 2, '.', '') ?>"
                                data-stock="<?= (int)$product['prodStock'] ?>"
                            >
                                <td><?= htmlspecialchars($product['prodName']) ?></td>
                                <td>$<?= number_format((float)$product['prodCost'], 2) ?></td>
                                <td><?= (int)$product['prodStock'] ?></td>
                                <td>
                                    <!-- Add one item to the cart -->
                                    <button
                                        type="button"
                                        class="btn-add"
                                        data-product-id="<?= (int)$product['prodID'] ?>"
                                    >
                                        Add
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No products found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Cart Panel -->
       <section class="panel cart-panel">
            <div class="panel-header">
                <h2>Make Sale</h2>
            </div>

            <!-- 1. Choose product -->
            <div class="form-group">
                <label for="selectedProduct">Product:</label>
                <select id="selectedProduct">
                    <option value="">Select a product</option>

                    <!-- Fill dropdown box with a product from database -->
                    <?php foreach ($products as $product): ?>
                        <option
                            value="<?= (int)$product['prodID'] ?>"
                            data-price="<?= number_format((float)$product['prodCost'], 2, '.', '') ?>"
                        >
                            <?= htmlspecialchars($product['prodName']) ?> - $<?= number_format((float)$product['prodCost'], 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Choose a produt quantity -->
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" value="1" min="1">
            </div>

            <!-- 3. Show total price for this item and quantity -->
            <div class="form-group">
                <label for="price">Total Price for this item:</label>
                <input type="text" id="price" value="$0.00" readonly>
            </div>

            
            <!-- Buttons -->
            <div class="cart-actions">
                <!-- add chosen product and add to cart as well-->
                <button class="btn-submit" type="button">Submit Sale (Add to Cart)</button>
                <button class="btn-cancel" type="button">Cancel</button>
            </div>

            <div class="panel-footer">
                <button class="btn-exit-small" type="button">Exit</button>
            </div>
        </section>
            <!-- Product Selection -->
            <div class="form-group">
                <label>Product:</label>
                <select id="selectedProduct">
                    <option>Select</option>
                    <option> FlipFold - $12.99 </option>
                    <option>Wool Dryer Balls - $7.99</option>
                    <option>Tide Dryer Sheets - $4.99</option>
                    <option>Downey Fabric Softner - $7.99</option>
                    <option>Box Legend Folding Board - $11.99</option>
                    <option>Tide Stain Fighting Detergent - $6.99</option>
                    <option>Downey Sensitive Detergen - $8.99</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity:</label>
                <input type="number" id="quantity" value="1" min="1">
            </div>

            <div class="form-group">
                <label>Price:</label>
                <input type="text" id="price" value="$0.00" readonly>
            </div>

            <!-- Cart Items Table -->
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
                    <tbody>
                        <tr>
                            <td>Downey Fabric Softner</td>
                            <td>2</td>
                            <td>$15.98</td>
                            <td><button class="btn-remove">X</button></td>
                        </tr>
                        <tr>
                            <td>Tide Dryer Sheets</td>
                            <td>1</td>
                            <td>$9.98</td>
                            <td><button class="btn-remove">X</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="cart-total">
                <strong>Total: $25.96</strong>
            </div>

            <!-- Checkout Button -->
            <div class="cart-actions">
                <button class="btn-submit">Submit Sale</button>
                <button class="btn-cancel">Cancel</button>
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
                    <span>SW Leather Belt</span>
                </div>
                <div class="receipt-row">
                    <span>Quantity:</span>
                    <span>2</span>
                </div>
                <div class="receipt-row">
                    <span>Price:</span>
                    <span>$25.00</span>
                </div>
                <div class="receipt-row">
                    <span>Total:</span>
                    <span>$50.00</span>
                </div>
                <div class="receipt-row">
                    <span>Date:</span>
                    <span>10/30/2025</span>
                </div>

                <div class="receipt-actions">
                    <button class="btn-print">Print</button>
                    <button class="btn-download">Download</button>
                </div>

                <a href="index.html" class="back-link">Back to Dashboard</a>
            </div>
        </section>

    </main>

</body>
</html>
