<?php

session_start();

require_once ("../includes/connection.php");

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


// التأكد من تسجيل الدخول
if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


// التأكد أن المستخدم User
if ($_SESSION["role"] != "user") {

    header("Location: ../index.php");
    exit;

}


// رقم المستخدم
$user_id = $_SESSION["user_id"];


// جلب بيانات المستخدم
$sql = "SELECT * FROM users WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("خطأ في قاعدة البيانات");
}

$user = mysqli_fetch_assoc($result);



?>


<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>الملف الشخصي</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>


<body>


<!-- الشريط العلوي -->

<!-- <div class="navbar">

    <h2>My Project</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div> -->


<!-- القائمة الجانبية -->

    
    <div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <!-- <a href="../courses.php">الكورسات</a> -->

        <a href="my_courses.php">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

        <a href="profile.php" class="active">الملف الشخصي</a>

        <a href="edit_profile.php">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">تسجيل الخروج</a>

</div>

<!-- المحتوى -->

<div class="content">

    <a href="dashboard.php" class="btn-back">
        العودة للوحة التحكم
    </a>

    <h1>الملف الشخصي</h1>




    <div class="card">

        <div class="profile-head">

            <?php if ($user["image"] != "") { ?>

                <div class="profile-avatar">
                    <img src="../uploads/profile/<?php echo $user["image"]; ?>"
                         alt="صورة البروفايل">
                </div>

            <?php } else { ?>

                <div class="profile-avatar profile-avatar-empty">
                    👤
                </div>

            <?php } ?>

            <div class="profile-head-info">

                <h2>👤 <?php echo $user["name"]; ?></h2>

             

            </div>

        </div>


        <div class="profile-info">

            <div class="profile-row">
                <strong> الاسم</strong>
                <span><?php echo $user["name"]; ?></span>
            </div>

            <div class="profile-row">
                <strong> البريد الإلكتروني</strong>
                <span><?php echo $user["email"]; ?></span>
            </div>

            <div class="profile-row">
                <strong> الجنس</strong>
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
                <strong> تاريخ الميلاد</strong>
                <span><?php echo $user["birth_date"]; ?></span>
            </div>

            <div class="profile-row">
                <strong> الصلاحية</strong>
                <span><?php echo $user["role"]; ?></span>
            </div>

        </div>


        <div class="profile-actions">

            <a class="button" href="edit_profile.php">
                ⚙️ تعديل البيانات
            </a>

        </div>

    </div>

</div>


</body>

</html>