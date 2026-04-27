<?php
$host='localhost'; $user='root'; $pass='GOwt12@#'; $db='student_db';
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){die("DB Connection Failed");}

$sort = $_GET['sort'] ?? 'name';
$dept = $_GET['department'] ?? '';

$order = ($sort=='date') ? 'created_at DESC' : 'name ASC';

$where='';
if($dept!=''){
  $safe=$conn->real_escape_string($dept);
  $where="WHERE department='$safe'";
}

$sql="SELECT * FROM students $where ORDER BY $order";
$result=$conn->query($sql);

$countSql="SELECT department, COUNT(*) total FROM students GROUP BY department";
$countResult=$conn->query($countSql);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Data Retrieval Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Student Dashboard</h1>

<form method="GET" class="filters">
<select name="sort">
<option value="name" <?= $sort=='name'?'selected':'' ?>>Sort by Name</option>
<option value="date" <?= $sort=='date'?'selected':'' ?>>Sort by Date</option>
</select>

<select name="department">
<option value="">All Departments</option>
<option value="CSE" <?= $dept=='CSE'?'selected':'' ?>>CSE</option>
<option value="ECE" <?= $dept=='ECE'?'selected':'' ?>>ECE</option>
<option value="EEE" <?= $dept=='EEE'?'selected':'' ?>>EEE</option>
<option value="MECH" <?= $dept=='MECH'?'selected':'' ?>>MECH</option>
<option value="CIVIL" <?= $dept=='CIVIL'?'selected':'' ?>>CIVIL</option>
</select>

<button type="submit">Apply</button>
</form>

<h2>Student Records</h2>
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Created Date</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['department'] ?></td>
<td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<h2>Count of Students per Department</h2>
<table>
<tr><th>Department</th><th>Total Students</th></tr>
<?php while($r=$countResult->fetch_assoc()): ?>
<tr>
<td><?= $r['department'] ?></td>
<td><?= $r['total'] ?></td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>