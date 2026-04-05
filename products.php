<?php
date_default_timezone_set('America/New_York');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: updated_login.php");
    exit;
}

$cashier_name = $_SESSION['cashier_name'] ?? 'Unknown';

// Connect to database
require_once __DIR__ . "/db_connection.php";

// Fetch all products
$query = "SELECT * FROM products ORDER BY name ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products - FreshFold POS</title>
</head>
<body style="background:white; font-family:Arial;">

<!-- Connection bar -->
<div style="background:white; color:#000; padding:6px 12px; font-size:0.9rem; font-weight:bold; border-bottom:1px solid #e0e0e0;">
  Connected successfully
</div>

<!-- Top navigation bar -->
<div class="top-nav" style="background:#3a3a3a; color:white; padding:8px 16px; display:flex; justify-content:space-between; align-items:center;">
  <div>FreshFold POS</div>

  <div class="menu" style="display:flex; gap:10px;">
      <a href="pos.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">
          Make Sale
      </a>

      <a href="products.php" class="active"
         style="color:white; padding:6px 12px; background:#ffa500; font-weight:bold; text-decoration:none;">
          Products
      </a>

      <a href="sales.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">
          Sales
      </a>

      <a href="audit_logs.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">
          Audit Logs
      </a>
  </div>

  <div class="user-section" style="display:flex; gap:15px; align-items:center;">
    User: <?= htmlspecialchars($cashier_name) ?>
    <a href="logout.php" style="color:white;">Logout</a>
  </div>
</div>

<!-- Main content -->
<div style="padding:20px;">

    <!-- Beige header bar -->
    <div style="
        background:#d6b98a;
        padding:10px;
        font-weight:bold;
        border:1px solid #c0b070;
        border-bottom:none;
        width:100%;
        border-radius:6px 6px 0 0;
    ">
        Products List
    </div>

    <!-- Beige box -->
    <div style="
        background:#fffaf2;
        border:1px solid:#c0b070;
        border-top:none;
        padding:20px;
        border-radius:0 0 6px 6px;
    ">

        <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
            <thead>
                <tr style="background:#d6b98a;">
                    <th style="border:1px solid #c0b070; padding:10px;">Product</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Price</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Stock</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Status</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($p = $result->fetch_assoc()): ?>
                    <?php
                        if ($p['stock'] == 0) {
                            $status = "<span style='color:red; font-weight:bold;'>Out of Stock</span>";
                        } elseif ($p['stock'] <= 5) {
                            $status = "<span style='color:orange; font-weight:bold;'>Low Stock</span>";
                        } else {
                            $status = "<span style='color:green; font-weight:bold;'>In Stock</span>";
                        }
                    ?>
                    <tr>
                        <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $p["name"] ?></td>
                        <td style="border:1px solid #c0b070; padding:12px 14px;">$<?= number_format($p["price"], 2) ?></td>
                        <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $p["stock"] ?></td>
                        <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $status ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>
