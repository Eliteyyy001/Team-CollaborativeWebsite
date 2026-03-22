<?php
date_default_timezone_set('America/New_York');
session_start();
require_once "audit_helper.php";

// Save username before destroying session
$user = $_SESSION['cashier_name'] ?? 'Unknown';

// Write logout log
add_audit_log($user, "LOGOUT", "System");

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
