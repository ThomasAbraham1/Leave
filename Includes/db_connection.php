<?php
// Database configuration
$databaseHost = 'localhost';
$databaseName = 'graceerp';
$databaseUsername = 'root';
$databasePassword = '';

// Create a database connection
$conn = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Check if the connection was successful
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Set the character set to UTF-8
mysqli_set_charset($conn, "utf8");
?>
