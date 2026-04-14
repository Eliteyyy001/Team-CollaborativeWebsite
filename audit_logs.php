<?php
//start session
session_start();

require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/audit_helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['roleName'] !== 'Administrator') {
    header("Location: admin-login.php");
    exit();
}

$logs = [];
$result = $conn->query("
    SELECT a.auditID,
           a.actionTime,
           a.actionType,
           a.affectedEntity,
           u.userName,
           u.userID
    FROM AuditLog a
    JOIN Users u ON a.performedByUserID = u.userID
    ORDER BY a.actionTime DESC
    LIMIT 200
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold - Audit Logs</title>
    <link rel="stylesheet" href="pos.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">FreshFold POS</div>
    <ul class="nav-links">
       
		<li><a href="admin-dashboard.php">Users</a></li>
        <li><a href="admin-alerts.php">Alerts</a></li>
        <li><a href="display_charts.php">Charts</a></li>
        <li><a href="top_selling_report.php" >Reports</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="audit_logs.php" class="active">Audit Logs</a></li>
    </ul>
    <div class="nav-user">
        <span><?php echo htmlspecialchars($_SESSION['userName'] ?? ''); ?></span>
        <form method="post" action="logout.php" style="display:inline;">
            <button type="submit" class="btn-exit">Logout</button>
        </form>
    </div>
</nav>

<main class="pos-main" style="max-width: 1200px;">
    <section class="panel" style="flex: 1;">
        <div class="panel-header">
            <h2>Audit Logs</h2>
        </div>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="4">No audit logs found.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$log['actionTime']); ?></td>
                            <td><?php echo htmlspecialchars((string)$log['userName']); ?> (<?php echo (int)$log['userID']; ?>)</td>
                            <td><?php echo htmlspecialchars((string)$log['actionType']); ?></td>
                            <td><?php echo htmlspecialchars((string)($log['affectedEntity'] ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>

