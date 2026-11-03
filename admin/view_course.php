<?php

session_start();

require_once "../includes/connection.php";
require_once "../includes/format.php";

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


/* الحصول على رقم الكورس */

if (!isset($_GET["id"])) {

    header("Location: course_requests.php");
    exit;

}

$course_id = $_GET["id"];


/* جلب بيانات الكورس */

$sql = "SELECT * FROM courses WHERE id = $course_id";

$result = mysqli_query($conn, $sql);

if (!$result) {

    die("خطأ في استعلام الكورس: " . mysqli_error($conn));

}


if (mysqli_num_rows($result) == 0) {

    header("Location: course_requests.php");
    exit;

}

$course = mysqli_fetch_assoc($result);


/* جلب اسم المعلم */

$teacher_name = "غير معروف";

if ($course["teacher_id"] != "") {

    $teacher_id = $course["teacher_id"];

    $sql_teacher = "SELECT * FROM users WHERE id = $teacher_id";

    $result_teacher = mysqli_query($conn, $sql_teacher);

    if ($result_teacher && mysqli_num_rows($result_teacher) > 0) {

        $teacher = mysqli_fetch_assoc($result_teacher);

        $teacher_name = $teacher["name"];

    }

}


/* جلب اسم التصنيف */

$category_name = "غير محدد";

if ($course["category_id"] != "") {

    $category_id = $course["category_id"];

    $sql_category = "SELECT * FROM categories WHERE id = $category_id";

    $result_category = mysqli_query($conn, $sql_category);

    if ($result_category && mysqli_num_rows($result_category) > 0) {

        $category = mysqli_fetch_assoc($result_category);

        $category_name = $category["name"];

    }

}


/* جلب دروس الكورس */

$sql_lessons = "SELECT * FROM lessons
                WHERE course_id = $course_id
                ORDER BY lesson_order ASC";

$result_lessons = mysqli_query($conn, $sql_lessons);

if (!$result_lessons) {

    die("خطأ في استعلام الدروس: " . mysqli_error($conn));

}

$lessons_count = mysqli_num_rows($result_lessons);


/* نص حالة الكورس */

$status_text = $course["status"];
$status_class = "is-draft";

if ($course["status"] == "approved") {

    $status_text = "✅ مقبول";
    $status_class = "is-approved";

} elseif ($course["status"] == "pending") {

    $status_text = "⏳ بانتظار المراجعة";
    $status_class = "is-pending";

} elseif ($course["status"] == "rejected") {

    $status_text = "❌ مرفوض";
    $status_class = "is-rejected";

} else {

    $status_text = "📝 مسودة";
    $status_class = "is-draft";

}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تفاصيل الكورس</title>

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

    <a href="course_requests.php" class="btn-back">
        العودة إلى طلبات المراجعة
    </a>


    <div class="entry-head">

        <h1>تفاصيل الكورس</h1>

        <a class="button"
           href="add_lesson.php?course_id=<?php echo $course["id"]; ?>">
            ➕ إضافة درس
        </a>

    </div>


    <p class="welcome">
        مراجعة بيانات الكورس ودروسه بالكامل قبل اتخاذ القرار.
    </p>


    <div class="card">

        <div class="profile-head">

            <div class="profile-avatar profile-avatar-empty">
                📚
            </div>

            <div class="profile-head-info">

                <h2>📚 <?php echo $course["title"]; ?></h2>

                <p>من إعداد: <?php echo $teacher_name; ?></p>

            </div>

        </div>


        <div class="profile-info">

            <div class="profile-row">
                <strong>🆔 رقم الكورس</strong>
                <span><?php echo $course["id"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>🏷️ التصنيف</strong>
                <span><?php echo $category_name; ?></span>
            </div>

            <div class="profile-row">
                <strong>👨‍🏫 المعلم</strong>
                <span><?php echo $teacher_name; ?></span>
            </div>

            <div class="profile-row">
                <strong>📚 عدد الدروس</strong>
                <span><?php echo $lessons_count; ?></span>
            </div>

            <div class="profile-row">
                <strong>📅 تاريخ الإضافة</strong>
                <span><?php echo $course["created_at"]; ?></span>
            </div>

            <div class="profile-row">
                <strong>🔖 الحالة</strong>
                <span class="entry-status <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
            </div>

        </div>


        <div class="profile-actions">

            <?php if ($course["status"] == "pending") { ?>

                <a class="button"
                   href="approve_course.php?id=<?php echo $course["id"]; ?>">
                    ✅ قبول الكورس
                </a>

                <a class="btn btn-danger btn-sm"
                   href="reject_course.php?id=<?php echo $course["id"]; ?>"
                   onclick="return confirm('هل أنت متأكد من رفض هذا الكورس؟');">
                    ❌ رفض الكورس
                </a>

            <?php } ?>

            <a class="btn-ghost"
               href="edit_course.php?id=<?php echo $course["id"]; ?>">
                ✏️ تعديل الكورس
            </a>

        </div>


        <div class="profile-actions mt-14">

            <p class="m-0">
                <?php echo nl2br(htmlspecialchars($course["description"], ENT_QUOTES, "UTF-8")); ?>
            </p>

        </div>

    </div>


    <div class="lesson-list-head">

        <h1 class="page-title-flush">📚 دروس الكورس (<?php echo $lessons_count; ?>)</h1>

    </div>


    <?php if ($lessons_count == 0) { ?>

        <div class="lesson-empty">
            لا توجد دروس في هذا الكورس بعد.
        </div>

    <?php } ?>


    <?php while ($lesson = mysqli_fetch_assoc($result_lessons)) { ?>

        <div class="card mt-16">

            <div class="lesson-header">

                <span class="lesson-num">
                    <?php echo $lesson["lesson_order"]; ?>
                </span>

                <h3 class="lesson-title lesson-title-flush">
                    <?php echo $lesson["title"]; ?>
                </h3>

                <div class="lesson-actions lesson-actions-end">

                    <?php if ($lesson["video"] != "") { ?>

                        <a class="btn-ghost"
                           href="<?php echo $lesson["video"]; ?>"
                           target="_blank">
                            🎬 الفيديو
                        </a>

                    <?php } ?>

                    <a class="btn-ghost"
                       href="edit_lesson.php?id=<?php echo $lesson["id"]; ?>">
                        تعديل
                    </a>

                    <a class="btn btn-danger btn-sm"
                       href="delete_lesson.php?id=<?php echo $lesson["id"]; ?>&course_id=<?php echo $course["id"]; ?>"
                       onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟');">
                        حذف
                    </a>

                </div>

            </div>


            <?php if ($lesson["content"] != "") { ?>

                <div class="lesson-body">

                    <?php echo render_lesson_content($lesson["content"]); ?>

                </div>

            <?php } else { ?>

                <p class="lesson-empty m-0">
                    لا يحتوي هذا الدرس على محتوى مكتوب.
                </p>

            <?php } ?>

        </div>

    <?php } ?>


</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>