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


$user_id = $_SESSION["user_id"];

$lesson_id = $_GET["id"];


/* التأكد هل يوجد سجل سابق */

$sql_check = "SELECT * FROM lesson_progress
              WHERE user_id = $user_id
              AND lesson_id = $lesson_id";

$result_check = mysqli_query($conn, $sql_check);


if (mysqli_num_rows($result_check) > 0) {

    /* تحديث السجل */

    $sql = "UPDATE lesson_progress
            SET completed = 1,
                completed_at = NOW()
            WHERE user_id = $user_id
            AND lesson_id = $lesson_id";

} else {

    /* إنشاء سجل جديد */

    $sql = "INSERT INTO lesson_progress
            (user_id, lesson_id, completed, completed_at)
            VALUES
            ($user_id, $lesson_id, 1, NOW())";

}


mysqli_query($conn, $sql);


/* العودة إلى الدرس */

header("Location: lesson.php?id=$lesson_id");
exit;

?>