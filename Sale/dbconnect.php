<?php
$conn = new mysqli("localhost", "root", "", "freshfold_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
