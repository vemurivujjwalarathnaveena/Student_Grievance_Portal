<?php

$conn = mysqli_connect(
    "sql202.infinityfree.com",
    "if0_41871355",
    "Vujjwalavemuri",
    "if0_41871355_grievance"
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}
?>