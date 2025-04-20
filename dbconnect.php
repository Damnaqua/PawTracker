<?php
$servername = "localhost"; 
$username = "root"; // Default for XAMPP
$password = ""; // Default is empty for XAMPP
$database = "alynnvet"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
