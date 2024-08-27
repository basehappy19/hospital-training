<?php
$dsn = 'mysql:host=db;dbname=hospital-training;charset=utf8';
$username = "user";
$password = "password";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
