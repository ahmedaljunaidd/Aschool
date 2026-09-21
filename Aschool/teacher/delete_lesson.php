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


if ($_SESSION["role"] != "teacher") {
    die("ليس لديك صلاحية");
}


$teacher_id = $_SESSION["user_id"];

$lesson_id = $_GET["id"];


// جلب الدرس
$sql = "SELECT lessons.*, courses.teacher_id
        FROM lessons, courses
        WHERE lessons.id = '$lesson_id'
        AND lessons.course_id = courses.id
        AND courses.teacher_id = '$teacher_id'";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("الدرس غير موجود");
}


$lesson = mysqli_fetch_assoc($result);

$course_id = $lesson["course_id"];


// حذف الدرس
$sql = "DELETE FROM lessons
        WHERE id = '$lesson_id'";


if (mysqli_query($conn, $sql)) {

    // إعادة الدورة للمراجعة
    $sql2 = "UPDATE courses
             SET status = 'pending'
             WHERE id = '$course_id'";

    mysqli_query($conn, $sql2);


    header("Location: lessons.php?course_id=$course_id");
    exit;

} else {

    echo "حدث خطأ: " . mysqli_error($conn);

}

?>