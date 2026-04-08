<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // YOUR CUSTOM LOGIN CREDENTIALS
    if ($username === "hamza" && $password === "hamza123") {
        $_SESSION['logged_in'] = true;
        $_SESSION['cashier_name'] = "Hamza";
        header("Location: pos.php"); // redirect to POS page
        exit;
    } else {
        $error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>FreshFold POS Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 30px;
            width: 320px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.15);
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-btn:hover {
            background: #45a049;
        }
        .error {
            color: red;
            margin-top: 12px;
            font-weight: bold;
        }
        .test-info {
            margin-top: 15px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>FreshFold POS</h2>

    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="login-btn" type="submit">Login</button>
    </form>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <div class="test-info">
        Test Login: hamza / hamza123
    </div>
</div>

</body>
</html>
