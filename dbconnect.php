<?php
$conn = new mysqli("localhost", "root", "", "freshfold_system");

if ($conn->connect_error) {
    die("Connection failed");
}
echo "Connected successfully";
