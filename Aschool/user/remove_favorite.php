<?php

session_start();

include "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


$user_id = $_SESSION["user_id"];

$course_id = $_GET["id"];


$sql = "DELETE FROM favorites
        WHERE user_id = $user_id
        AND course_id = $course_id";


mysqli_query($conn, $sql);


header("Location: favorites.php");
exit;

?>