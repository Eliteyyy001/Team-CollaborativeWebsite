<?php
date_default_timezone_set('America/New_York');
session_start();

require_once "db_connection.php";
require_once "audit_helper.php";

$user = $_SESSION['cashier_name'] ?? 'Unknown';

add_audit_log($user, "LOGOUT", "System");

session_unset();
session_destroy();

header("Location: login.php");   // Now correct
exit;
?>
