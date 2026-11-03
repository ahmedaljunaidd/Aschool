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

if ($_SESSION["role"] != "user") {
    die("ليس لديك صلاحية");
}

$user_id = (int) $_SESSION["user_id"];

$course_id = isset($_POST["course_id"]) ? (int) $_POST["course_id"] : (isset($_GET["id"]) ? (int) $_GET["id"] : 0);

if ($course_id <= 0) {
    header("Location: favorites.php");
    exit;
}

$return = isset($_POST["return"]) ? (string) $_POST["return"] : (isset($_GET["return"]) ? (string) $_GET["return"] : 'favorites.php');

/* السماح فقط بالعودة إلى صفحات الطالب */
$return = str_replace(array("\r", "\n", "\\"), '', $return);
if (!preg_match('~^(browse|course_detail|course|favorites|my_courses|dashboard)\.php(\?[A-Za-z0-9_=&%\-]*)?$~', $return)) {
    $return = 'favorites.php';
}

$exists = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id = $user_id AND course_id = $course_id LIMIT 1");

if ($exists && mysqli_num_rows($exists) > 0) {
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $user_id AND course_id = $course_id");
} else {
    mysqli_query($conn, "INSERT INTO favorites (user_id, course_id) VALUES ($user_id, $course_id)");
}

header("Location: " . $return);
exit;

?>