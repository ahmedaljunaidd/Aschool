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
$lesson_id = isset($_POST["lesson_id"]) ? (int) $_POST["lesson_id"] : (isset($_GET["id"]) ? (int) $_GET["id"] : 0);

if ($lesson_id <= 0) {
    header("Location: my_courses.php");
    exit;
}

/* جلب الدرس */
$result = mysqli_query($conn, "SELECT * FROM lessons WHERE id = $lesson_id LIMIT 1");

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: my_courses.php");
    exit;
}

$lesson = mysqli_fetch_assoc($result);
$course_id = (int) $lesson["course_id"];

/* التأكد أن المستخدم مسجل في الكورس */
$check = mysqli_query($conn, "SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id LIMIT 1");

if (!$check || mysqli_num_rows($check) == 0) {
    header("Location: course_detail.php?id=$course_id");
    exit;
}

/* تعليم الدرس كمكتمل — بدون فهرس فريد، لذلك نتحقق أولاً */
$progress = mysqli_query($conn, "SELECT id FROM lesson_progress WHERE user_id = $user_id AND lesson_id = $lesson_id LIMIT 1");

if ($progress && mysqli_num_rows($progress) > 0) {
    mysqli_query($conn, "UPDATE lesson_progress SET completed = 1, completed_at = NOW() WHERE user_id = $user_id AND lesson_id = $lesson_id");
} else {
    mysqli_query($conn, "INSERT INTO lesson_progress (user_id, lesson_id, completed, completed_at) VALUES ($user_id, $lesson_id, 1, NOW())");
}

/* الدرس التالي حسب الترتيب */
$next = mysqli_query($conn,
    "SELECT id FROM lessons
     WHERE course_id = $course_id
     AND (lesson_order > " . (int) $lesson["lesson_order"] . "
          OR (lesson_order = " . (int) $lesson["lesson_order"] . " AND id > $lesson_id))
     ORDER BY lesson_order ASC, id ASC
     LIMIT 1");

if ($next && mysqli_num_rows($next) > 0) {
    $row = mysqli_fetch_assoc($next);
    header("Location: lesson.php?id=" . (int) $row["id"]);
    exit;
}

/* هل أكمل الطالب كل دروس الدورة؟ */
$remaining = mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM lessons l
     WHERE l.course_id = $course_id
     AND l.id NOT IN (
         SELECT lesson_id FROM lesson_progress
         WHERE user_id = $user_id AND completed = 1
     )");
$remaining_count = 0;
if ($remaining) {
    $row = mysqli_fetch_assoc($remaining);
    $remaining_count = (int) $row["c"];
}

header("Location: course.php?id=$course_id" . ($remaining_count === 0 ? "&done=1" : ""));
exit;

?>