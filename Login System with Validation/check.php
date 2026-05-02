<?php
$conn = new mysqli('localhost:3307', 'root', '', 'login_db');
if ($conn->connect_error) die('Connect failed: ' . $conn->connect_error);
$result = $conn->query('SELECT * FROM users');
while ($row = $result->fetch_assoc()) {
    echo $row['username'] . ' / ' . $row['password'] . PHP_EOL;
}
$conn->close();
?>