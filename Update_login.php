<?php
date_default_timezone_set('America/New_York');
session_start();

require_once "db_connection.php";
require_once "audit_helper.php";

// Save username 
$user = $_SESSION['cashier_name'] ?? 'Unknown';

// logout audit log
add_audit_log($user, "LOGOUT", "System");

// Destroy the session
session_unset();
session_destroy();

// actual login file
header("Location: login.php");
exit;
?>
