<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "project";

$conn = mysqli_connect($host, $user, $password, $database);
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}
