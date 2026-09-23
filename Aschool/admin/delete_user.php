<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");

    exit();

}


// التحقق من أن المستخدم Admin
if ($_SESSION['role'] != 'admin') {

    echo "ليس لديك صلاحية";

    exit();

}


// أخذ ID المستخدم
$id = $_GET['id'];


// حذف المستخدم
$sql = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $sql)) {

    header("Location: users.php");

    exit();

} else {

    echo "حدث خطأ أثناء حذف المستخدم";

}


?>