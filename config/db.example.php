<?php
// Copy this file to db.php and fill in your details
$host = 'localhost';
$db   = 'prms';        // your database name
$user = 'root';        // your MySQL username
$pass = '';            // your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>