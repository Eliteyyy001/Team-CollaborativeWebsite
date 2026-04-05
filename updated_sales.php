<?php

date_default_timezone_set('America/New_York');

session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Example sales data (replace with database later)
$sales = [
    ["id" => 23, "datetime" => "2026-03-22 00:30:41", "total" => 16.98, "items" => 2, "cashier" => 9],
    ["id" => 22, "datetime" => "2026-03-22 00:28:39", "total" => 19.98, "items" => 2, "cashier" => 9],
    ["id" => 21, "datetime" => "2026-03-22 00:21:52", "total" => 17.98, "items" => 2, "cashier" => 9],
    ["id" => 20, "datetime" => "2026-03-18 22:38:54", "total" => 33.96, "items" => 4, "cashier" => 9],
    ["id" => 19, "datetime" => "2026-03-17 20:24:20", "total" => 13.98, "items" => 2, "cashier" => 8],
    ["id" => 18, "datetime" => "2026-03-17 20:21:03", "total" => 26.97, "items" => 3, "cashier" => 8],
    ["id" => 17, "datetime" => "2026-03-01 00:05:26", "total" => 4.99, "items" => 1, "cashier" => 4],
    ["id" => 16, "datetime" => "2026-03-01 00:04:13", "total" => 11.99, "items" => 1, "cashier" => 4],
    ["id" => 15, "datetime" => "2026-03-01 00:03:52", "total" => 11.99, "items" => 1, "cashier" => 4],
    ["id" => 14, "datetime" => "2026-03-01 00:01:27", "total" => 20.97, "items" => 3, "cashier" => 4],
    ["id" => 13, "datetime" => "2026-02-28 23:15:56", "total" => 21.98, "items" => 2, "cashier" => 4],
    ["id" => 12, "datetime" => "2026-02-28 23:08:25", "total" => 33.97, "items" => 3, "cashier" => 4],
    ["id" => 11, "datetime" => "2026-02-28 22:32:36", "total" => 20.98, "items" => 2, "cashier" => 4],
];

$total_sales = array_sum(array_column($sales, "total"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales History - FreshFold POS</title>
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

      <a href="sales.php" class="active"
         style="color:white; padding:6px 12px; background:#ffa500; font-weight:bold; text-decoration:none;">
          Sales
      </a>

      <a href="audit_logs.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">
          Audit Logs
      </a>
  </div>

  <div class="user-section" style="display:flex; gap:15px; align-items:center;">
    User: <?= htmlspecialchars($_SESSION['username'] ?? 'Jane Smith') ?>
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
        Sales History
    </div>

    <!-- Beige box -->
    <div style="
        background:#fffaf2;
        border:1px solid #c0b070;
        border-top:none;
        padding:20px;
        border-radius:0 0 6px 6px;
    ">

        <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
            <thead>
                <tr style="background:#d6b98a;">
                    <th style="border:1px solid #c0b070; padding:10px;">Sale ID</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Date / Time</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Total Amount</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Items</th>
                    <th style="border:1px solid #c0b070; padding:10px;">Cashier (userID)</th>
                    <th style="border:1px solid #c0b070; padding:10px;">View</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($sales as $s): ?>
                <tr>
                    <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $s["id"] ?></td>
                    <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $s["datetime"] ?></td>
                    <td style="border:1px solid #c0b070; padding:12px 14px;">$<?= number_format($s["total"], 2) ?></td>
                    <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $s["items"] ?></td>
                    <td style="border:1px solid #c0b070; padding:12px 14px;"><?= $s["cashier"] ?></td>
                    <td style="border:1px solid #c0b070; padding:12px 14px;">
                        <a href="view_sale.php?id=<?= $s["id"] ?>">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 style="margin-top:20px;">Total of all sales: $<?= number_format($total_sales, 2) ?></h3>

    </div>

</div>

</body>
</html>
