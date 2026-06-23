<?php
// Enable full error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Force login session for this test
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'admin';
$_SESSION['admin_role'] = 'admin';

// Set request ID
$_GET['id'] = 39;

// Include the edit page
include 'edit_house.php';
?>
