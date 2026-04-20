<?php
session_start();
require_once __DIR__ . '/freshfoldDatabase/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Username and password are required.";
    header("Location: index.php");
    exit;
}

// Correct query - using 'password' column as shown in your database
$sql = "SELECT * FROM users WHERE userName = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['login_error'] = "Database error. Please try again.";
    header("Location: index.php");
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Compare with the actual column name 'password'
    if ($user['password'] === $password) {
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['userName'] = $user['userName'];
        
        unset($_SESSION['login_error']);
        
        header("Location: pos.php");
        exit;
    }
}

// Login failed
$_SESSION['login_error'] = "Invalid username or password.";
header("Location: index.php");
exit;
?>