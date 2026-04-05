<?php
// displays sale updates, inventory updates,  stock status, and filters reports by month or day

session_start();

if (file_exists(__DIR__ . '/freshfoldDatabase/dbconnect.php')) {
    require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';
} else {
    require_once __DIR__ . '/dbconnect.php';
}
//ensure user is logged in
$isAdminDashboardSession = isset($_SESSION['admin_logged_in'], $_SESSION['roleName'])
    && $_SESSION['admin_logged_in'] === true
    && $_SESSION['roleName'] === 'Administrator';

$isPosAdminSession = isset($_SESSION['userID']) && in_array((int)($_SESSION['roleID'] ?? 0), [1, 2, 4], true);

if (!$isAdminDashboardSession && !$isPosAdminSession) {
    if (isset($_SESSION['userID'])) {
        header('Location: pos.php');
    } else {
        header('Location: admin-login.php');
    }
    exit;
}

// ---- Filtered period (daily / monthly) for sales slice, movements, top products ----
$metricsMode = (isset($_GET['metrics_mode']) && $_GET['metrics_mode'] === 'monthly') ? 'monthly' : 'daily';
$metricsDayValue = date('Y-m-d');
if (!empty($_GET['metrics_day']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$_GET['metrics_day'])) {
    $metricsDayValue = $_GET['metrics_day'];
}
$metricsMonthValue = date('Y-m');
if (!empty($_GET['metrics_month']) && preg_match('/^\d{4}-\d{2}$/', (string)$_GET['metrics_month'])) {
    $metricsMonthValue = $_GET['metrics_month'];
}

$periodStart = '';
$periodEnd = '';
$periodLabel = '';
if ($metricsMode === 'daily') {
    $dt = DateTime::createFromFormat('Y-m-d', $metricsDayValue);
    if (!$dt) {
        $dt = new DateTime('today');
        $metricsDayValue = $dt->format('Y-m-d');
    }
    $periodStart = $dt->format('Y-m-d 00:00:00');
    $periodEnd = $dt->format('Y-m-d 23:59:59');
    $periodLabel = $dt->format('F j, Y') . ' (daily)';
} else {
    $dt = DateTime::createFromFormat('!Y-m', $metricsMonthValue);
    if (!$dt) {
        $dt = new DateTime('first day of this month');
        $metricsMonthValue = $dt->format('Y-m');
    }
    $periodStart = $dt->format('Y-m-01 00:00:00');
    $periodEnd = $dt->format('Y-m-t 23:59:59');
    $periodLabel = $dt->format('F Y') . ' (monthly)';
}

$salesPeriodRevenue = 0.0;
$salesPeriodOrders = 0;
$salesPeriodAvg = 0.0;
$stmtSalesPeriod = $conn->prepare(
    'SELECT COALESCE(SUM(totalAmount),0) AS rev, COUNT(*) AS ord FROM Sale WHERE saleDateTime >= ? AND saleDateTime <= ?'
);
if ($stmtSalesPeriod) {
    $stmtSalesPeriod->bind_param('ss', $periodStart, $periodEnd);
    $stmtSalesPeriod->execute();
    $rowSp = $stmtSalesPeriod->get_result()->fetch_assoc();
    if ($rowSp) {
        $salesPeriodRevenue = (float)($rowSp['rev'] ?? 0);
        $salesPeriodOrders = (int)($rowSp['ord'] ?? 0);
        if ($salesPeriodOrders > 0) {
            $salesPeriodAvg = $salesPeriodRevenue / $salesPeriodOrders;
        }
    }
    $stmtSalesPeriod->close();
}

$movementsCount = 0;
$movementsNetQty = 0;
$stmtMoveSum = $conn->prepare(
    'SELECT COUNT(*) AS cnt, COALESCE(SUM(quantityChange),0) AS netQty FROM InventoryMovement WHERE movedAt >= ? AND movedAt <= ?'
);
if ($stmtMoveSum) {
    $stmtMoveSum->bind_param('ss', $periodStart, $periodEnd);
    $stmtMoveSum->execute();
    $rowMs = $stmtMoveSum->get_result()->fetch_assoc();
    if ($rowMs) {
        $movementsCount = (int)($rowMs['cnt'] ?? 0);
        $movementsNetQty = (int)($rowMs['netQty'] ?? 0);
    }
    $stmtMoveSum->close();
}

$topAllQty = 0;
$topAllRevenue = 0.0;
$stmtTopSum = $conn->prepare(
    'SELECT COALESCE(SUM(si.quantity),0) AS qty, COALESCE(SUM(si.quantity * si.itemPrice),0) AS revenue
     FROM SaleItem si INNER JOIN Sale s ON s.saleID = si.saleID
     WHERE s.saleDateTime >= ? AND s.saleDateTime <= ?'
);
if ($stmtTopSum) {
    $stmtTopSum->bind_param('ss', $periodStart, $periodEnd);
    $stmtTopSum->execute();
    $rowTs = $stmtTopSum->get_result()->fetch_assoc();
    if ($rowTs) {
        $topAllQty = (int)($rowTs['qty'] ?? 0);
        $topAllRevenue = (float)($rowTs['revenue'] ?? 0);
    }
    $stmtTopSum->close();
}

$topRowsSubtotalQty = 0;
$topRowsSubtotalRev = 0.0;

// ---- Sales metrics ----
$grandTotal = 0.0;
$ordersAll = 0;
$resAll = $conn->query("SELECT COALESCE(SUM(totalAmount),0) AS revenueAll, COUNT(*) AS ordersAll FROM Sale");
if ($resAll && $row = $resAll->fetch_assoc()) {
    $grandTotal = (float)($row['revenueAll'] ?? 0);
    $ordersAll = (int)($row['ordersAll'] ?? 0);
}

$revenueToday = 0.0;
$ordersToday = 0;
$resToday = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS revenueToday, COUNT(*) AS ordersToday
    FROM Sale
    WHERE DATE(saleDateTime) = CURDATE()
");
if ($resToday && $row = $resToday->fetch_assoc()) {
    $revenueToday = (float)($row['revenueToday'] ?? 0);
    $ordersToday = (int)($row['ordersToday'] ?? 0);
}

$revenue7d = 0.0;
$orders7d = 0;
$avgOrder7d = 0.0;
$res7d = $conn->query("
    SELECT COALESCE(SUM(totalAmount),0) AS revenue7d, COUNT(*) AS orders7d
    FROM Sale
    WHERE saleDateTime >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
if ($res7d && $row = $res7d->fetch_assoc()) {
    $revenue7d = (float)($row['revenue7d'] ?? 0);
    $orders7d = (int)($row['orders7d'] ?? 0);
    if ($orders7d > 0) {
        $avgOrder7d = $revenue7d / $orders7d;
    }
}

// ---- Inventory metrics ----
$productCount = 0;
$totalUnits = 0;
$inStockCount = 0;
$outOfStockCount = 0;
$resInv = $conn->query("
    SELECT
        SUM(CASE WHEN quantityStocked > 0 THEN 1 ELSE 0 END) AS inStockCount,
        SUM(CASE WHEN quantityStocked <= 0 THEN 1 ELSE 0 END) AS outOfStockCount,
        COUNT(*) AS productCount,
        COALESCE(SUM(quantityStocked),0) AS totalUnits
    FROM Product
");
if ($resInv && $row = $resInv->fetch_assoc()) {
    $productCount = (int)($row['productCount'] ?? 0);
    $totalUnits = (int)($row['totalUnits'] ?? 0);
    $inStockCount = (int)($row['inStockCount'] ?? 0);
    $outOfStockCount = (int)($row['outOfStockCount'] ?? 0);
}

// ---- Sales trend (last 30 days) ----
$trendLabels = [];
$trendTotals = [];
$resTrend = $conn->query("
    SELECT DATE(saleDateTime) AS day, COALESCE(SUM(totalAmount),0) AS total
    FROM Sale
    WHERE saleDateTime >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(saleDateTime)
    ORDER BY day ASC
");
if ($resTrend) {
    while ($r = $resTrend->fetch_assoc()) {
        $trendLabels[] = (string)$r['day'];
        $trendTotals[] = (float)$r['total'];
    }
}

// ---- Low stock (reorder) ----
$lowStock = [];
$resLow = $conn->query("
    SELECT p.prodID, p.prodName, p.quantityStocked, pt.reorderPoint, pt.targetLevel
    FROM Product p
    JOIN ProductThreshold pt ON pt.prodID = p.prodID
    WHERE p.quantityStocked <= pt.reorderPoint
    ORDER BY (pt.reorderPoint - p.quantityStocked) DESC, p.prodName
    LIMIT 12
");
if ($resLow) {
    while ($r = $resLow->fetch_assoc()) {
        $lowStock[] = $r;
    }
}

// ---- Recent inventory movements ----
$recentMovements = [];
$stmtMove = $conn->prepare(
    'SELECT im.transType, im.transID, im.quantityChange, im.movedAt,
            p.prodName,
            u.userName AS movedByName
     FROM InventoryMovement im
     JOIN Product p ON p.prodID = im.prodID
     LEFT JOIN Users u ON u.userID = im.movedBy
     WHERE im.movedAt >= ? AND im.movedAt <= ?
     ORDER BY im.movedAt DESC
     LIMIT 100'
);
if ($stmtMove) {
    $stmtMove->bind_param('ss', $periodStart, $periodEnd);
    $stmtMove->execute();
    $resMove = $stmtMove->get_result();
    if ($resMove) {
        while ($r = $resMove->fetch_assoc()) {
            $recentMovements[] = $r;
        }
    }
    $stmtMove->close();
}

$generatedAt = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold - Admin Metrics</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">FreshFold Admin</div>
    <ul class="nav-links">
        <li><a href="admin-dashboard.php" >Users</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="admin_metrics.php" class="active">Admin Metrics</a></li>
        <li><a href="display_charts.php">Charts</a></li>
        <li><a href="audit_logs.php">Audit Logs</a></li>
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
    <form class="admin-metrics__filters panel" method="get" action="">
        <div class="panel-header"><h2>Metrics date range</h2></div>
        <p>
            <label><strong>Granularity:</strong></label>
            <select name="metrics_mode">
                <option value="daily"<?php echo $metricsMode === 'daily' ? ' selected' : ''; ?>>Daily</option>
                <option value="monthly"<?php echo $metricsMode === 'monthly' ? ' selected' : ''; ?>>Monthly</option>
            </select>
        </p>
        <p>
            <label><strong>Day:</strong></label>
            <input type="date" name="metrics_day" value="<?php echo htmlspecialchars($metricsDayValue); ?>">
            <label><strong>Month:</strong></label>
            <input type="month" name="metrics_month" value="<?php echo htmlspecialchars($metricsMonthValue); ?>">
        </p>
        <p><button type="submit" class="btn-add">Apply range</button></p>
        <p class="admin-metrics__hint">Daily uses the day field; monthly uses the month field.</p>
    </form>

    <section class="panel admin-metrics__panel admin-metrics__panel--sales">
        <div class="panel-header"><h2>Sales Metrics</h2></div>
        <p><strong>Selected period (<?php echo htmlspecialchars($periodLabel); ?>):</strong>
            $<?php echo number_format($salesPeriodRevenue, 2); ?> revenue,
            <?php echo (int)$salesPeriodOrders; ?> orders,
            $<?php echo number_format($salesPeriodAvg, 2); ?> avg order</p>
        <p><strong>Total of all sales:</strong> $<?php echo number_format($grandTotal, 2); ?> (<?php echo (int)$ordersAll; ?> orders)</p>
        <p><strong>Today:</strong> $<?php echo number_format($revenueToday, 2); ?> (<?php echo (int)$ordersToday; ?> orders)</p>
        <p><strong>Last 7 days:</strong> $<?php echo number_format($revenue7d, 2); ?> (<?php echo (int)$orders7d; ?> orders)</p>
        <p><strong>Avg order (7 days):</strong> $<?php echo number_format($avgOrder7d, 2); ?></p>
        <p class="admin-metrics__updated">Updated: <?php echo htmlspecialchars($generatedAt); ?></p>
    </section>

    <section class="panel admin-metrics__panel admin-metrics__panel--inventory">
        <div class="panel-header"><h2>Inventory Metrics</h2></div>
        <p><strong>Products:</strong> <?php echo (int)$productCount; ?></p>
        <p><strong>Total units in stock:</strong> <?php echo (int)$totalUnits; ?></p>
        <p><strong>In-stock items:</strong> <?php echo (int)$inStockCount; ?></p>
        <p><strong>Out-of-stock items:</strong> <?php echo (int)$outOfStockCount; ?></p>
    </section>

    <section class="panel admin-metrics__panel admin-metrics__panel--lowstock">
        <div class="panel-header"><h2>Low Stock (reorder)</h2></div>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>On hand</th>
                    <th>Reorder</th>
                    <th>Target</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lowStock)): ?>
                    <tr><td colspan="4">No low-stock items right now.</td></tr>
                <?php else: ?>
                    <?php foreach ($lowStock as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['prodName']); ?></td>
                            <td><?php echo (int)$r['quantityStocked']; ?></td>
                            <td><?php echo (int)$r['reorderPoint']; ?></td>
                            <td><?php echo (int)$r['targetLevel']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="panel admin-metrics__panel admin-metrics__panel--movements">
        <div class="panel-header"><h2>Recent Inventory Movements</h2></div>
        <p><strong>Period:</strong> <?php echo htmlspecialchars($periodLabel); ?></p>
        <p><strong>Summary:</strong> <?php echo (int)$movementsCount; ?> movement(s),
            net quantity change <?php echo (int)$movementsNetQty; ?></p>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentMovements)): ?>
                    <tr><td colspan="5">No movements found in this period.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentMovements as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['movedAt']); ?></td>
                            <td><?php echo htmlspecialchars($r['prodName']); ?></td>
                            <td><?php echo htmlspecialchars($r['transType']); ?></td>
                            <td><?php echo (int)$r['quantityChange']; ?></td>
                            <td><?php echo htmlspecialchars($r['movedByName'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php
$adminMetricsJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
window.ADMIN_METRICS = {
  trendLabels: <?php echo json_encode($trendLabels, $adminMetricsJsonFlags); ?>,
  trendTotals: <?php echo json_encode($trendTotals, $adminMetricsJsonFlags); ?>,
  inStock: <?php echo (int)$inStockCount; ?>,
  outStock: <?php echo (int)$outOfStockCount; ?>
};
</script>
<script src="admin_metrics.js"></script>

</body>
</html>

