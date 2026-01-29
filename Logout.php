<?php
// logout.php

// Start session first to get user data
session_start();

// Include database and security config
include("db.php");
include("security_config.php");

// Initialize security logger
$securityLogger = new SecurityLogger($conn);

// Get user info from session BEFORE destroying it
$user_email = $_SESSION['user_email'] ?? null;
$username = $_SESSION['user_fullname'] ?? 'Unknown User';

// Debug: Check what values we have
error_log("Logout - User Email: " . $user_email);
error_log("Logout - Username: " . $username);

// Log logout event with the actual user data
$securityLogger->logLogout($user_email, $username);

// Clear session
$_SESSION = [];
session_unset();
session_destroy();

header("location: index.php");
exit;
?>