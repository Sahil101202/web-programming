<?php

$host = "mysql";
$username = "sahil";
$password = "101202"; 
$dbname = "sportz_car";

$conn = mysqli_connect($host, $username, $password, $dbname);

if ($conn == False){
    echo "NOT ABLE TO CONNECT WITH DATABASE";
}

?>