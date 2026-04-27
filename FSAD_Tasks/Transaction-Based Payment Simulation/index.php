<?php
$host="localhost"; $user="root"; $pass=""; $db="payment_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error . ". Start MySQL in XAMPP, import database.sql (CREATE payment_db; accounts table). Root pass empty?");
}
$msg=""; $type="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $amount=(float)$_POST["amount"];
 $conn->begin_transaction();
 try{
   $res=$conn->query("SELECT balance FROM accounts WHERE role='user' LIMIT 1");
   $bal=$res->fetch_assoc()["balance"];
   if($amount<=0) throw new Exception("Enter valid amount");
   if($bal<$amount) throw new Exception("Insufficient balance");
   $conn->query("UPDATE accounts SET balance=balance-$amount WHERE role='user'");
   $conn->query("UPDATE accounts SET balance=balance+$amount WHERE role='merchant'");
   $conn->commit();
   $msg="Payment Successful! Rs. ".$amount." transferred.";
   $type="success";
 }catch(Exception $e){
   $conn->rollback();
   $msg="Payment Failed: ".$e->getMessage();
   $type="error";
 }
}
$data=$conn->query("SELECT * FROM accounts");
?>
<!DOCTYPE html><html><head><title>Payment Simulation</title>
<link rel="stylesheet" href="style.css"></head><body>
<div class="container">
<h1>Transaction Payment Simulation</h1>
<?php if($msg!=""){ ?><p class="<?php echo $type; ?>"><?php echo $msg; ?></p><?php } ?>
<form method="POST">
<label>Amount</label>
<input type="number" name="amount" step="0.01" required>
<button type="submit">Pay Now</button>
</form>
<h2>Account Balances</h2>
<table><tr><th>ID</th><th>Name</th><th>Role</th><th>Balance</th></tr>
<?php while($r=$data->fetch_assoc()){ ?>
<tr><td><?php echo $r["id"]; ?></td><td><?php echo $r["name"]; ?></td><td><?php echo $r["role"]; ?></td><td>Rs. <?php echo $r["balance"]; ?></td></tr>
<?php } ?>
</table></div></body></html>