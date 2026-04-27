CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

CREATE TABLE IF NOT EXISTS students (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL,
 dob DATE NOT NULL,
 department VARCHAR(50) NOT NULL,
 phone VARCHAR(15) NOT NULL
);

INSERT INTO students (name, email, dob, department, phone) VALUES
('Gowtham Sai Garnepudi', 'gsai9582@gmail.com', '2005-05-29', 'CSE', '8074943985');

-- Retrieve data
SELECT * FROM students;
