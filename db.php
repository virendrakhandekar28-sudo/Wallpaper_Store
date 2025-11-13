<?php
// db.php - Database connection file

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';       // Host name
$username = 'root';        // Database username
$password = '';            // Database password
$dbname = 'wallpaper_store'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
