<?php

session_start();


if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "user") {

    die("ليس لديك صلاحية");

}

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>لوحة المستخدم</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


    <div class="navbar">

    <h2> التعليمية Aschool</h2>

    <div class="dash-nav">

        <a href="dashboard.php" class="active">الرئيسية</a>

        <!-- <a href="../courses.php">الكورسات</a> -->

        <a href="my_courses.php">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

        <a href="profile.php">الملف الشخصي</a>

        <a href="edit_profile.php">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">تسجيل الخروج</a>

</div>


<div class="content">

    <a href="../index.php" class="btn-back">
        العودة إلى الرئيسية
    </a>

    <h1>
        مرحبًا
        <?php echo $_SESSION["name"]; ?>
    </h1>


    <div class="cards">


        <div class="stat-card">

            <div class="stat-card-icon icon-blue">📚</div>

            <div class="stat-card-body">

                <span class="stat-card-title">تصفح المنصة</span>

                <strong class="stat-card-value">الكورسات</strong>

                <a class="stat-card-link" href="../courses.php">
                    مشاهدة الكورسات ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-green">🎓</div>

            <div class="stat-card-body">

                <!-- <span class="stat-card-title">الكورسات المسجل فيها</span> -->

                <strong class="stat-card-value">دوراتي</strong>

                <a class="stat-card-link" href="my_courses.php">
                    متابعة التقدم ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-amber">⭐</div>

            <div class="stat-card-body">

                <!-- <span class="stat-card-title">الكورسات المحفوظة</span> -->

                <strong class="stat-card-value">المفضلة</strong>

                <a class="stat-card-link" href="favorites.php">
                    عرض المفضلة ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-violet">👤</div>

            <div class="stat-card-body">

                <!-- <span class="stat-card-title">بيانات حسابك</span> -->

                <strong class="stat-card-value">الملف الشخصي</strong>

                <a class="stat-card-link" href="profile.php">
                    عرض الملف ←
                </a>

            </div>

        </div>


    </div>

</div>


</body>

</html>