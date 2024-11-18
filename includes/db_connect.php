<?php
$servername = "sql305.infinityfree.com"; // Updated server name
$username = "if0_37712517"; // Your MySQL username
$password = "Madhav2004"; // Your MySQL password
$dbname = "if0_37712517_SustainNest"; // The database for your project

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
