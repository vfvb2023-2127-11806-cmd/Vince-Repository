<?php
$conn = mysqli_connect("localhost", "root", "", "class_db"); 

$id = $_POST['id'];  /* mali po ang "$_POST", dapat "$_GET"*/
                     /* nasa name na po ng scenario 1 yung possible na sagot which is the "$_GET" and triny ko baguhin*/

$sql = "SELECT * FROM students WHERE id = $id"; /*mali po ang "id = $id", dapat "student_id = $id"*/
                                                /* "student_id" yung nakalagay sa db hinde "id"*/
$res = mysqli_query($conn, $sql); 
$r = mysqli_fetch_assoc($res); 

echo $r['first_name']; 
?>

<?php 
$conn = mysqli_connect("localhost", "root", "", "class_db"); 

$id = $_GET['id'];  

$sql = "SELECT * FROM students WHERE student_id = $id"; 
$res = mysqli_query($conn, $sql); 
$r = mysqli_fetch_assoc($res); 

echo $r['first_name']; 
?>