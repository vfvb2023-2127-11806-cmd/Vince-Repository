<?php 
$conn = mysqli_connect("localhost","root","","class_db"); 
$age = $_GET['age'];

$stmt = $conn->prepare("SELECT * FROM students WHERE age = ?"); /* prevents SQL injection by using a placeholder*/
$stmt->bind_param("i", $age); /* bind as integer to avoid malicious input*/
$stmt->execute();

$res = $stmt->get_result();
?>
