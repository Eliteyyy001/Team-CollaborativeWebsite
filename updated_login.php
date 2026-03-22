<?php

date_default_timezone_set('America/New_York');

session_start();
require_once "audit_helper.php";   // ← MUST BE AT THE TOP

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Your login check
    if ($username === 'Jane Smith' && $password === 'hamza123') {

        // Set session
        $_SESSION['logged_in']    = true;
        $_SESSION['cashier_name'] = $username;

        // Write login log with current time
        add_audit_log($_SESSION['cashier_name'], "LOGIN", "System");

        // Redirect
        header("Location: pos.php");
        exit;
    }

    else {
        $error = "Wrong username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshFold POS - Login</title>
  <style>
    body { margin:0; font-family:Arial,sans-serif; background:#f0f2f5; height:100vh; display:flex; align-items:center; justify-content:center; }
    .login-box { background:white; padding:40px; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.15); width:380px; text-align:center; }
    h2 { color:#2c3e50; margin-bottom:25px; }
    input { width:100%; padding:12px; margin:10px 0; border:1px solid #ccd0d5; border-radius:6px; font-size:16px; box-sizing:border-box; }
    button { width:100%; padding:12px; background:#27ae60; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
    button:hover { background:#219653; }
    .error { color:#c0392b; margin:12px 0; font-size:14px; }
  </style>
</head>
<body>
<div class="login-box">
  <h2>FreshFold POS</h2>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="text" name="username" placeholder="Username" required autofocus>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p style="margin-top:20px;color:#777;font-size:13px;">Test: Jane Smith / hamza123</p>
</div>
</body>
</html>
