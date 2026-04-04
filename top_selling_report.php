<?php
session_start();

// db connection
if (file_exists(__DIR__ . '/freshfoldDatabase/dbconnect.php')) {
    require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';
} else {
    require_once __DIR__ . '/dbconnect.php';
}

// auth check
$isAdminSession = isset($_SESSION['admin_logged_in'], $_SESSION['roleName'])
    && $_SESSION['admin_logged_in'] === true
    && $_SESSION['roleName'] === 'Administrator';

$isPosAdminSession = isset($_SESSION['userID'])
    && in_array((int)($_SESSION['roleID'] ?? 0), [1, 2, 4], true);

if (!$isAdminSession && !$isPosAdminSession) {
    header(isset($_SESSION['userID']) ? 'Location: pos.php' : 'Location: admin-login.php');
    exit;
}

// validate period
$period = $_GET['period'] ?? 'weekly';
if (!in_array($period, ['daily', 'weekly', 'monthly'], true)) {
    $period = 'weekly';
}

// date range label
switch ($period) {
    case 'daily':
        $whereClause = 'DATE(s.saleDateTime) = CURDATE()';
        $periodLabel = 'Today';
        break;
    case 'monthly':
        $whereClause = 's.saleDateTime >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
        $periodLabel = 'Last 30 Days';
        break;
    default:
        $whereClause = 's.saleDateTime >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        $periodLabel = 'Last 7 Days';
}

// top sellers query
$rows = [];
$res = $conn->query("
    SELECT p.prodName,
           SUM(si.quantity)                 AS totalSold,
           SUM(si.quantity * si.itemPrice)  AS totalRevenue
    FROM SaleItem si
    JOIN Product p ON si.prodID = p.prodID
    JOIN Sale    s ON si.saleID = s.saleID
    WHERE $whereClause
    GROUP BY p.prodID, p.prodName
    ORDER BY totalSold DESC
    LIMIT 20
");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $filename = 'top_selling_' . $period . '_' . date('Ymd') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Rank', 'Product', 'Units Sold', 'Total Revenue ($)']);
    $rank = 1;
    foreach ($rows as $row) {
        fputcsv($out, [
            $rank++,
            $row['prodName'],
            (int)$row['totalSold'],
            number_format((float)$row['totalRevenue'], 2, '.', ''),
        ]);
    }
    fclose($out);
    exit;
}

// pass data to JS
$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$chartJson = json_encode([
    'labels' => array_column($rows, 'prodName'),
    'values' => array_map(fn($r) => (int)$r['totalSold'], $rows),
], $jsonFlags);

$generatedAt = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top-Selling Products - FreshFold</title>
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .report-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-btn {
            padding: 8px 18px;
            border: 2px solid #d4a574;
            border-radius: 6px;
            background: #fefaf2;
            color: #5a5a5a;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .filter-btn.active,
        .filter-btn:hover {
            background: #d4840d;
            color: #fff;
            border-color: #d4840d;
        }
        .export-btn {
            padding: 8px 18px;
            border: 2px solid #6b9e78;
            border-radius: 6px;
            background: #6b9e78;
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .export-btn:hover {
            background: #5a8a66;
            border-color: #5a8a66;
        }
        .rank-badge {
            display: inline-block;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 13px;
            background: #e8d4a8;
            color: #8b5a2b;
        }
        .rank-badge.top1 { background: #f5c518; color: #5a3e00; }
        .rank-badge.top2 { background: #c0c0c0; color: #333; }
        .rank-badge.top3 { background: #cd7f32; color: #fff; }
        .chart-wrap {
            max-width: 700px;
            margin: 0 auto 8px;
        }
        #reportStatus {
            text-align: center;
            color: #888;
            margin: 16px 0;
        }
        .report-meta {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }
        @media print {
            .navbar, .report-controls, .no-print { display: none !important; }
            body { background: #fff; }
            .panel { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">FreshFold Admin</div>
    <ul class="nav-links">
        <li><a href="admin-dashboard.php">Users</a></li>
        <li><a href="admin-alerts.php">Alerts</a></li>
        <li><a href="display_charts.php">Charts</a></li>
        <li><a href="top_selling_report.php" class="active">Reports</a></li>
        <li><a href="audit_logs.php">Audit Logs</a></li>
    </ul>
    <div class="nav-user">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? ($_SESSION['userName'] ?? '')); ?></span>
        <?php if ($isAdminSession): ?>
            <a href="admin-logout.php" class="btn-exit">Logout</a>
        <?php else: ?>
            <form method="post" action="logout.php" style="margin:0;">
                <button type="submit" class="btn-exit">Logout</button>
            </form>
        <?php endif; ?>
    </div>
</nav>

<main class="admin-main">
    <div class="panel">
        <div class="panel-header">
            <h2>Top-Selling Products &mdash; <?php echo htmlspecialchars($periodLabel); ?></h2>
        </div>

        <div class="report-controls no-print">
            <strong>Filter:</strong>
            <a href="?period=daily"   class="filter-btn <?php echo $period === 'daily'   ? 'active' : ''; ?>">Daily</a>
            <a href="?period=weekly"  class="filter-btn <?php echo $period === 'weekly'  ? 'active' : ''; ?>">Weekly</a>
            <a href="?period=monthly" class="filter-btn <?php echo $period === 'monthly' ? 'active' : ''; ?>">Monthly</a>

            <span style="margin-left:12px;"><strong>Export:</strong></span>
            <a href="?period=<?php echo $period; ?>&export=csv" class="export-btn">Export to Excel (CSV)</a>
            <button onclick="window.print()" class="export-btn">Export to PDF</button>
        </div>

        <?php if (empty($rows)): ?>
            <p id="reportStatus">No sales recorded for this period.</p>
        <?php else: ?>
            <div class="chart-wrap">
                <canvas id="topSellingChart" aria-label="Top-selling products bar chart"></canvas>
            </div>
            <p id="reportStatus"></p>

            <table class="data-table" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row): ?>
                        <?php
                            $rank = $i + 1;
                            $badgeClass = $rank === 1 ? 'top1' : ($rank === 2 ? 'top2' : ($rank === 3 ? 'top3' : ''));
                        ?>
                        <tr>
                            <td><span class="rank-badge <?php echo $badgeClass; ?>"><?php echo $rank; ?></span></td>
                            <td><?php echo htmlspecialchars($row['prodName']); ?></td>
                            <td><?php echo (int)$row['totalSold']; ?></td>
                            <td>$<?php echo number_format((float)$row['totalRevenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="report-meta">Generated: <?php echo $generatedAt; ?></p>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>window.TOP_SELLING_DATA = <?php echo $chartJson; ?>;</script>
<script src="top_selling_report.js"></script>

</body>
</html>
<?php $conn->close(); ?>
