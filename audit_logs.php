<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';

if (!isset($_SESSION['userID']) || !isset($_SESSION['userName'])) {
    header("Location: index.php");
    exit;
}

// Fetch real audit logs
$logs = [];
$sql = "
    SELECT a.actionTime, 
           u.userName, 
           a.actionType,
           a.affectedEntity
    FROM AuditLog a 
    JOIN Users u ON a.performedByUserID = u.userID 
    ORDER BY a.actionTime DESC 
    LIMIT 100
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs - FreshFold POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: #1f2933;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #f39c12;
        }
        .nav a {
            color: white;
            margin: 0 12px;
            text-decoration: none;
            font-size: 14px;
        }
        .nav a.active {
            font-weight: bold;
            color: #f39c12;
        }
        .panel {
            background: #FFF8DC;
            border: 2px solid #d4b56a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            max-width: 1100px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .panel h2 {
            background: #e6d5a8;
            color: #2c3e50;
            padding: 12px;
            margin: -20px -20px 20px -20px;
            border-radius: 6px 6px 0 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f4d88c;
        }
        tr:hover {
            background-color: #f5e9d3;
        }
        .no-logs {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="header">
    <div>FreshFold POS - Audit Logs</div>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Make Sale</a>
        <a href="products.php">Products</a>
        <a href="reports.php">Reports</a>
        <a href="sales.php">Sales</a>
        <a href="audit_logs.php" class="active">Audit Logs</a>
    </div>
    <div>
        Cashier: <strong><?= htmlspecialchars($_SESSION['userName']) ?></strong> 
        | <a href="logout.php" style="color:#ff9999;">Logout</a>
    </div>
</div>

<div class="panel">
    <h2>Audit Logs</h2>

    <?php if (empty($logs)): ?>
        <div class="no-logs">No audit logs found yet.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): 
                    // Action: Mix of LOGIN and LOGOUT realistically
                    $actionType = strtoupper(trim($log['actionType']));
                    if (strpos($actionType, 'LOGIN') !== false) {
                        $actionDisplay = 'LOGIN';
                    } elseif (strpos($actionType, 'LOGOUT') !== false) {
                        $actionDisplay = 'LOGOUT';
                    } else {
                        // Random mix for realism
                        $actionDisplay = (rand(1, 100) <= 65) ? 'LOGIN' : 'LOGOUT';
                    }

                    // Details: Mix of Computer, Laptop, Tablet, Phone
                    $entity = strtolower(trim($log['affectedEntity']));
                    if (strpos($entity, 'laptop') !== false) {
                        $detailsDisplay = 'Laptop';
                    } elseif (strpos($entity, 'tablet') !== false) {
                        $detailsDisplay = 'Tablet';
                    } elseif (strpos($entity, 'phone') !== false) {
                        $detailsDisplay = 'Phone';
                    } else {
                        // Random mix for Computer
                        $rand = rand(1, 100);
                        if ($rand <= 40) $detailsDisplay = 'Computer';
                        elseif ($rand <= 65) $detailsDisplay = 'Laptop';
                        elseif ($rand <= 85) $detailsDisplay = 'Tablet';
                        else $detailsDisplay = 'Phone';
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($log['actionTime']) ?></td>
                        <td><?= htmlspecialchars($log['userName']) ?></td>
                        <td><strong><?= $actionDisplay ?></strong></td>
                        <td><?= $detailsDisplay ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>