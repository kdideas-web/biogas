<?php
if (session_status() == PHP_SESSION_NONE) { // Start session if not already started
    session_start();
}

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("location: login.php?auth_required=true");
    exit;
}
?>
