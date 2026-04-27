<?php
$host = 'localhost';
$dbname = 'student_db';
$username = 'root';
$password = 'GOwt12@#';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create table if not exists
$db->exec("CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    department VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL
)");

$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $dob=$_POST['dob'];
    $department=$_POST['department'];
    $phone=$_POST['phone'];

    $stmt = $db->prepare("INSERT INTO students(name,email,dob,department,phone) VALUES (?,?,?,?,?)");
    $stmt->execute([$name, $email, $dob, $department, $phone]);
    $msg="Student registered successfully.";
}

$result = $db->query("SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Student Registration Form</h1>
<?php if($msg!=''): ?><p class="msg"><?php echo $msg; ?></p><?php endif; ?>

<form method="POST">
<label>Name</label>
<input type="text" name="name" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Date of Birth</label>
<input type="date" name="dob" required>

<label>Department</label>
<select name="department" required>
<option value="">Select Department</option>
<option>CSE</option>
<option>ECE</option>
<option>EEE</option>
<option>MECH</option>
<option>CIVIL</option>
</select>

<label>Phone</label>
<input type="tel" name="phone" pattern="[0-9]{10}" required>

<button type="submit">Register</button>
</form>

<h2>Registered Students</h2>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>DOB</th><th>Department</th><th>Phone</th>
</tr>
<?php while($row=$result->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
<td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row['dob'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row['department'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
