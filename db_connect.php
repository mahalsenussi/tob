<?php
// db_connect.php
$servername = "localhost"; // Database server
$username = "harmony1_mahmoud";
$password = "7-GACv~bkbq9";
$dbname = "harmony1_tob"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4"); 

?>