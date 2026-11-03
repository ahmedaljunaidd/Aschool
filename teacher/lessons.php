<?php

session_start();

include "../includes/connection.php";
include '../includes/icons.php';
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


if ($_SESSION["role"] != "teacher") {
    die("ليس لديك صلاحية");
}


$teacher_id = $_SESSION["user_id"];


if (!isset($_GET["course_id"])) {
    die("الدورة غير موجودة");
}


$course_id = $_GET["course_id"];


// التأكد أن الدورة للمعلم الحالي
$sql = "SELECT * FROM courses
        WHERE id = '$course_id'
        AND teacher_id = '$teacher_id'";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("هذه الدورة ليست لك");
}


$course = mysqli_fetch_assoc($result);


// جلب الدروس
$sql = "SELECT * FROM lessons
        WHERE course_id = '$course_id'
        ORDER BY lesson_order";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}

?>

<?php
require_once "../includes/dash_helpers.php";

$course_id = (int) $course_id;
$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "معلم";
$lesson_total = mysqli_num_rows($result);

// اسم تصنيف الدورة
$category_name = "";

$cat_result = mysqli_query($conn,
    "SELECT name FROM categories WHERE id = (SELECT category_id FROM courses WHERE id = $course_id) LIMIT 1");

if ($cat_result && mysqli_num_rows($cat_result) > 0) {
    $cat_row = mysqli_fetch_assoc($cat_result);
    $category_name = trim($cat_row["name"]);
}

// حالة الدورة
$course_status = strtolower(trim(isset($course["status"]) ? $course["status"] : ""));

if ($course_status == "active" || $course_status == "approved") {
    $status_label = "معتمدة";
    $status_class = "is-active";
    $status_icon = "check-circle";
} elseif ($course_status == "pending") {
    $status_label = "قيد المراجعة";
    $status_class = "is-pending";
    $status_icon = "clock";
} elseif ($course_status == "rejected") {
    $status_label = "مرفوضة";
    $status_class = "is-rejected";
    $status_icon = "x";
} else {
    $status_label = "مسودة";
    $status_class = "is-draft";
    $status_icon = "edit";
}

// أول حرف من اسم المعلم للصورة الرمزية
$sidebar_letter = "م";
if ($user_name != "") {
    if (function_exists("mb_substr")) {
        $sidebar_letter = mb_substr($user_name, 0, 1, "UTF-8");
    } else {
        $sidebar_letter = substr($user_name, 0, 1);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="دروس الدورة — لوحة المعلم في منصة Aschool لإدارة الدروس والترتيب والمحتوى.">
    <title>دروس الدورة — Aschool</title>
    <link rel="stylesheet" href="../css/style.css?v=29">
    <link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">
    <link rel="stylesheet" href="../css/dash-ui.css?v=29">
</head>

<body class="theme-dark dash-ui shell-page" data-user-name="<?php echo dui_e($user_name); ?>">

<script>
(function () {
    try {
        if (localStorage.getItem('aschool.theme') === 'light') {
            document.body.classList.remove('theme-dark');
            document.body.classList.add('theme-light');
        }
    } catch (e) {
        // إذا كان المتصفح يمنع التخزين نكمل بالوضع الداكن
    }
})();
</script>

<aside class="sidebar">

    <div class="side-brand">
        <span class="brand-mark">A</span>
        <span class="side-brand-text">
            <strong>Aschool</strong>
            <small>لوحة المعلم</small>
        </span>
    </div>

    <div class="side-user">
        <span class="side-avatar"><?php echo dui_e($sidebar_letter); ?></span>
        <span class="side-user-meta">
            <strong><?php echo dui_e($user_name); ?></strong>
            <small>حساب معلم</small>
        </span>
    </div>

    <div class="sidebar-section-title">القائمة الرئيسية</div>

    <a href="dashboard.php">
        <span class="nav-ic"><?php echo icon('home', 'ic ic-sm'); ?></span>
        الرئيسية
    </a>

    <a href="courses.php">
        <span class="nav-ic"><?php echo icon('book', 'ic ic-sm'); ?></span>
        دوراتي
    </a>

    <div class="sidebar-section-title">المنصة</div>

    <a href="../courses.php">
        <span class="nav-ic"><?php echo icon('globe', 'ic ic-sm'); ?></span>
        استكشاف المنصة
    </a>

    <div class="sidebar-footer">
        منصة <strong>Aschool</strong> التعليمية
    </div>

</aside>

<button type="button" class="side-backdrop" data-sidebar-toggle tabindex="-1" aria-label="إغلاق القائمة"></button>

<div class="navbar">
    <a class="logout" href="../logout.php">تسجيل الخروج</a>
</div>

<main class="content dui-content">
    <div class="dui-wrap">

<a class="dui-back" href="courses.php">
    <?php echo icon('arrow-right', 'ic ic-sm'); ?>
    العودة إلى دوراتي
</a>

<section class="dui-hero">
    <div class="dui-hero-body">
        <span class="dui-eyebrow"><?php echo icon('play', 'ic ic-xs'); ?> محتوى الدورة</span>
        <h1><?php echo dui_e($course["title"]); ?></h1>
        <p>أضف دروس الدورة ورتّبها، وحدّد ما إذا كان الدرس يحتوي فيديو أو محتوى مكتوباً.</p>
    </div>
    <div class="dui-hero-actions">
        <a class="dui-btn dui-btn-primary" href="add_lesson.php?course_id=<?php echo $course_id; ?>">
            <?php echo icon('plus', 'ic ic-sm'); ?>
            إضافة درس جديد
        </a>
        <a class="dui-btn dui-btn-ghost" href="edit_course.php?id=<?php echo $course_id; ?>">
            <?php echo icon('edit', 'ic ic-sm'); ?>
            تعديل الدورة
        </a>
    </div>
</section>

<div class="dui-stats">

    <div class="dui-stat">
        <span class="dui-stat-ic tint-green"><?php echo icon('play', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $lesson_total; ?></b>
            <span>درس في الدورة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic"><?php echo icon('folder', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo dui_e($category_name != "" ? $category_name : "بدون تصنيف"); ?></b>
            <span>التصنيف</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-violet"><?php echo icon('graduation', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo dui_e($user_name); ?></b>
            <span>المدرس</span>
        </span>
    </div>

</div>

<section class="dui-section">
    <div class="dui-sec-head">
        <h2><?php echo icon('list', 'ic ic-sm'); ?> قائمة الدروس</h2>
        <span class="dui-sec-meta">
            <span class="dui-badge <?php echo $status_class; ?>">
                <?php echo icon($status_icon, 'ic ic-xs'); ?>
                <?php echo $status_label; ?>
            </span>
        </span>
    </div>

    <?php if ($lesson_total == 0) { ?>
        <div class="dui-empty">
            <span class="dui-empty-ic"><?php echo icon('play', 'ic ic-xl'); ?></span>
            <h3>لا توجد دروس في هذه الدورة بعد</h3>
            <p>أضف الدرس الأول لتبدأ بناء محتوى الدورة.</p>
            <a class="dui-btn dui-btn-primary dui-btn-sm" href="add_lesson.php?course_id=<?php echo $course_id; ?>">
                <?php echo icon('plus', 'ic ic-xs'); ?> إضافة درس جديد
            </a>
        </div>
    <?php } else { ?>
        <div class="dui-lesson-block is-standalone">
            <div class="dui-lesson-head">
                <h3><?php echo icon('book-open', 'ic ic-xs'); ?> <?php echo $lesson_total; ?> درس</h3>
                <a class="dui-link" href="add_lesson.php?course_id=<?php echo $course_id; ?>">
                    <?php echo icon('plus', 'ic ic-xs'); ?> إضافة درس
                </a>
            </div>
            <ul class="dui-lessons">
                <?php while ($lesson = mysqli_fetch_assoc($result)) {
                    $lesson_id = (int) $lesson["id"];
                    $lesson_order = isset($lesson["lesson_order"]) ? (int) $lesson["lesson_order"] : 0;
                    ?>
                    <li class="dui-lesson">
                        <span class="dui-lesson-num"><?php echo $lesson_order > 0 ? $lesson_order : '-'; ?></span>
                        <span class="dui-lesson-body">
                            <span class="dui-lesson-title"><?php echo dui_e($lesson["title"]); ?></span>
                            <span class="dui-lesson-meta">الدرس رقم <?php echo $lesson_order; ?></span>
                        </span>
                        <span class="dui-lesson-tags">
                            <?php if (trim($lesson["video"]) != "") { ?>
                                <span class="dui-tag"><?php echo icon('video', 'ic ic-xs'); ?> فيديو</span>
                            <?php } ?>
                            <?php if (trim($lesson["content"]) != "") { ?>
                                <span class="dui-tag"><?php echo icon('file', 'ic ic-xs'); ?> محتوى مكتوب</span>
                            <?php } ?>
                            <?php if (trim($lesson["video"]) == "" && trim($lesson["content"]) == "") { ?>
                                <span class="dui-tag"><?php echo icon('file', 'ic ic-xs'); ?> درس</span>
                            <?php } ?>
                        </span>
                        <span class="dui-lesson-actions">
                            <a class="dui-btn dui-btn-ghost dui-btn-sm" href="edit_lesson.php?id=<?php echo $lesson_id; ?>">
                                <?php echo icon('edit', 'ic ic-xs'); ?> تعديل
                            </a>
                            <a class="dui-btn dui-btn-danger dui-btn-sm"
                               href="delete_lesson.php?id=<?php echo $lesson_id; ?>"
                               onclick="return confirm('هل تريد حذف هذا الدرس؟');">
                                <?php echo icon('trash', 'ic ic-xs'); ?> حذف
                            </a>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</section>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
