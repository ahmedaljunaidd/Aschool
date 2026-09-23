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


if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


if (!isset($_GET["id"])) {

    header("Location: courses.php");
    exit;

}


$id = $_GET["id"];


/* معرفة الدورة التي ينتمي إليها الدرس */

$sql = "SELECT course_id FROM lessons WHERE id = $id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    header("Location: courses.php");
    exit;

}


$lesson = mysqli_fetch_assoc($result);

$course_id = $lesson["course_id"];


/* حذف الدرس */

$sql = "DELETE FROM lessons WHERE id = $id";

mysqli_query($conn, $sql);


/* العودة إلى دروس الدورة */

header("Location: lessons.php?course_id=$course_id");

exit;

?>