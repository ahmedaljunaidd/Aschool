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


if (!isset($_GET["id"])) {

    die("الكورس غير موجود");

}


$course_id = $_GET["id"];


/* عدد الدروس */

$sql = "SELECT COUNT(*) AS total
        FROM lessons
        WHERE course_id = $course_id";

$result = mysqli_query($conn, $sql);

$data = mysqli_fetch_assoc($result);

$total_lessons = $data["total"];


/* عدد الدروس المكتملة */

$sql = "SELECT COUNT(*) AS completed
        FROM lesson_progress
        INNER JOIN lessons
        ON lesson_progress.lesson_id = lessons.id
        WHERE lesson_progress.user_id = $user_id
        AND lessons.course_id = $course_id
        AND lesson_progress.completed = 1";

$result = mysqli_query($conn, $sql);

$data = mysqli_fetch_assoc($result);

$completed_lessons = $data["completed"];


/* حساب النسبة */

if ($total_lessons > 0) {

    $progress = ($completed_lessons / $total_lessons) * 100;

} else {

    $progress = 0;

}

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>تقدم الكورس</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


    
    <div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <!-- <a href="../courses.php">الكورسات</a> -->

        <a href="my_courses.php" class="active">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

        <a href="profile.php">الملف الشخصي</a>

        <a href="edit_profile.php">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">تسجيل الخروج</a>

</div>


<div class="content">

    <a href="my_courses.php" class="btn-back">
        العودة إلى دوراتي
    </a>

    <h1>تقدمك في الكورس</h1>


    <div class="card">

        <h2>
            الدروس المكتملة:
            <?php echo $completed_lessons; ?>
        </h2>


        <h2>
            إجمالي الدروس:
            <?php echo $total_lessons; ?>
        </h2>


        <h2>
            نسبة الإنجاز:
            <?php echo round($progress); ?>%
        </h2>

        <div class="progress-bar">

            <div class="progress-fill"
                 style="width: <?php echo round($progress); ?>%;">
            </div>

        </div>

    </div>


    <br>


    <a class="btn-guide"
       href="course.php?id=<?php echo $course_id; ?>">

        متابعة التعلم

    </a>

</div>


</body>

</html>