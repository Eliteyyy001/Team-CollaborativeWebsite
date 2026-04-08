<?php
date_default_timezone_set('America/New_York');
session_start();

require_once "db_connection.php";
require_once "audit_helper.php";

// Save username before destroying session
$user = $_SESSION['cashier_name'] ?? 'Unknown';

// Write logout audit log
add_audit_log($user, "LOGOUT", "System");

// Destroy the session
session_unset();
session_destroy();

// FIXED: Redirect to your actual login file
header("Location: login.php");
exit;
?>
