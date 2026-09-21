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


if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* جلب الدورات */

$sql = "SELECT * FROM courses ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة الدورات</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>لوحة الإدارة</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="sidebar">

        <h2>لوحة الإدارة</h2>

        <div class="sidebar-section-title">القائمة الرئيسية</div>

        <a href="dashboard.php">
            🏠 الرئيسية
        </a>

        <a href="users.php">
            👥 المستخدمون
        </a>

        <div class="sidebar-section-title">المحتوى</div>

        <a href="categories.php">
            📂 التصنيفات
        </a>

        <a href="courses.php" class="active">
            📚 الدورات
        </a>

        <a href="course_requests.php">
            ⏳ طلبات المراجعة
        </a>

        <div class="sidebar-section-title">النظام</div>

        <a href="settings.php">
            ⚙️ الإعدادات
        </a>

        <div class="sidebar-footer">
            منصة <strong>Aschool</strong> التعليمية
        </div>

    </div>


<div class="content">

    <a href="dashboard.php" class="btn-back">
        العودة إلى لوحة التحكم
    </a>

    <div class="entry-head">

        <h1>إدارة الدورات</h1>

        <a class="button" href="add_course.php">
            ➕ إضافة دورة
        </a>

    </div>

    <p class="welcome">
        إضافة وتعديل وحذف الدورات التعليمية
    </p>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دورات حاليًا.
        </div>

    <?php

    }

    ?>


    <div class="entry-list">

    <?php

    while ($course = mysqli_fetch_assoc($result)) {

    ?>

        <div class="entry-item">

            <div class="entry-body">

                <span class="entry-title">
                    <?php echo $course["title"]; ?>
                </span>

                <span class="entry-desc">
                    <?php echo $course["description"]; ?>
                </span>

            </div>

            <div class="lesson-actions">

                <a class="btn-ghost"
                   href="edit_course.php?id=<?php echo $course["id"]; ?>">
                    تعديل
                </a>

                <a class="btn-danger"
                   href="delete_course.php?id=<?php echo $course["id"]; ?>"
                   onclick="return confirm('هل أنت متأكد من حذف الدورة؟');">
                    حذف
                </a>

                <a class="btn-ghost"
                   href="lessons.php?course_id=<?php echo $course["id"]; ?>">
                    📚 الدروس
                </a>

            </div>

        </div>

    <?php

    }

    ?>

    </div>

</div>


</body>

</html>