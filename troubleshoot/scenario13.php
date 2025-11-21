<?php 
$newEmail = $_POST['email']; 
$id = $_POST['id']; 
$sql = "UPDATE students SET email='$newEmail' WHERE student_id=$id"; /*nag add ng WHERE*/ 
mysqli_query($conn,$sql); 
?>
