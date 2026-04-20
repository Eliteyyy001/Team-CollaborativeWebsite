<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "freshfold_pos"; // <-- CHANGE THIS TO YOUR REAL DATABASE NAME

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
