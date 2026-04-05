<?php
//this page will print out the sales report chart displayed in display_charts.php 
session_start();

//connect to database
if (file_exists(__DIR__ . '/freshfoldDatabase/dbconnect.php')) {
    include __DIR__ . '/freshfoldDatabase/dbconnect.php';
} else {
    include __DIR__ . '/dbconnect.php';
}
//ensure admin logged in
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
//sales trend chart handling
$trendLabels = [];
$trendTotals = [];
$trendRows = [];
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
        $trendRows[] = [
            'day' => (string)$r['day'],
            'total' => (float)$r['total'],
        ];
    }
}
//stock status handling
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

$generatedAt = date('Y-m-d H:i:s');
$periodStart = date('Y-m-d', strtotime('-30 days'));
$periodEnd = date('Y-m-d');
$daysWithSales = count($trendRows);
$totalRevenue = array_sum($trendTotals);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales report - FreshFold</title>
    <link rel="stylesheet" href="print_report.css">

</head>
<body>
<div class="receipt">
    <div class="store-name">FreshFold</div>
    <div class="receipt-meta">
        Sales and stock report (last 30 days)<br>
        Generated <?php echo htmlspecialchars($generatedAt); ?>
    </div>
    <hr>

    <p class="report-section-title">Summary</p>
    <div class="summary-grid">
        <div><strong>Report period:</strong> <?php echo htmlspecialchars($periodStart); ?> to <?php echo htmlspecialchars($periodEnd); ?></div>
        <div><strong>Days with sales:</strong> <?php echo (int)$daysWithSales; ?></div>
        <div><strong>Total revenue:</strong> $<?php echo number_format((float)$totalRevenue, 2); ?></div>
        <div><strong>In stock / out of stock:</strong> <?php echo (int)$inStockCount; ?> / <?php echo (int)$outOfStockCount; ?></div>
    </div>

    <hr>

    <p class="report-section-title">Sales trend (last 30 days)</p>
    <p class="receipt-meta" style="margin-bottom:10px">Daily revenue from recorded sales.</p>
    <div class="chart-wrap-print">
        <canvas id="salesTrend" aria-label="Sales trend line chart"></canvas>
    </div>

    <hr>

    <p class="report-section-title">Stock status</p>
    <p class="receipt-meta" style="margin-bottom:10px">
        In stock: <?php echo (int)$inStockCount; ?> products —
        Out of stock: <?php echo (int)$outOfStockCount; ?> products
    </p>
    <div class="chart-wrap-print chart-wrap-print--pie">
        <canvas id="stockPie" aria-label="Stock status doughnut chart"></canvas>
    </div>

    <hr>

    <p class="report-section-title">Daily sales breakdown</p>
    <?php if (!empty($trendRows)): ?>
        <table class="daily-table" aria-label="Daily sales table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trendRows as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['day']); ?></td>
                        <td>$<?php echo number_format((float)$row['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="hint">No sales found for the selected period.</p>
    <?php endif; ?>

    <hr>
    <div class="receipt-meta">End of report</div>

    <div class="actions">
        <button type="button" class="btn-print" id="btnPrintReport">Print / Save as PDF</button>
        <a class="back-links" href="display_charts.php">← Back to Charts</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>window.PRINT_REPORT_CHART_DATA = <?php echo $chartJson; ?>;</script>
<script src="print_report.js"></script>

</body>
</html>
