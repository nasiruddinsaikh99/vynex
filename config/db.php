<?php
// Database configuration
$servername = "localhost";
$username = "vynex";
$password = "vyn3x#!";
$dbname = "vynex";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
