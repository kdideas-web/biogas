<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
if (session_destroy()) {
    // Redirect to login page with a logged_out message
    header("location: login.php?logged_out=true");
    exit;
} else {
    // If session destroy fails, still try to redirect, maybe with an error
    header("location: login.php?logout_error=true"); // You can handle this GET param on login if needed
    exit;
}
?>
