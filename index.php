<?php
// Simple login + signup using the Users table user names, emails, and passwords

session_start();
require __DIR__ . '/dbconnect.php'; 


// Messages
$loginError = '';
$signupError = '';
$signupSuccess = '';

// Handle LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usernameOrEmail === '' || $password === '') {
        $loginError = 'Please enter both username/email and password.';
    } else {
        // Look up active user by username or email + passcode
        $stmt = $conn->prepare("
    SELECT userID, userName, roleID, userPasscode
    FROM Users
    WHERE activityStatus = TRUE
      AND (userName = ? OR userEmail = ?)
    LIMIT 1");
	if ($stmt) {
    $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

            if ($user && $user['userPasscode'] === $password) {
                // block customers
                if ((int)$user['roleID'] === 5) {
                    $loginError = 'This login is for staff only.';
                } else {
                    $_SESSION['userID'] = (int)$user['userID'];
                    $_SESSION['userName'] = (string)$user['userName'];
                    $_SESSION['roleID'] = (int)$user['roleID'];
                    header('Location: pos.php');
                    exit;
                }
            } else {
                $loginError = 'Wrong username/email or password!';
            }
        } else {
            $loginError = 'Database error during login.';
        }
    }
}

// Handle SIGNUP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $newUsername = trim($_POST['new_username'] ?? '');
    $newEmail    = trim($_POST['new_email'] ?? '');
    $newPass     = trim($_POST['new_password'] ?? '');

    if ($newUsername === '' || $newEmail === '' || $newPass === '') {
        $signupError = 'Please fill in all signup fields.';
    } else {
        // Check if username or email already exists
        $check = $conn->prepare("SELECT userID FROM Users WHERE userName = ? OR userEmail = ? LIMIT 1");
        if ($check) {
            $check->bind_param('ss', $newUsername, $newEmail);
            $check->execute();
            $checkResult = $check->get_result();
            $exists = $checkResult && $checkResult->num_rows > 0;
            $check->close();

            if ($exists) {
                $signupError = 'That username or email is already taken.';
            } else {
                // Create new user as Cashier (roleID = 3), active
                $roleID = 3; // Cashier
                $activityStatus = 1;

                $insert = $conn->prepare("
                    INSERT INTO Users (userName, userPasscode, userEmail, roleID, activityStatus)
                    VALUES (?, ?, ?, ?, ?)
                ");
                if ($insert) {
                    $insert->bind_param('sssii', $newUsername, $newPass, $newEmail, $roleID, $activityStatus);
                    if ($insert->execute()) {
                        $signupSuccess = 'Account created! You can now log in.';
                    } else {
                        $signupError = 'Could not create account. Try again.';
                    }
                    $insert->close();
                } else {
                    $signupError = 'Database error during signup.';
                }
            }
        } else {
            $signupError = 'Database error during signup.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Website</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<!-- Navigation Bar -->
<nav>
  <ul class="navbar">
    <li><a href="#">Home</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Services</a></li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>

<h1>Welcome to Our Collaborative Website!</h1>

<div class="login-container">
    <h1>FreshFold POS</h1>
    
    <?php if ($loginError): ?>
        <p class="error"><?php echo htmlspecialchars($loginError); ?></p>
    <?php elseif ($signupSuccess): ?>
        <p class="success"><?php echo htmlspecialchars($signupSuccess); ?></p>
    <?php endif; ?>

    <!-- LOGIN FORM -->
    <form method="post" style="margin-bottom: 30px;">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required placeholder="cashier1 or email">
        </div>

        <div class="form-group">
            <label for="password">Passcode</label>
            <input type="password" id="password" name="password" required placeholder="your passcode">
        </div>

        <button type="submit" class="login-btn">Login</button>
    </form>

    <!-- SIGNUP FORM -->
    <h2 style="margin-bottom: 10px;">Create a New Account</h2>
    <?php if ($signupError): ?>
        <p class="error"><?php echo htmlspecialchars($signupError); ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="signup">
        <div class="form-group">
            <label for="new_username">New Username</label>
            <input type="text" id="new_username" name="new_username" required>
        </div>
        <div class="form-group">
            <label for="new_email">Email</label>
            <input type="email" id="new_email" name="new_email" required>
        </div>
        <div class="form-group">
            <label for="new_password">Passcode</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="login-btn">Sign Up</button>
    </form>
</div>

<footer>
  <p>&copy; 2025 FreshFold Systems. All rights reserved.</p>
</footer>

</body>
</html>
