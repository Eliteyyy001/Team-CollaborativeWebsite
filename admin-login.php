<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'freshfoldDatabase/dbconnect.php';
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT u.userID, u.userName, u.userPasscode, u.activityStatus, r.roleName 
            FROM Users u 
            JOIN Roles r ON u.roleID = r.roleID 
            WHERE u.userName = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($user['activityStatus'] == 0) {
            $error = "This account has been deactivated.";
        }
        elseif ($password === $user['userPasscode']) {
            if ($user['roleName'] === 'Administrator') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['userID'];
                $_SESSION['admin_username'] = $user['userName'];
                $_SESSION['roleName'] = $user['roleName'];
                header("Location: admin-dashboard.php");
                exit();
            } else {
                $error = "Access denied. Administrator privileges required.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Freshfold</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box panel">
            <div class="panel-header">
                <h2>Freshfold Admin Login</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="admin-login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Enter admin username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter password">
                </div>
                
                <button type="submit" class="btn-submit btn-full">Login</button>
            </form>
            
            <a href="index.html" class="back-link">← Back to Main Site</a>
        </div>
    </div>
</body>
</html>
