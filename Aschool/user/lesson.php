<?php

session_start();

include "../includes/connection.php";
require_once "../includes/format.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


/* التأكد أن المستخدم مسجل دخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/* التأكد أن نوع المستخدم user */

if ($_SESSION["role"] != "user") {

    die("ليس لديك صلاحية");

}


/* التأكد من وجود رقم الدرس */

if (!isset($_GET["id"])) {

    die("الدرس غير موجود");

}


$user_id = $_SESSION["user_id"];
$lesson_id = $_GET["id"];


/* جلب الدرس */

$sql = "SELECT * FROM lessons
        WHERE id = $lesson_id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    die("الدرس غير موجود");

}


$lesson = mysqli_fetch_assoc($result);

$course_id = $lesson["course_id"];


/* التأكد أن المستخدم مسجل في الكورس */

$sql_check = "SELECT * FROM enrollments
              WHERE user_id = $user_id
              AND course_id = $course_id";

$result_check = mysqli_query($conn, $sql_check);


if (mysqli_num_rows($result_check) == 0) {

    die("أنت غير مسجل في هذا الكورس");

}


/* معرفة هل الدرس مكتمل */

$sql_progress = "SELECT * FROM lesson_progress
                 WHERE user_id = $user_id
                 AND lesson_id = $lesson_id";

$result_progress = mysqli_query($conn, $sql_progress);

$completed = 0;


if (mysqli_num_rows($result_progress) > 0) {

    $progress = mysqli_fetch_assoc($result_progress);

    $completed = $progress["completed"];

}


/* جلب الدرس التالي */

$current_order = $lesson["lesson_order"];

$sql_next = "SELECT * FROM lessons
             WHERE course_id = $course_id
             AND lesson_order > $current_order
             ORDER BY lesson_order ASC
             LIMIT 1";

$result_next = mysqli_query($conn, $sql_next);

$next_lesson = null;


if (mysqli_num_rows($result_next) > 0) {

    $next_lesson = mysqli_fetch_assoc($result_next);

}

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title><?php echo $lesson["title"]; ?></title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>


<body>


<!-- Navbar -->

<div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <a href="my_courses.php" class="active">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

        <a href="profile.php">الملف الشخصي</a>

        <a href="edit_profile.php">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<!-- محتوى الدرس -->

<div class="content">


    <!-- العودة إلى الكورس -->

    <a href="course.php?id=<?php echo $course_id; ?>"
       class="btn-back">

        العودة إلى الكورس

    </a>


    <div class="card">


        <!-- عنوان الدرس -->
        <div class="lesson-header">

            <span class="lesson-num">
                <?php echo $lesson["lesson_order"]; ?>
            </span>

            <h1>
                <?php echo $lesson["title"]; ?>
            </h1>

        </div>


        <!-- محتوى الدرس -->
        <div class="lesson-content lesson-body">

            <?php echo render_lesson_content($lesson["content"]); ?>

        </div>


        <!-- الفيديو -->
        <?php

        if ($lesson["video"] != "") {

        ?>
        <?php

        }

        ?>


        <!-- حالة إكمال الدرس -->
        <?php

        if ($completed == 1) {

        ?>

            <div class="lesson-status done">

                ✅ تم إكمال هذا الدرس.

            </div>

        <?php

        } else {

        ?>

            <div class="lesson-status pending">

                ⏳ لم يتم إكمال هذا الدرس بعد.

            </div>

            <a class="button save-button"
               href="complete_lesson.php?id=<?php echo $lesson_id; ?>">

                تم إكمال الدرس

            </a>

        <?php

        }

        ?>


        <!-- زر الدرس التالي -->
        <div class="lesson-nav">

        <?php

        if ($next_lesson != null) {

        ?>

            <a class="button"
               href="lesson.php?id=<?php echo $next_lesson["id"]; ?>">

                التالي →
            </a>

        <?php

        } else {

        ?>

            <div class="lesson-empty">

                🎉 لقد وصلت إلى آخر درس في هذا الكورس.

            </div>

        <?php

        }

        ?>

        </div>


    </div>

</div>


</body>

</html>