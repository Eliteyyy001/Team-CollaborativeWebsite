<?php
/*  Builds chart data from the database and creates the chart page with charts included
 */

session_start();

if (file_exists(__DIR__ . '/freshfoldDatabase/dbconnect.php')) {
    require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';
} else {
    require_once __DIR__ . '/dbconnect.php';
}

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

$inStockCount = 0;
$outOfStockCount = 0;
$resInv = $conn->query("
    SELECT
        SUM(CASE WHEN quantityStocked > 0 THEN 1 ELSE 0 END) AS inStockCount,
        SUM(CASE WHEN quantityStocked <= 0 THEN 1 ELSE 0 END) AS outOfStockCount
    FROM Product
");
if ($resInv && $row = $resInv->fetch_assoc()) {
    $inStockCount = (int)($row['inStockCount'] ?? 0);
    $outOfStockCount = (int)($row['outOfStockCount'] ?? 0);
}

$chartPayload = [
    'trendLabels' => $trendLabels,
    'trendTotals' => $trendTotals,
    'inStock' => $inStockCount,
    'outStock' => $outOfStockCount,
];
$chartJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$chartJson = json_encode($chartPayload, $chartJsonFlags);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold - Chart display</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">FreshFold Admin</div>
    <ul class="nav-links">
        <li><a href="admin-dashboard.php" >Users</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="admin-metrics.php">Admin Metrics</a></li>
        <li><a href="display_charts.php" class="active">Charts</a></li>
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
    <section class="panel admin-metrics__panel admin-metrics__panel--trend">
        <div class="panel-header"><h2>Sales trend (last 30 days)</h2></div>
        <p class="admin-metrics__chart-caption">Daily revenue from recorded sales.</p>
        <div class="admin-metrics__chart-wrap">
            <canvas id="salesTrend" aria-label="Sales trend line chart"></canvas>
        </div>
    </section>

    <section class="panel admin-metrics__panel admin-metrics__panel--stock">
        <div class="panel-header"><h2>Stock status</h2></div>
        <p class="admin-metrics__chart-caption">Products in stock vs out of stock.</p>
        <div class="admin-metrics__chart-wrap admin-metrics__chart-wrap--pie">
            <canvas id="stockPie" aria-label="Stock status doughnut chart"></canvas>
        </div>
    </section>

    <p id="chartStatus" class="admin-metrics__hint" aria-live="polite"></p>
    <p class="admin-metrics__hint"><a href="admin-metrics.php" class="back-link" style="margin-top:0">Back to Admin Metrics</a></p>
	<p class="admin-metrics__hint"><a href="print_report.php" class="back-link" style="margin-top:0">Print Report</a></p>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>window.CHART_DISPLAY_DATA = <?php echo $chartJson; ?>;</script>
<script src="display_charts.js"></script>

</body>
</html>
