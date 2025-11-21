<?php 
$id = intval($_GET['id']); /* kinoconvert sa integer para safe at hindi na kailangan ng quotes*/
$sql = "SELECT * FROM students WHERE student_id = $id"; /* tinanggal ang quotes dahil integer ang column*/
?>
