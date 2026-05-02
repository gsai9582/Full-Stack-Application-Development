<?php
$host='localhost'; $user='root'; $pass=''; $db='order_db';
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){ die("DB Connection Failed"); }

$history=$conn->query("
SELECT o.id,c.name customer,p.product_name,o.quantity,p.price,
(o.quantity*p.price) total,o.order_date
FROM orders o
JOIN customers c ON o.customer_id=c.id
JOIN products p ON o.product_id=p.id
ORDER BY o.order_date DESC
");

$highest=$conn->query("
SELECT c.name customer,p.product_name,(o.quantity*p.price) total
FROM orders o
JOIN customers c ON o.customer_id=c.id
JOIN products p ON o.product_id=p.id
WHERE (o.quantity*p.price)=(
 SELECT MAX(o2.quantity*p2.price)
 FROM orders o2
 JOIN products p2 ON o2.product_id=p2.id
)
LIMIT 1
")->fetch_assoc();

$active=$conn->query("
SELECT name,total_orders FROM customers
WHERE id=(
 SELECT customer_id FROM orders
 GROUP BY customer_id
 ORDER BY COUNT(*) DESC
 LIMIT 1
)
")->fetch_assoc();

$count=$conn->query("
SELECT customer_id,COUNT(*) total_orders FROM orders GROUP BY customer_id ORDER BY COUNT(*) DESC LIMIT 1
")->fetch_assoc();
if($active){ $active['total_orders']=$count['total_orders'];}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Order Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Order Management Dashboard</h1>

<div class="cards">
<div class="card">
<h3>Highest Value Order</h3>
<p><?= $highest['customer'] ?></p>
<p><?= $highest['product_name'] ?></p>
<strong>₹<?= $highest['total'] ?></strong>
</div>

<div class="card">
<h3>Most Active Customer</h3>
<p><?= $active['name'] ?></p>
<strong><?= $active['total_orders'] ?> Orders</strong>
</div>
</div>

<h2>Customer Order History</h2>
<table>
<tr>
<th>Order ID</th><th>Customer</th><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th>
</tr>
<?php while($row=$history->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['customer'] ?></td>
<td><?= $row['product_name'] ?></td>
<td><?= $row['quantity'] ?></td>
<td>₹<?= $row['price'] ?></td>
<td>₹<?= $row['total'] ?></td>
<td><?= $row['order_date'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>