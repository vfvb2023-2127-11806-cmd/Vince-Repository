<?php
/* Nakalimutan ilagay ang $conn sa mysqli_query.*/
/*dinagdag ang $conn.*/
$conn = mysqli_connect("localhost", "root", "", "class_db");

$sql = "SELECT * FROM students";
$result = mysqli_query($conn, $sql);
?>