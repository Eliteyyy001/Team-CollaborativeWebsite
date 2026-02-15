<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['roleName'] !== 'Administrator') {
    header("Location: admin-login.php");
    exit();
}

require_once 'freshfoldDatabase/dbconnect.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'add_user') {
        $userName = trim($_POST['userName']);
        $userEmail = trim($_POST['userEmail']);
        $userPasscode = trim($_POST['userPasscode']);
        $roleID = intval($_POST['roleID']);
        
        $checkSql = "SELECT userID FROM Users WHERE userName = ? OR userEmail = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $userName, $userEmail);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $message = "Username or email already exists.";
            $messageType = "error";
        } else {
            $sql = "INSERT INTO Users (userName, userEmail, userPasscode, roleID, activityStatus) VALUES (?, ?, ?, ?, TRUE)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $userName, $userEmail, $userPasscode, $roleID);
            
            if ($stmt->execute()) {
                $message = "User added successfully.";
                $messageType = "success";
            } else {
                $message = "Error adding user.";
                $messageType = "error";
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
    
    elseif ($_POST['action'] === 'edit_user') {
        $userID = intval($_POST['userID']);
        $userName = trim($_POST['userName']);
        $userEmail = trim($_POST['userEmail']);
        $roleID = intval($_POST['roleID']);
        
        $checkSql = "SELECT userID FROM Users WHERE (userName = ? OR userEmail = ?) AND userID != ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ssi", $userName, $userEmail, $userID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $message = "Username or email already exists for another user.";
            $messageType = "error";
        } else {
            if (!empty($_POST['userPasscode'])) {
                $userPasscode = trim($_POST['userPasscode']);
                $sql = "UPDATE Users SET userName = ?, userEmail = ?, userPasscode = ?, roleID = ? WHERE userID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssii", $userName, $userEmail, $userPasscode, $roleID, $userID);
            } else {
                $sql = "UPDATE Users SET userName = ?, userEmail = ?, roleID = ? WHERE userID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssii", $userName, $userEmail, $roleID, $userID);
            }
            
            if ($stmt->execute()) {
                $message = "User updated successfully.";
                $messageType = "success";
            } else {
                $message = "Error updating user.";
                $messageType = "error";
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
    
    elseif ($_POST['action'] === 'toggle_status') {
        $userID = intval($_POST['userID']);
        $newStatus = intval($_POST['newStatus']);
        
        if ($userID == $_SESSION['admin_user_id'] && $newStatus == 0) {
            $message = "You cannot deactivate your own account.";
            $messageType = "error";
        } else {
            $sql = "UPDATE Users SET activityStatus = ? WHERE userID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $newStatus, $userID);
            
            if ($stmt->execute()) {
                $statusText = $newStatus ? "activated" : "deactivated";
                $message = "User $statusText successfully.";
                $messageType = "success";
            } else {
                $message = "Error updating user status.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }
}

$usersSql = "SELECT u.userID, u.userName, u.userEmail, u.activityStatus, u.roleID, r.roleName 
             FROM Users u 
             JOIN Roles r ON u.roleID = r.roleID 
             ORDER BY u.userID";
$usersResult = $conn->query($usersSql);

$rolesSql = "SELECT roleID, roleName FROM Roles ORDER BY roleID";
$rolesResult = $conn->query($rolesSql);
$roles = [];
while ($role = $rolesResult->fetch_assoc()) {
    $roles[] = $role;
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editID = intval($_GET['edit']);
    $editSql = "SELECT userID, userName, userEmail, roleID FROM Users WHERE userID = ?";
    $editStmt = $conn->prepare($editSql);
    $editStmt->bind_param("i", $editID);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    if ($editResult->num_rows === 1) {
        $editUser = $editResult->fetch_assoc();
    }
    $editStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Freshfold</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">Freshfold Admin</div>
        <ul class="nav-links">
            <li><a href="admin-dashboard.php" class="active">Users</a></li>
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

        <div class="admin-grid">
            <div class="panel user-list-panel">
                <div class="panel-header">
                    <h2>All Users</h2>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                        <tr class="<?php echo $user['activityStatus'] ? '' : 'inactive-row'; ?>">
                            <td><?php echo $user['userID']; ?></td>
                            <td><?php echo htmlspecialchars($user['userName']); ?></td>
                            <td><?php echo htmlspecialchars($user['userEmail']); ?></td>
                            <td><?php echo htmlspecialchars($user['roleName']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $user['activityStatus'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $user['activityStatus'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="admin-dashboard.php?edit=<?php echo $user['userID']; ?>" class="btn-edit">Edit</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="userID" value="<?php echo $user['userID']; ?>">
                                    <input type="hidden" name="newStatus" value="<?php echo $user['activityStatus'] ? '0' : '1'; ?>">
                                    <button type="submit" class="<?php echo $user['activityStatus'] ? 'btn-deactivate' : 'btn-activate'; ?>">
                                        <?php echo $user['activityStatus'] ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel user-form-panel">
                <div class="panel-header">
                    <h2><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h2>
                </div>
                
                <form method="POST" action="admin-dashboard.php">
                    <input type="hidden" name="action" value="<?php echo $editUser ? 'edit_user' : 'add_user'; ?>">
                    <?php if ($editUser): ?>
                        <input type="hidden" name="userID" value="<?php echo $editUser['userID']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="userName">Username</label>
                        <input type="text" id="userName" name="userName" required 
                               value="<?php echo $editUser ? htmlspecialchars($editUser['userName']) : ''; ?>"
                               placeholder="Enter username">
                    </div>
                    
                    <div class="form-group">
                        <label for="userEmail">Email</label>
                        <input type="email" id="userEmail" name="userEmail" required 
                               value="<?php echo $editUser ? htmlspecialchars($editUser['userEmail']) : ''; ?>"
                               placeholder="Enter email">
                    </div>
                    
                    <div class="form-group">
                        <label for="userPasscode">Password <?php echo $editUser ? '(leave blank to keep current)' : ''; ?></label>
                        <input type="password" id="userPasscode" name="userPasscode" 
                               <?php echo $editUser ? '' : 'required'; ?>
                               placeholder="<?php echo $editUser ? 'Enter new password or leave blank' : 'Enter password'; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="roleID">Role</label>
                        <select id="roleID" name="roleID" required>
                            <option value="">Select a role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['roleID']; ?>" 
                                    <?php echo ($editUser && $editUser['roleID'] == $role['roleID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['roleName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <?php echo $editUser ? 'Update User' : 'Add User'; ?>
                        </button>
                        <?php if ($editUser): ?>
                            <a href="admin-dashboard.php" class="btn-cancel">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
