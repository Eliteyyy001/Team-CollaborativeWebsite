<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Collaborative Website!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #2c3e50;
            padding: 15px 0;
            text-align: center;
        }
        .navbar a {
            color: white;
            margin: 0 20px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            color: #4CAF50;
        }
        .welcome {
            text-align: center;
            margin: 40px 0 20px;
            font-size: 28px;
            color: #2c3e50;
        }
        .container {
            width: 420px;
            margin: 0 auto;
            background: #fffbeb;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #f4d88c;
        }
        .title {
            text-align: center;
            font-size: 24px;
            color: #d97706;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin: 8px 0 5px;
            font-weight: bold;
            color: #444;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 13px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-btn {
            background: #4CAF50;
            color: white;
        }
        .login-btn:hover {
            background: #45a049;
        }
        .signup-btn {
            background: #4CAF50;
            color: white;
        }
        .signup-btn:hover {
            background: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Contact</a>
    </div>

    <h1 class="welcome">Welcome to Our Collaborative Website!</h1>

    <div class="container">
        <h2 class="title">FreshFold POS</h2>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="admin" value="admin" required>

            <label>Passcode</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <?php if (isset($_SESSION['login_error'])): ?>
                <p class="error"><?= htmlspecialchars($_SESSION['login_error']) ?></p>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <button type="submit" class="btn login-btn">Login</button>
        </form>

        <!-- Go to POS Button (optional, you can remove if you don't want it) -->
        <!-- <a href="pos.php" style="display:block; text-align:center; margin:15px 0; color:#2196F3; text-decoration:none;">Go to POS</a> -->

        <!-- Sign Up Form -->
        <h3 style="margin-top:30px; color:#444;">Create a New Account</h3>
        <form action="signup.php" method="POST">
            <label>New Username</label>
            <input type="text" name="new_username" placeholder="New Username" required>

            <label>Passcode</label>
            <input type="password" name="new_password" placeholder="Passcode" required>

            <button type="submit" class="btn signup-btn">Sign Up</button>
        </form>
    </div>

    <div class="footer">
        © 2025 FreshFold Systems. All rights reserved.
    </div>
</body>
</html>