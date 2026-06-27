<?php
$servername = "localhost";
$username = "news_user";
$password = "news123";
$dbname = "xe";

// Create connection using PDO for Oracle
try {
    // Simple EZCONNECT format
    $conn = new PDO("oci:dbname=//$servername:1521/$dbname", $username, $password);

    // Set error mode silent to match previous mysqli behavior closely 
    // and case_lower to keep array keys lowercase for existing frontend code
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>