<?php
$SERVER = "localhost"; 
$USERNAME = "root";
$PASSWORD = "";    
$DATABASE = "travel_agency";

$conn = new mysqli($SERVER, $USERNAME, $PASSWORD, $DATABASE);

if ($conn->connect_error) {
    echo "Failed to connect to MySQL: ".$conn ->connect_errno."".$conn->connect_error;
    exit();
}
?>