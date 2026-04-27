<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-box">
<h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>
<p class="success">Login Successful</p>
<a href="logout.php"><button>Logout</button></a>
</div>
</body>
</html>