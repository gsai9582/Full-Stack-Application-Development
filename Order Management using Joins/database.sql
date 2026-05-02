CREATE DATABASE IF NOT EXISTS order_db;
USE order_db;

CREATE TABLE customers(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100)
);

CREATE TABLE products(
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(100),
 price DECIMAL(10,2)
);

CREATE TABLE orders(
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id INT,
 product_id INT,
 quantity INT,
 order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES customers(id),
 FOREIGN KEY(product_id) REFERENCES products(id)
);

INSERT INTO customers(name) VALUES ('Arun'),('Bala'),('Divya');
INSERT INTO products(product_name,price) VALUES
('Laptop',55000),('Mouse',800),('Keyboard',1500),('Monitor',12000);

INSERT INTO orders(customer_id,product_id,quantity) VALUES
(1,1,1),
(1,2,2),
(2,4,1),
(3,3,3),
(1,4,1),
(2,2,5);

-- JOIN History
SELECT o.id,c.name,p.product_name,o.quantity,p.price,(o.quantity*p.price) total
FROM orders o
JOIN customers c ON o.customer_id=c.id
JOIN products p ON o.product_id=p.id
ORDER BY o.order_date DESC;

-- Highest value order
SELECT MAX(o.quantity*p.price) FROM orders o JOIN products p ON o.product_id=p.id;

-- Most active customer
SELECT customer_id,COUNT(*) FROM orders GROUP BY customer_id ORDER BY COUNT(*) DESC;