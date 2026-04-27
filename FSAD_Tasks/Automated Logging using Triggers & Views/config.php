<?php
/**
 * Database Configuration File
 * XAMPP Compatible - Update credentials as per your setup
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Default XAMPP MySQL user
define('DB_PASS', '');          // Default XAMPP MySQL password is empty
define('DB_NAME', 'audit_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to handle special characters
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session variables for triggers (changed_by and ip_address)
$_SESSION['changed_by'] = $_SESSION['username'] ?? 'admin';
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Set MySQL session variables for triggers
$changed_by = $conn->real_escape_string($_SESSION['changed_by']);
$ip_address = $conn->real_escape_string($_SESSION['ip_address']);
$conn->query("SET @changed_by = '{$changed_by}'");
$conn->query("SET @ip_address = '{$ip_address}'");
?>

