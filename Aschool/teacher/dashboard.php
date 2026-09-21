<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "teacher") {

    header("Location: ../index.php");
    exit;

}


$teacher_id = $_SESSION["user_id"];


/* عدد دورات المدرس */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id";

$result = mysqli_query($conn, $sql);

$courses = mysqli_fetch_assoc($result);


/* الدورات المقبولة */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id
        AND status = 'approved'";

$result = mysqli_query($conn, $sql);

$approved = mysqli_fetch_assoc($result);


/* الدورات المنتظرة */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id
        AND status = 'pending'";

$result = mysqli_query($conn, $sql);

$pending = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>لوحة المدرس</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>لوحة المدرس</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="sidebar">

    <h2>لوحة المدرس</h2>

    <div class="sidebar-section-title">القائمة الرئيسية</div>

    <a href="dashboard.php" class="active">
        🏠 الرئيسية
    </a>

    <a href="courses.php">
        📚 دوراتي
    </a>

    <div class="sidebar-section-title">المنصة</div>

    <a href="../courses.php">
        🌐 المنصة
    </a>

    <div class="sidebar-footer">
        منصة <strong>Aschool</strong> التعليمية
    </div>

</div>


<div class="content">

    <a href="../index.php" class="btn-back">
        العودة إلى الرئيسية
    </a>

    <h1>
        مرحباً <?php echo $_SESSION["name"]; ?> 👋
    </h1>

    <p>
        لوحة تحكم المدرس
    </p>


    <div class="cards">


        <div class="stat-card">

            <div class="stat-card-icon icon-blue">📚</div>

            <div class="stat-card-body">

                <span class="stat-card-title">جميع دوراتي</span>

                <strong class="stat-card-value">
                    <?php echo $courses["total"]; ?>
                </strong>

                <a class="stat-card-link" href="courses.php">
                    عرض الدورات ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-green">✅</div>

            <div class="stat-card-body">

                <span class="stat-card-title">الدورات المقبولة</span>

                <strong class="stat-card-value">
                    <?php echo $approved["total"]; ?>
                </strong>

                <a class="stat-card-link" href="courses.php">
                    عرض الدورات ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-amber">⏳</div>

            <div class="stat-card-body">

                <span class="stat-card-title">قيد المراجعة</span>

                <strong class="stat-card-value">
                    <?php echo $pending["total"]; ?>
                </strong>

                <a class="stat-card-link" href="courses.php">
                    متابعة الحالة ←
                </a>

            </div>

        </div>


    </div>

</div>

</body>

</html>