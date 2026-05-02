CREATE DATABASE IF NOT EXISTS payment_db;
USE payment_db;
CREATE TABLE accounts(
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
role VARCHAR(20),
balance DECIMAL(10,2)
);
INSERT INTO accounts(name,role,balance) VALUES
('Arun User','user',5000.00),
('Shop Merchant','merchant',2000.00);
START TRANSACTION;
UPDATE accounts SET balance=balance-1000 WHERE role='user';
UPDATE accounts SET balance=balance+1000 WHERE role='merchant';
COMMIT;