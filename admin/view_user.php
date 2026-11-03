<?php

session_start();

require_once "../includes/connection.php";
include '../includes/icons.php';

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


/* التأكد من تسجيل الدخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/* التأكد أنه Admin */

if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* التأكد من رقم المستخدم */

if (!isset($_GET["id"])) {

    header("Location: users.php");
    exit;

}


$user_id = $_GET["id"];


$sql = "SELECT * FROM users WHERE id = $user_id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    header("Location: users.php");
    exit;

}


$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>عرض المستخدم</title>

    <link rel="stylesheet" href="../css/style.css?v=29">
<link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">

</head>

<body class="theme-dark shell-page">

<script>
(function () {
    try {
        if (localStorage.getItem('aschool.theme') === 'light') {
            document.body.classList.remove('theme-dark');
            document.body.classList.add('theme-light');
        }
    } catch (e) { /* ignore */ }
})();
</script>


<div class="navbar">

    <h2>لوحة الإدارة</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>




<div class="content">

    <a href="users.php" class="btn-back">
        العودة إلى إدارة المستخدمين
    </a>

    <h1>عرض المستخدم</h1>

    <div class="card">

        <div class="profile-head">

            <?php if ($user["image"] != "") { ?>

                <div class="profile-avatar">
                    <img src="../uploads/profile/<?php echo $user["image"]; ?>"
                         alt="صورة المستخدم">
                </div>

            <?php } else { ?>

                <div class="profile-avatar profile-avatar-empty">
                    👤
                </div>

            <?php } ?>

            <div class="profile-head-info">

                <h2>👤 <?php echo $user["name"]; ?></h2>

                <p>عرض لبيانات الحساب — للقراءة فقط</p>

            </div>

        </div>


        <div class="profile-info">

            <div class="profile-row">
                <strong> <?php echo icon('') ?> رقم المستخدم</strong>
                <span><?php  echo $user["id"]; ?></span>
            </div>

            <div class="profile-row">
                <strong> <?php echo icon('user') ?> الاسم</strong>
                <span><?php echo $user["name"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>  <?php echo icon('mail') ?> البريد الإلكتروني</strong>
                <span><?php echo $user["email"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>⚧ الجنس</strong>
                <span>
                    <?php
                    if ($user["gender"] == "man") {
                        echo "ذكر";
                    } elseif ($user["gender"] == "women") {
                        echo "أنثى";
                    } else {
                        echo $user["gender"];
                    }
                    ?>
                </span>
            </div>

            <div class="profile-row">
                <strong> <?php echo icon('calendar') ?> تاريخ الميلاد</strong>
                <span><?php echo $user["birth_date"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>🔰 الصلاحية</strong>
                <span><?php echo $user["role"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>✅ الحالة</strong>
                <span><?php echo $user["status"]; ?></span>
            </div>

        </div>


        <div class="profile-actions">

            <a class="btn-ghost"
               href="edit_user.php?id=<?php echo $user["id"]; ?>">
                تعديل
            </a>

            <a class="btn btn-danger btn-sm"
               href="delete_user.php?id=<?php echo $user["id"]; ?>"
               onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                حذف
            </a>
            
    <a href="users.php" class="btn-ghost">
       → عودة 
    </a>
        </div>

    </div>

</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>