CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

CREATE TABLE IF NOT EXISTS students(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100),
 email VARCHAR(100),
 department VARCHAR(50),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students(name,email,department) VALUES
('Arun','arun@mail.com','CSE'),
('Bala','bala@mail.com','ECE'),
('Charan','charan@mail.com','CSE'),
('Divya','divya@mail.com','EEE'),
('Eswar','eswar@mail.com','MECH'),
('Farah','farah@mail.com','CIVIL');

-- Sorting
SELECT * FROM students ORDER BY name ASC;
SELECT * FROM students ORDER BY created_at DESC;

-- Filtering
SELECT * FROM students WHERE department='CSE';

-- Count
SELECT department, COUNT(*) total FROM students GROUP BY department;