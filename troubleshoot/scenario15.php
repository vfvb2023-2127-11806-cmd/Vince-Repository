<?php 
$page = intval($_GET['page']); /* para ma convert sa integer*/
if($page < 0) $page = 0; /* limit ang minimum page number*/
$limit = 5; 
$offset = $page * $limit; 
$sql = "SELECT * FROM students LIMIT $offset, $limit"; 
?>
