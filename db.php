<?php
$conn = mysqli_connect("localhost", "root", "", "paulodb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

 echo "Connected successfully";
?>