<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "teacher") {

    header("Location: ../index.php");
    exit;

}


if (!isset($_GET["id"])) {

    header("Location: courses.php");
    exit;

}


$id = $_GET["id"];

$teacher_id = $_SESSION["user_id"];


$sql = "DELETE FROM courses

        WHERE id = $id

        AND teacher_id = $teacher_id";


mysqli_query($conn, $sql);


header("Location: courses.php");

exit;

?>