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


if (!isset($_GET["id"])) {

    die("الكورس غير موجود");

}


$user_id = $_SESSION["user_id"];
$course_id = $_GET["id"];


/* التأكد أن الكورس ليس موجودًا */

$sql_check = "SELECT * FROM favorites
              WHERE user_id = $user_id
              AND course_id = $course_id";

$result_check = mysqli_query($conn, $sql_check);


if (mysqli_num_rows($result_check) == 0) {

    $sql = "INSERT INTO favorites
            (user_id, course_id)
            VALUES
            ($user_id, $course_id)";

    mysqli_query($conn, $sql);

}


header("Location: course.php?id=$course_id");
exit;

?>