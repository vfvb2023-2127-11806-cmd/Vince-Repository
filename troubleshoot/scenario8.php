<?php
$conn = mysqli_connect("localhost","root","","class_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* nagrun ng query for all students*/
$res = mysqli_query($conn, "SELECT * FROM students");

if (mysqli_num_rows($res) > 0) {
    /* gumamit ng condition para ma display ang mga email*/
    while ($row = mysqli_fetch_assoc($res)) {
        echo "Email: " . $row['email'] . "<br>";
    }
} else {
    echo "No students found.";
}

/* masara ang connection*/ 
mysqli_close($conn);

?>