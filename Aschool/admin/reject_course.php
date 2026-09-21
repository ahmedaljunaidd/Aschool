<?php

session_start();

include "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



/* التأكد أن المستخدم أدمن */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {

    header("Location: ../login.php");
    exit;

}


/* التأكد من وجود رقم الكورس */
if (!isset($_GET["id"])) {

    die("رقم الكورس غير موجود");

}


$course_id = $_GET["id"];


/* تغيير حالة الكورس إلى rejected */
$sql = "UPDATE courses
        SET status = 'rejected'
        WHERE id = $course_id
        AND status = 'pending'";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die("حدث خطأ: " . mysqli_error($conn));

}


/* العودة إلى صفحة الطلبات */
header("Location: course_requests.php");
exit;

?>