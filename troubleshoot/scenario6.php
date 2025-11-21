<?php 
$conn = mysqli_connect("localhost","root","","class_db"); 
$id = intval($_GET['id']); /* ginawang integer para safe at maiwasan ang SQL injection*/
$sql = "DELETE FROM students WHERE student_id = $id"; /* use correct column name*/
mysqli_query($conn, $sql); 
?>
