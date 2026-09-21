<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



/* التأكد أن المستخدم مسجل دخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/* التأكد أنه Admin */

if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* التأكد أن رقم الدورة موجود */

if (!isset($_GET["course_id"])) {

    header("Location: courses.php");
    exit;

}


$course_id = $_GET["course_id"];


/* جلب معلومات الدورة */

$sql_course = "SELECT * FROM courses WHERE id = $course_id";

$result_course = mysqli_query($conn, $sql_course);


if (mysqli_num_rows($result_course) == 0) {

    echo "الدورة غير موجودة";
    exit;

}


$course = mysqli_fetch_assoc($result_course);


/* جلب الدروس */

$sql = "SELECT * FROM lessons
        WHERE course_id = $course_id
        ORDER BY lesson_order ASC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>دروس الدورة</title>

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

    <div class="lesson-list-head">

        <h1 style="margin:0;">📚 دروس الدورة</h1>

        <a class="button"
           href="add_lesson.php?course_id=<?php echo $course_id; ?>">

            ➕ إضافة درس

        </a>

    </div>

    <p class="welcome">

        الدورة:
        <?php echo $course["title"]; ?>

    </p>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دروس في هذه الدورة بعد.
        </div>

    <?php

    }

    ?>


    <div class="lesson-list">

    <?php

    while ($lesson = mysqli_fetch_assoc($result)) {

    ?>

        <div class="lesson-item">

            <span class="lesson-num">
                <?php echo $lesson["lesson_order"]; ?>
            </span>

            <div class="lesson-item-body">

                <span class="lesson-title">
                    <?php echo $lesson["title"]; ?>
                </span>

                <span class="lesson-meta">
                    <?php if ($lesson["video"] != "") { ?>
                        🎬 فيديو
                    <?php } ?>
                    <?php if ($lesson["content"] != "") { ?>
                        📝 محتوى مكتوب
                    <?php } ?>
                </span>

            </div>

            <div class="lesson-actions">

                <a class="btn-ghost"
                   href="edit_lesson.php?id=<?php echo $lesson["id"]; ?>">

                    تعديل

                </a>


                <a class="btn-danger"
                   href="delete_lesson.php?id=<?php echo $lesson["id"]; ?>&course_id=<?php echo $course_id; ?>"
                   onclick="return confirm('هل أنت متأكد من حذف الدرس؟');">

                    حذف

                </a>

            </div>

        </div>

    <?php

    }

    ?>

    </div>


    <br>


    <a class="button" href="courses.php">

        ← العودة إلى الدورات

    </a>


</div>


</body>

</html>