<?php

// // MySQLi Object-Oriented
if (!defined("START_CONFIG") || START_CONFIG !== true) die("Not Allowed");

$servername = "localhost";
$uname = "id19400222_root";
$upass = "Pd9SSL3guSntsai-";
$dbname = "id19400222_login_system";

$conn = new mysqli($servername, $uname, $upass, $dbname);
if ($conn->connect_error) {
  die("Connection Failed Bro." . $conn->connect_error);
}
// echo "Connection Established <br>";
