<?php
$host='localhost:3307'; $user='root'; $pass=''; $db='login_db';
$conn=new mysqli($host,$user,$pass);
if($conn->connect_error){ die("DB Connection Failed: " . $conn->connect_error); }

$sql = file_get_contents(__DIR__ . '/database.sql');
$queries = array_filter(array_map('trim', explode(';', $sql)));

foreach($queries as $query){
    if(!empty($query)){
        if($conn->query($query) === TRUE){
            echo "Query executed successfully<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }
}

$conn->close();
echo "Database import completed.";
?>