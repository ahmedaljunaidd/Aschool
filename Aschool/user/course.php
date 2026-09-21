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


/* التأكد أن المستخدم مسجل في الكورس */

$sql_check = "SELECT * FROM enrollments
              WHERE user_id = $user_id
              AND course_id = $course_id";

$result_check = mysqli_query($conn, $sql_check);


if (mysqli_num_rows($result_check) == 0) {

    die("أنت غير مسجل في هذا الكورس");

}


/* جلب الكورس */

$sql = "SELECT * FROM courses
        WHERE id = $course_id
        AND status = 'approved'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    die("الكورس غير موجود");

}


$course = mysqli_fetch_assoc($result);


/* جلب الدروس */

$sql_lessons = "SELECT * FROM lessons
                WHERE course_id = $course_id
                ORDER BY lesson_order ASC";

$result_lessons = mysqli_query($conn, $sql_lessons);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title><?php echo $course["title"]; ?></title>

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

    <h1>
        <?php echo $course["title"]; ?>
    </h1>

    <p>
        <?php echo $course["description"]; ?>
    </p>

    <a class="button"
   href="add_favorite.php?id=<?php echo $course_id; ?>">

    ⭐ إضافة إلى المفضلة

</a>

    <div class="lesson-list-head">

        <h2>📚 الدروس
            <small style="font-weight:500;color:var(--text-muted);">
                (<?php echo mysqli_num_rows($result_lessons); ?> درس)
            </small>
        </h2>

    </div>


    <?php

    if (mysqli_num_rows($result_lessons) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دروس في هذه الدورة بعد — عد لاحقًا.
        </div>

    <?php

    }

    ?>


    <div class="lesson-list">

    <?php

    while ($lesson = mysqli_fetch_assoc($result_lessons)) {

    ?>

        <a class="lesson-item"
           href="lesson.php?id=<?php echo $lesson["id"]; ?>">

            <span class="lesson-num">
                <?php echo $lesson["lesson_order"]; ?>
            </span>

            <span class="lesson-item-body">

                <span class="lesson-title">
                    <?php echo $lesson["title"]; ?>
                </span>

                <?php if ($lesson["video"] != "") { ?>

                    <span class="lesson-meta">
                        🎬 يحتوي على فيديو
                    </span>

                <?php } ?>

            </span>

            <span class="lesson-arrow">←</span>

        </a>

    <?php

    }

    ?>

    </div>

</div>


</body>

</html>