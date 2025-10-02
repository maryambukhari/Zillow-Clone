<?php
$host = 'localhost';
$dbname = 'dbehc3iw1mx66c';
$username = 'uasxxqbztmxwm';
$password = 'wss863wqyhal';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
