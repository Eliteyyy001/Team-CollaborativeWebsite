<?php
//this page will display all products and allow and admin role to edit and add new products into inventory list
session_start();

require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/audit_helpers.php';

//validate if admin role is logged in 
$isAdminDashboardSession = isset($_SESSION['admin_logged_in'], $_SESSION['roleName'])
    && $_SESSION['admin_logged_in'] === true
    && $_SESSION['roleName'] === 'Administrator';

$isPosAdminSession = isset($_SESSION['userID']) && (int)($_SESSION['roleID'] ?? 0) === 1;

if (!$isAdminDashboardSession && !$isPosAdminSession) {
    if (isset($_SESSION['userID'])) {
        header('Location: pos.php');
    } else {
        header('Location: login.php');
    }
    exit;
}

$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';

    if ($action === 'add' || $action === 'update') {
        $prodName = isset($_POST['prod_name']) ? trim((string)$_POST['prod_name']) : '';
        $catID = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
        $prodCostRaw = isset($_POST['prod_cost']) ? trim((string)$_POST['prod_cost']) : '';
        $quantityStockedRaw = isset($_POST['quantity_stocked']) ? trim((string)$_POST['quantity_stocked']) : '';
        $prodDiscountRaw = isset($_POST['prod_discount']) ? trim((string)$_POST['prod_discount']) : '0';

        if ($prodName === '') {
            $error = 'Product name is required.';
        } elseif ($catID <= 0) {
            $error = 'Please select a category.';
        } elseif (!is_numeric($prodCostRaw) || (float)$prodCostRaw < 0) {
            $error = 'Product cost must be a valid number.';
        } elseif (!preg_match('/^-?\d+$/', $quantityStockedRaw)) {
            $error = 'Stock quantity must be a whole number.';
        } elseif (!is_numeric($prodDiscountRaw) || (float)$prodDiscountRaw < 0) {
            $error = 'Discount must be a valid number.';
        } else {
            $prodCost = (float)$prodCostRaw;
            $quantityStocked = (int)$quantityStockedRaw;
            $prodDiscount = (float)$prodDiscountRaw;

            if ($action === 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO Product (catID, prodName, prodCost, quantityStocked, prodDiscount)
                     VALUES (?, ?, ?, ?, ?)"
                );
                if ($stmt) {
                    $stmt->bind_param('isdid', $catID, $prodName, $prodCost, $quantityStocked, $prodDiscount);
                    if ($stmt->execute()) {
                        $newProdId = (int)$stmt->insert_id;
                        $notice = 'Product added successfully.';
                        audit_log($conn, (int)($_SESSION['userID'] ?? 0), 'PRODUCT_ADD', 'Product #' . $newProdId . ' ' . $prodName);
                    } else {
                        $error = 'Failed to add product.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Unable to prepare add statement.';
                }
            } else {
                $prodID = isset($_POST['prod_id']) ? (int)$_POST['prod_id'] : 0;
                if ($prodID <= 0) {
                    $error = 'Invalid product selected for update.';
                } else {
                    $stmt = $conn->prepare(
                        "UPDATE Product
                         SET catID = ?, prodName = ?, prodCost = ?, quantityStocked = ?, prodDiscount = ?
                         WHERE prodID = ?"
                    );
                    if ($stmt) {
                        $stmt->bind_param('isdidi', $catID, $prodName, $prodCost, $quantityStocked, $prodDiscount, $prodID);
                        if ($stmt->execute()) {
                            $notice = 'Product updated successfully.';
                            audit_log($conn, (int)($_SESSION['userID'] ?? 0), 'PRODUCT_UPDATE', 'Product #' . $prodID . ' ' . $prodName);
                        } else {
                            $error = 'Failed to update product.';
                        }
                        $stmt->close();
                    } else {
                        $error = 'Unable to prepare update statement.';
                    }
                }
            }
        }
    } elseif ($action === 'delete') {
        $prodID = isset($_POST['prod_id']) ? (int)$_POST['prod_id'] : 0;
        if ($prodID <= 0) {
            $error = 'Invalid product selected for deletion.';
        } else {
            $nameForAudit = '';
            $lookup = $conn->prepare("SELECT prodName FROM Product WHERE prodID = ? LIMIT 1");
            if ($lookup) {
                $lookup->bind_param('i', $prodID);
                $lookup->execute();
                $lookupResult = $lookup->get_result();
                if ($lookupResult) {
                    $existing = $lookupResult->fetch_assoc();
                    if ($existing) {
                        $nameForAudit = (string)$existing['prodName'];
                    }
                }
                $lookup->close();
            }

            $stmt = $conn->prepare("DELETE FROM Product WHERE prodID = ?");
            if ($stmt) {
                $stmt->bind_param('i', $prodID);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $notice = 'Product removed successfully.';
                        audit_log($conn, (int)($_SESSION['userID'] ?? 0), 'PRODUCT_DELETE', 'Product #' . $prodID . ' ' . $nameForAudit);
                    } else {
                        $error = 'Product not found.';
                    }
                } else {
                    $error = 'Failed to remove product. It may be referenced by sales records.';
                }
                $stmt->close();
            } else {
                $error = 'Unable to prepare delete statement.';
            }
        }
    }
}

$categories = [];
$catRes = $conn->query("SELECT catID, catName FROM Category ORDER BY catName");
if ($catRes) {
    while ($row = $catRes->fetch_assoc()) {
        $categories[] = $row;
    }
}

$products = [];
$prodRes = $conn->query(
    "SELECT p.prodID, p.prodName, p.prodCost, p.quantityStocked, p.prodDiscount, p.catID, c.catName
     FROM Product p
     LEFT JOIN Category c ON c.catID = p.catID
     ORDER BY p.prodName ASC"
);
if ($prodRes) {
    while ($row = $prodRes->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold - Product Inventory</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .products-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
        .products-helper { font-size: 13px; color: #5a5a5a; margin-bottom: 12px; }
        .notice { background: #e8f4e8; color: #2f6b3b; padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .error { background: #fde9e9; color: #a93226; padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .btn-secondary { background-color: #7f8c8d; color: #fff; border: none; padding: 10px 14px; border-radius: 6px; cursor: pointer; }
        .btn-secondary:hover { background-color: #6c7778; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .actions form { margin: 0; }
        .actions .btn-add, .actions .btn-remove { padding: 6px 10px; font-size: 12px; }
        @media (max-width: 1000px) { .products-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">FreshFold Admin</div>
    <ul class="nav-links">
        <li><a href="admin-dashboard.php">Users</a></li>
		<li><a href="products.php"class="active">Products</a></li>
        <li><a href="admin-alerts.php">Alerts</a></li>
        <li><a href="display_charts.php">Charts</a></li>
        <li><a href="top_selling_report.php" >Reports</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="audit_logs.php" >Audit Logs</a></li>
    </ul>
    <div class="nav-user">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? ($_SESSION['userName'] ?? '')); ?></span>
        <?php if ($isAdminDashboardSession): ?>
            <a href="admin-logout.php" class="btn-exit">Logout</a>
        <?php else: ?>
            <form method="post" action="logout.php" class="inline-form">
                <button type="submit" class="btn-exit">Logout</button>
            </form>
        <?php endif; ?>
    </div>
</nav>

<main class="pos-main admin-metrics">
    <?php if ($notice !== ''): ?>
        <p class="notice"><?php echo htmlspecialchars($notice); ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="products-layout">
        <section class="panel">
            <div class="panel-header"><h2 id="productFormHeading">Add Product</h2></div>
            <p class="products-helper">Admins can create, edit, and remove items from inventory.</p>
            <form method="post" action="products.php" id="productForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="prod_id" id="prodID" value="">

                <div class="form-group">
                    <label for="prodName">Product Name</label>
                    <input type="text" name="prod_name" id="prodName" required>
                </div>
                <div class="form-group">
                    <label for="catID">Category</label>
                    <select name="cat_id" id="catID" required>
                        <option value="">Select category</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo (int)$c['catID']; ?>"><?php echo htmlspecialchars($c['catName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="prodCost">Cost</label>
                    <input type="number" name="prod_cost" id="prodCost" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="quantityStocked">Stock Quantity</label>
                    <input type="number" name="quantity_stocked" id="quantityStocked" step="1" required>
                </div>
                <div class="form-group">
                    <label for="prodDiscount">Discount</label>
                    <input type="number" name="prod_discount" id="prodDiscount" min="0" step="0.01" value="0">
                </div>
                <div class="actions">
                    <button type="submit" class="btn-submit" id="formSubmitButton">Add Product</button>
                    <button type="button" class="btn-secondary" id="cancelEditButton" style="display:none;">Cancel Edit</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header"><h2>Inventory Product List</h2></div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Stock</th>
                        <th>Discount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="7">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?php echo (int)$p['prodID']; ?></td>
                                <td><?php echo htmlspecialchars($p['prodName']); ?></td>
                                <td><?php echo htmlspecialchars($p['catName'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo number_format((float)$p['prodCost'], 2); ?></td>
                                <td><?php echo (int)$p['quantityStocked']; ?></td>
                                <td>$<?php echo number_format((float)$p['prodDiscount'], 2); ?></td>
                                <td>
                                    <div class="actions">
                                        <button
                                            type="button"
                                            class="btn-add edit-product-btn"
                                            data-prod-id="<?php echo (int)$p['prodID']; ?>"
                                            data-prod-name="<?php echo htmlspecialchars($p['prodName'], ENT_QUOTES); ?>"
                                            data-cat-id="<?php echo (int)$p['catID']; ?>"
                                            data-prod-cost="<?php echo number_format((float)$p['prodCost'], 2, '.', ''); ?>"
                                            data-quantity-stocked="<?php echo (int)$p['quantityStocked']; ?>"
                                            data-prod-discount="<?php echo number_format((float)$p['prodDiscount'], 2, '.', ''); ?>"
                                        >Edit</button>
                                        <form method="post" action="products.php" class="delete-product-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="prod_id" value="<?php echo (int)$p['prodID']; ?>">
                                            <button type="submit" class="btn-remove" data-prod-name="<?php echo htmlspecialchars($p['prodName'], ENT_QUOTES); ?>">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</main>

<script src="products.js"></script>
</body>
</html>
