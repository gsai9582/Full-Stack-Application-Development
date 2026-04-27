<?php
session_start();
$host='localhost:3307'; $user='root'; $pass=''; $db='login_db';
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){ die("DB Connection Failed"); }

$error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $username=$_POST['username'];
    $password=$_POST['password'];

    $stmt=$conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss",$username,$password);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows>0){
        $_SESSION['user']=$username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error="Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login System</title>
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>
<body>
<div class="login-box">
<h1>Login</h1>
<form method="POST" onsubmit="return validateForm()">
<input type="text" id="username" name="username" placeholder="Username">
<span id="userError" class="error"></span>

<input type="password" id="password" name="password" placeholder="Password">
<span id="passError" class="error"></span>

<?php if($error!=''): ?><p class="error center"><?php echo $error; ?></p><?php endif; ?>

<button type="submit">Login</button>
</form>
</div>
</body>
</html>