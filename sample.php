<?php 
include 'prepared_statements.php';

$conn = mysqli_connect("localhost","root","","hello",3306);
$query  = "INSERT INTO table (name,message,time) VALUES (hello,world,5)";

prepared_mysqli_query($conn,$query);
?>

