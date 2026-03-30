<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "owa_db";
$port = 3307; // IMPORTANT: your MySQL port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>