<?php
// index.php - main entry point (shows login form or redirects)
session_start();

// If already logged in, go to POS
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: pos.php");
    exit();
}

// Otherwise show login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold POS - Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .subtitle {
            color: #555;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 15px;
        }
        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 16px;
            font-size: 18px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-btn:hover {
            background: #219653;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>FreshFold POS</h1>
        <p class="subtitle">Sign in to start selling</p>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'wrong'): ?>
            <div class="error">Wrong username or password!</div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="hamza">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="hamza123">
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>