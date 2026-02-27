<?php
// login.php - handles login for FreshFold POS
// Checks username "hamza" and password "hamza123"

session_start();  // This lets us remember the user is logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = trim($_POST["password"] ?? '');

    // Your real credentials (hard-coded for now)
    $real_username = "hamza";
    $real_password = "hamza123";

    if ($username === $real_username && $password === $real_password) {
        // Success - remember the user and redirect to POS
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: pos.php");
        exit();
    } else {
        // Wrong credentials - go back with error
        header("Location: index.php?error=wrong");
        exit();
    }
} else {
    // If someone opens login.php directly → send back to login page
    header("Location: index.php");
    exit();
}
?>