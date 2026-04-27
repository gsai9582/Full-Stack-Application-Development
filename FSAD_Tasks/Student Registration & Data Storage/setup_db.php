<?php
$host = 'localhost';
$username = 'root';
$password = 'GOwt12@#';

try {
    // Connect without database
    $db = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $db->exec("CREATE DATABASE IF NOT EXISTS student_db CHARACTER SET utf8 COLLATE utf8_general_ci");
    $db->exec("USE student_db");
    
    // Drop and recreate table to ensure correct schema
    $db->exec("DROP TABLE IF EXISTS students");
    
    // Create table
    $db->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        dob DATE NOT NULL,
        department VARCHAR(50) NOT NULL,
        phone VARCHAR(15) NOT NULL
    )");
    
    // Insert sample data
    $stmt = $db->prepare("INSERT INTO students (name, email, dob, department, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Gowtham Sai Garnepudi', 'gsai9582@gmail.com', '2005-05-29', 'CSE', '8074943985']);
    
    echo "Database setup completed successfully!";
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>

