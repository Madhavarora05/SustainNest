<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql305.infinityfree.com";
$username = "if0_37712517";
$password = "Madhav2004";
$dbname = "if0_37712517_SustainNest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
