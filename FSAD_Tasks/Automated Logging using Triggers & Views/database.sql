-- ============================================================
-- Database Mini Project: Automated Logging using Triggers & Views
-- MySQL + PHP + HTML + CSS + JavaScript
-- XAMPP Compatible
-- ============================================================

-- 1. Create Database
CREATE DATABASE IF NOT EXISTS audit_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE audit_db;

-- 2. Create employees Table
CREATE TABLE IF NOT EXISTS employees (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    department VARCHAR(50) NOT NULL,
    designation VARCHAR(50) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    joining_date DATE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Create audit_logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values TEXT,
    new_values TEXT,
    changed_by VARCHAR(100) DEFAULT 'admin',
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1'
) ENGINE=InnoDB;

-- 4. AFTER INSERT Trigger on employees
-- Automatically logs new employee insertions into audit_logs
DELIMITER //
CREATE TRIGGER IF NOT EXISTS trg_after_employee_insert
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (record_id, action_type, new_values, changed_by, changed_at, ip_address)
    VALUES (
        NEW.emp_id,
        'INSERT',
        CONCAT(
            '{"emp_id":', NEW.emp_id,
            ', "first_name":"', NEW.first_name,
            '", "last_name":"', NEW.last_name,
            '", "email":"', NEW.email,
            '", "phone":"', IFNULL(NEW.phone, ''),
            '", "department":"', NEW.department,
            '", "designation":"', NEW.designation,
            '", "salary":', NEW.salary,
            ', "joining_date":"', NEW.joining_date,
            '", "status":"', NEW.status, '"}'
        ),
        COALESCE(@changed_by, 'admin'),
        NOW(),
        COALESCE(@ip_address, '127.0.0.1')
    );
END//
DELIMITER ;

-- 5. AFTER UPDATE Trigger on employees
-- Automatically logs employee updates with old and new values
DELIMITER //
CREATE TRIGGER IF NOT EXISTS trg_after_employee_update
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (record_id, action_type, old_values, new_values, changed_by, changed_at, ip_address)
    VALUES (
        NEW.emp_id,
        'UPDATE',
        CONCAT(
            '{"emp_id":', OLD.emp_id,
            ', "first_name":"', OLD.first_name,
            '", "last_name":"', OLD.last_name,
            '", "email":"', OLD.email,
            '", "phone":"', IFNULL(OLD.phone, ''),
            '", "department":"', OLD.department,
            '", "designation":"', OLD.designation,
            '", "salary":', OLD.salary,
            ', "joining_date":"', OLD.joining_date,
            '", "status":"', OLD.status, '"}'
        ),
        CONCAT(
            '{"emp_id":', NEW.emp_id,
            ', "first_name":"', NEW.first_name,
            '", "last_name":"', NEW.last_name,
            '", "email":"', NEW.email,
            '", "phone":"', IFNULL(NEW.phone, ''),
            '", "department":"', NEW.department,
            '", "designation":"', NEW.designation,
            '", "salary":', NEW.salary,
            ', "joining_date":"', NEW.joining_date,
            '", "status":"', NEW.status, '"}'
        ),
        COALESCE(@changed_by, 'admin'),
        NOW(),
        COALESCE(@ip_address, '127.0.0.1')
    );
END//
DELIMITER ;

-- 6. Create VIEW: daily_activity_report
-- Shows daily summary of inserts, updates, and total activities
CREATE OR REPLACE VIEW daily_activity_report AS
SELECT 
    DATE(changed_at) AS activity_date,
    SUM(CASE WHEN action_type = 'INSERT' THEN 1 ELSE 0 END) AS total_inserts,
    SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) AS total_updates,
    COUNT(*) AS total_activities
FROM audit_logs
GROUP BY DATE(changed_at)
ORDER BY activity_date DESC;

-- 7. Insert Sample Records into employees
INSERT INTO employees (first_name, last_name, email, phone, department, designation, salary, joining_date, status) VALUES
('John', 'Doe', 'john.doe@company.com', '555-0101', 'Engineering', 'Software Engineer', 75000.00, '2023-01-15', 'Active'),
('Jane', 'Smith', 'jane.smith@company.com', '555-0102', 'Marketing', 'Marketing Manager', 85000.00, '2022-08-20', 'Active'),
('Robert', 'Johnson', 'robert.j@company.com', '555-0103', 'Finance', 'Financial Analyst', 68000.00, '2023-03-10', 'Active'),
('Emily', 'Davis', 'emily.davis@company.com', '555-0104', 'HR', 'HR Specialist', 62000.00, '2022-11-05', 'Active'),
('Michael', 'Wilson', 'michael.w@company.com', '555-0105', 'Engineering', 'DevOps Engineer', 82000.00, '2023-06-01', 'Active');

-- 8. Verify the data
SELECT * FROM employees;
SELECT * FROM audit_logs;
SELECT * FROM daily_activity_report;

