<?php
$host = "localhost";
$port = "3307";  
$username = "root";
$password = "";
$dbname = "theplug_db";

$dsn = "mysql:host=$host;port=$port;dbname=$dbname";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];
?>
