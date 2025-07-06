<?php
// Database configuration
define('DB_SERVER', 'localhost'); // Replace with your database server
define('DB_USERNAME', 'root');    // Replace with your database username
define('DB_PASSWORD', '');        // Replace with your database password
define('DB_NAME', 'biogas_db');   // Replace with your database name

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Optional: Set character set to utf8mb4 for better Unicode support
mysqli_set_charset($link, "utf8mb4");

// echo "Database connected successfully."; // For testing connection, remove in production
?>
