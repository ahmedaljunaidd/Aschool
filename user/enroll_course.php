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
    die("ليس لديك صلاحية التسجيل");
}

$user_id = (int) $_SESSION["user_id"];

$course_id = isset($_POST["course_id"]) ? (int) $_POST["course_id"] : (isset($_GET["id"]) ? (int) $_GET["id"] : 0);

if ($course_id <= 0) {
    header("Location: browse.php");
    exit;
}

/* التأكد أن الكورس معتمد */
$result = mysqli_query($conn, "SELECT id FROM courses WHERE id = $course_id AND status IN ('active', 'approved') LIMIT 1");

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: course_detail.php?id=$course_id&error=unavailable");
    exit;
}

/* التأكد أنه لم يسجل مسبقًا */
$check = mysqli_query($conn, "SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id LIMIT 1");

if (!$check || mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO enrollments (user_id, course_id) VALUES ($user_id, $course_id)");
}

header("Location: course.php?id=$course_id");
exit;

?>