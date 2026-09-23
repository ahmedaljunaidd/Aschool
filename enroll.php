<?php

session_start();

include "includes/connection.php";


/* التأكد أن المستخدم مسجل */

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}


/* التأكد أنه مستخدم عادي */

if ($_SESSION["role"] != "user") {

    die("ليس لديك صلاحية التسجيل");

}


if (!isset($_GET["id"])) {

    die("الكورس غير موجود");

}


$user_id = $_SESSION["user_id"];
$course_id = $_GET["id"];


/* التأكد أن الكورس معتمد */

$sql = "SELECT * FROM courses
        WHERE id = $course_id
        AND status = 'approved'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    die("هذا الكورس غير متاح");

}


/* التأكد أنه لم يسجل مسبقًا */

$sql_check = "SELECT * FROM enrollments
              WHERE user_id = $user_id
              AND course_id = $course_id";

$result_check = mysqli_query($conn, $sql_check);


if (mysqli_num_rows($result_check) > 0) {

    header("Location: user/my_courses.php");
    exit;

}


/* التسجيل */

$sql = "INSERT INTO enrollments
        (user_id, course_id)
        VALUES
        ($user_id, $course_id)";

mysqli_query($conn, $sql);


header("Location: user/my_courses.php");
exit;

?>