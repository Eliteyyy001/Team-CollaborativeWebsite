<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['roleName'] !== 'Administrator') {
    header("Location: admin-login.php");
    exit();
}

require_once 'freshfoldDatabase/dbconnect.php';
require_once 'audit_helpers.php';

$message = "";
$messageType = "";
$adminUserID = (int)$_SESSION['admin_user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {

    if ($_POST['action'] === 'dismiss_alert') {
        $alertID = intval($_POST['alertID']);
        $stmt = $conn->prepare("UPDATE LowStockAlert SET resolveStatus = TRUE WHERE alertID = ?");
        $stmt->bind_param("i", $alertID);
        if ($stmt->execute()) {
            audit_log($conn, $adminUserID, 'DISMISS_ALERT', 'Alert #' . $alertID);
            $message = "Alert dismissed successfully.";
            $messageType = "success";
        } else {
            $message = "Error dismissing alert.";
            $messageType = "error";
        }
        $stmt->close();
    }

    elseif ($_POST['action'] === 'set_threshold') {
        $prodID = intval($_POST['prodID']);
        $reorderPoint = intval($_POST['reorderPoint']);
        $targetLevel = intval($_POST['targetLevel']);
        $stmt = $conn->prepare("INSERT INTO ProductThreshold (prodID, reorderPoint, targetLevel) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE reorderPoint = VALUES(reorderPoint), targetLevel = VALUES(targetLevel)");
        $stmt->bind_param("iii", $prodID, $reorderPoint, $targetLevel);
        if ($stmt->execute()) {
            audit_log($conn, $adminUserID, 'SET_THRESHOLD', 'Product #' . $prodID . ' reorderPoint=' . $reorderPoint . ' targetLevel=' . $targetLevel);
            $message = "Threshold updated successfully.";
            $messageType = "success";
        } else {
            $message = "Error updating threshold.";
            $messageType = "error";
        }
        $stmt->close();
    }
}

$alertsResult = $conn->query("
    SELECT la.alertID, la.prodID, la.quantityOnHand, p.prodName,
           COALESCE(pt.reorderPoint, 5) AS reorderPoint
    FROM LowStockAlert la
    JOIN Product p ON la.prodID = p.prodID
    LEFT JOIN ProductThreshold pt ON la.prodID = pt.prodID
    WHERE la.resolveStatus = FALSE
    ORDER BY la.alertID DESC
");

$productsResult = $conn->query("
    SELECT p.prodID, p.prodName, p.quantityStocked,
           COALESCE(pt.reorderPoint, 5) AS reorderPoint,
           COALESCE(pt.targetLevel, 50) AS targetLevel
    FROM Product p
    LEFT JOIN ProductThreshold pt ON p.prodID = pt.prodID
    ORDER BY p.prodName
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alerts - Freshfold Admin</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">Freshfold Admin</div>
        <ul class="nav-links">
            <li><a href="admin-dashboard.php">Users</a></li>
            <li><a href="admin-alerts.php" class="active">Alerts</a></li>
        </ul>
        <div class="nav-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin-logout.php" class="btn-exit">Logout</a>
        </div>
    </nav>

    <main class="admin-main">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="panel" style="margin-bottom: 20px;">
            <div class="panel-header">
                <h2>Active Low-Stock Alerts</h2>
            </div>
            <?php if ($alertsResult->num_rows === 0): ?>
                <p style="padding: 16px; color: #5a5a5a;">No active alerts.</p>
            <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Alert ID</th>
                        <th>Product</th>
                        <th>Qty on Hand</th>
                        <th>Reorder Point</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($alert = $alertsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $alert['alertID']; ?></td>
                        <td><?php echo htmlspecialchars($alert['prodName']); ?></td>
                        <td><?php echo $alert['quantityOnHand']; ?></td>
                        <td><?php echo $alert['reorderPoint']; ?></td>
                        <td><span class="status-badge inactive">Low Stock</span></td>
                        <td class="action-buttons">
                            <form method="POST">
                                <input type="hidden" name="action" value="dismiss_alert">
                                <input type="hidden" name="alertID" value="<?php echo $alert['alertID']; ?>">
                                <button type="submit" class="btn-activate">Dismiss</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Product Thresholds</h2>
            </div>
            <?php
            // Render one hidden form per product (HTML5 form association via form="" attribute)
            while ($prod = $productsResult->fetch_assoc()):
                $fid = 'tf-' . $prod['prodID'];
            ?>
            <form id="<?php echo $fid; ?>" method="POST">
                <input type="hidden" name="action" value="set_threshold">
                <input type="hidden" name="prodID" value="<?php echo $prod['prodID']; ?>">
            </form>
            <?php endwhile;
            // Reset result pointer
            $productsResult->data_seek(0);
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Reorder Point</th>
                        <th>Target Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prod = $productsResult->fetch_assoc()):
                        $fid = 'tf-' . $prod['prodID'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prod['prodName']); ?></td>
                        <td><?php echo $prod['quantityStocked']; ?></td>
                        <td>
                            <input type="number" name="reorderPoint" form="<?php echo $fid; ?>"
                                   value="<?php echo $prod['reorderPoint']; ?>" min="0"
                                   style="width:80px;padding:4px 8px;border:2px solid #d4a574;border-radius:4px;font-size:14px;">
                        </td>
                        <td>
                            <input type="number" name="targetLevel" form="<?php echo $fid; ?>"
                                   value="<?php echo $prod['targetLevel']; ?>" min="0"
                                   style="width:80px;padding:4px 8px;border:2px solid #d4a574;border-radius:4px;font-size:14px;">
                        </td>
                        <td>
                            <button type="submit" form="<?php echo $fid; ?>" class="btn-edit">Save</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
