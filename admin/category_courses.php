<?php

session_start();

require_once "../includes/connection.php";
include '../includes/icons.php';

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


require_once "../includes/dash_helpers.php";

$category_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($category_id <= 0) {
    header("Location: categories.php");
    exit;
}

// جلب التصنيف
$category_result = mysqli_query($conn, "SELECT * FROM categories WHERE id = $category_id LIMIT 1");

if (mysqli_num_rows($category_result) == 0) {
    header("Location: categories.php");
    exit;
}

$category = mysqli_fetch_assoc($category_result);
$category_name = trim($category["name"]);
$category_desc = trim($category["description"]);

// جلب دورات التصنيف مع اسم المدرس
$courses_result = mysqli_query($conn,
    "SELECT courses.*, users.name AS teacher_name
     FROM courses
     LEFT JOIN users ON users.id = courses.teacher_id
     WHERE courses.category_id = $category_id
     ORDER BY courses.id DESC");

$course_count = mysqli_num_rows($courses_result);

// عدد الدروس في التصنيف
$count_result = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM lessons
     INNER JOIN courses ON courses.id = lessons.course_id
     WHERE courses.category_id = $category_id");
$count_row = mysqli_fetch_assoc($count_result);
$lesson_count = $count_row["total"];

// عدد الدورات المعتمدة
$count_result = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM courses
     WHERE category_id = $category_id
     AND (status = 'active' OR status = 'approved')");
$count_row = mysqli_fetch_assoc($count_result);
$approved_count = $count_row["total"];

// عدد المدرسين في التصنيف
$count_result = mysqli_query($conn,
    "SELECT COUNT(DISTINCT teacher_id) AS total
     FROM courses
     WHERE category_id = $category_id
     AND teacher_id IS NOT NULL");
$count_row = mysqli_fetch_assoc($count_result);
$teacher_count = $count_row["total"];

$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "مدير المنصة";

// أيقونة ولون التصنيف
if ($category_id == 5) {
    $cat_icon = "hash";
    $cat_tint = "dui-tint-5";
} elseif ($category_id == 6) {
    $cat_icon = "globe";
    $cat_tint = "dui-tint-6";
} elseif ($category_id == 7) {
    $cat_icon = "layers";
    $cat_tint = "dui-tint-7";
} elseif ($category_id == 8) {
    $cat_icon = "graduation";
    $cat_tint = "dui-tint-8";
} else {
    $cat_icon = "folder";
    $cat_tint = "dui-tint-x";
}

// أول حرف من اسم المدير للصورة الرمزية
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
    <meta name="description" content="دورات التصنيف — لوحة Aschool لإدارة المحتوى التعليمي والدورات والدروس.">
    <title>دورات التصنيف — Aschool</title>
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
            <small>لوحة الإدارة</small>
        </span>
    </div>

    <div class="side-user">
        <span class="side-avatar"><?php echo dui_e($sidebar_letter); ?></span>
        <span class="side-user-meta">
            <strong><?php echo dui_e($user_name); ?></strong>
            <small>مدير المنصة</small>
        </span>
    </div>

    <div class="sidebar-section-title">القائمة الرئيسية</div>

    <a href="dashboard.php">
        <span class="nav-ic"><?php echo icon('home', 'ic ic-sm'); ?></span>
        الرئيسية
    </a>

    <a href="users.php">
        <span class="nav-ic"><?php echo icon('users', 'ic ic-sm'); ?></span>
        المستخدمون
    </a>

    <a href="admin_activation.php">
        <span class="nav-ic"><?php echo icon('user-check', 'ic ic-sm'); ?></span>
        طلبات التفعيل
    </a>

    <div class="sidebar-section-title">المحتوى</div>

    <a href="categories.php">
        <span class="nav-ic"><?php echo icon('folder', 'ic ic-sm'); ?></span>
        التصنيفات
    </a>

    <a href="courses.php">
        <span class="nav-ic"><?php echo icon('book', 'ic ic-sm'); ?></span>
        الدورات
    </a>

    <a href="course_requests.php">
        <span class="nav-ic"><?php echo icon('clock', 'ic ic-sm'); ?></span>
        طلبات المراجعة
    </a>

    <div class="sidebar-section-title">النظام</div>

    <a href="settings.php">
        <span class="nav-ic"><?php echo icon('settings', 'ic ic-sm'); ?></span>
        الإعدادات
    </a>

    <a href="../courses.php">
        <span class="nav-ic"><?php echo icon('globe', 'ic ic-sm'); ?></span>
        عرض المنصة
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

<a class="dui-back" href="categories.php">
    <?php echo icon('arrow-right', 'ic ic-sm'); ?>
    العودة إلى التصنيفات
</a>

<section class="dui-hero">
    <div class="dui-hero-body">
        <span class="dui-eyebrow"><?php echo icon('folder', 'ic ic-xs'); ?> تصنيف</span>
        <h1><?php echo dui_e($category_name); ?></h1>
        <p><?php echo dui_e($category_desc != "" ? $category_desc : "كل الدورات والدروس التابعة لهذا التصنيف في مكان واحد."); ?></p>
    </div>
    <div class="dui-hero-actions">
        <a class="dui-btn dui-btn-primary" href="add_course.php">
            <?php echo icon('plus', 'ic ic-sm'); ?>
            إضافة دورة
        </a>
        <a class="dui-btn dui-btn-ghost" href="edit_category.php?id=<?php echo $category_id; ?>">
            <?php echo icon('edit', 'ic ic-sm'); ?>
            تعديل التصنيف
        </a>
    </div>
</section>

<div class="dui-stats">

    <div class="dui-stat">
        <span class="dui-stat-ic tint-violet"><?php echo icon('book', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $course_count; ?></b>
            <span>دورة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-green"><?php echo icon('check-circle', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $approved_count; ?></b>
            <span>دورة معتمدة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic"><?php echo icon('play', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $lesson_count; ?></b>
            <span>درس</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-amber"><?php echo icon('users', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $teacher_count; ?></b>
            <span>مدرس</span>
        </span>
    </div>

</div>

<section class="dui-section">
    <div class="dui-sec-head">
        <h2><?php echo icon('book-open', 'ic ic-sm'); ?> دورات التصنيف ودروسه</h2>
        <span class="dui-sec-meta"><?php echo $course_count; ?> دورة · <?php echo $lesson_count; ?> درس</span>
    </div>

    <?php if ($course_count == 0) { ?>

        <div class="dui-empty">
            <span class="dui-empty-ic"><?php echo icon('book', 'ic ic-xl'); ?></span>
            <h3>لا توجد دورات في هذا التصنيف</h3>
            <p>أضف دورة جديدة واختر هذا التصنيف لتظهر هنا مع دروسها وأسماء مدرسيها.</p>
            <a class="dui-btn dui-btn-primary dui-btn-sm" href="add_course.php">
                <?php echo icon('plus', 'ic ic-xs'); ?> إضافة دورة
            </a>
        </div>

    <?php } else { ?>

        <?php while ($course = mysqli_fetch_assoc($courses_result)) { ?>

            <?php
            $course_id = $course["id"];
            $course_title = $course["title"];
            $course_desc = trim($course["description"]);
            $teacher_name = trim($course["teacher_name"]);

            if ($teacher_name == "") {
                $teacher_name = "فريق Aschool";
            }

            // عدد دروس الدورة
            $course_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons WHERE course_id = $course_id");
            $course_row = mysqli_fetch_assoc($course_query);
            $course_lesson_count = $course_row["total"];

            // عدد المتعلمين في الدورة
            $course_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE course_id = $course_id");
            $course_row = mysqli_fetch_assoc($course_query);
            $course_learner_count = $course_row["total"];

            // حالة الدورة
            $course_status = strtolower(trim($course["status"]));

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

            // أول حرف من اسم المدرس
            if (function_exists("mb_substr")) {
                $teacher_letter = mb_substr($teacher_name, 0, 1, "UTF-8");
            } else {
                $teacher_letter = substr($teacher_name, 0, 1);
            }

            // وصف مختصر للدورة
            if ($course_desc != "") {
                if (function_exists("mb_substr")) {
                    $course_desc_short = mb_substr($course_desc, 0, 200, "UTF-8");
                } else {
                    $course_desc_short = substr($course_desc, 0, 200);
                }
            } else {
                $course_desc_short = "";
            }

            // دروس الدورة
            $lessons_result = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY lesson_order");
            ?>

            <article class="dui-course dui-course-wide">

                <div class="dui-course-cover <?php echo $cat_tint; ?>"></div>

                <div class="dui-wide-head">
                    <div class="dui-wide-main">
                        <span class="dui-wide-icon <?php echo $cat_tint; ?>">
                            <?php echo icon($cat_icon, 'ic ic-lg'); ?>
                        </span>
                        <div>
                            <span class="dui-kicker">
                                <?php echo icon('folder', 'ic ic-xs'); ?>
                                <?php echo dui_e($category_name); ?>
                            </span>
                            <h3 class="dui-wide-title"><?php echo dui_e($course_title); ?></h3>
                            <span class="dui-teacher">
                                <span class="dui-avatar"><?php echo dui_e($teacher_letter); ?></span>
                                <?php echo dui_e($teacher_name); ?>
                            </span>
                        </div>
                    </div>
                    <div class="dui-wide-side">
                        <span class="dui-badge <?php echo $status_class; ?>">
                            <?php echo icon($status_icon, 'ic ic-xs'); ?>
                            <?php echo $status_label; ?>
                        </span>
                    </div>
                </div>

                <?php if ($course_desc_short != "") { ?>
                    <p class="dui-wide-desc"><?php echo dui_e($course_desc_short); ?></p>
                <?php } ?>

                <div class="dui-wide-meta">
                    <span><?php echo icon('play', 'ic ic-xs'); ?> <?php echo $course_lesson_count; ?> درس</span>
                    <span><?php echo icon('users', 'ic ic-xs'); ?> <?php echo $course_learner_count; ?> متعلم</span>
                    <span><?php echo icon('user', 'ic ic-xs'); ?> المدرس: <?php echo dui_e($teacher_name); ?></span>
                </div>

                <div class="dui-lesson-block">

                    <div class="dui-lesson-head">
                        <h4><?php echo icon('list', 'ic ic-xs'); ?> دروس الدورة</h4>
                        <a class="dui-link" href="lessons.php?course_id=<?php echo $course_id; ?>">
                            إدارة الدروس
                            <?php echo icon('arrow-left', 'ic ic-xs'); ?>
                        </a>
                    </div>

                    <?php if (mysqli_num_rows($lessons_result) == 0) { ?>

                        <div class="dui-lesson-empty">لا توجد دروس في هذه الدورة بعد.</div>

                    <?php } else { ?>

                        <ul class="dui-lessons">

                            <?php while ($lesson = mysqli_fetch_assoc($lessons_result)) { ?>

                                <?php
                                $lesson_id = $lesson["id"];
                                $lesson_order = $lesson["lesson_order"];

                                if ($lesson_order > 0) {
                                    $order_text = $lesson_order;
                                } else {
                                    $order_text = "-";
                                }
                                ?>

                                <li class="dui-lesson">

                                    <span class="dui-lesson-num"><?php echo $order_text; ?></span>

                                    <span class="dui-lesson-body">
                                        <span class="dui-lesson-title"><?php echo dui_e($lesson["title"]); ?></span>
                                        <span class="dui-lesson-meta">ترتيب الدرس <?php echo $lesson_order; ?></span>
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
                                           href="delete_lesson.php?id=<?php echo $lesson_id; ?>&course_id=<?php echo $course_id; ?>"
                                           onclick="return confirm('هل أنت متأكد من حذف الدرس؟');">
                                            <?php echo icon('trash', 'ic ic-xs'); ?> حذف
                                        </a>
                                    </span>

                                </li>

                            <?php } ?>

                        </ul>

                    <?php } ?>

                </div>

                <div class="dui-course-foot">
                    <a class="dui-btn dui-btn-primary dui-btn-sm" href="lessons.php?course_id=<?php echo $course_id; ?>">
                        <?php echo icon('play', 'ic ic-xs'); ?> إدارة الدروس
                    </a>
                    <a class="dui-btn dui-btn-ghost dui-btn-sm" href="view_course.php?id=<?php echo $course_id; ?>">
                        <?php echo icon('eye', 'ic ic-xs'); ?> عرض التفاصيل
                    </a>
                    <a class="dui-btn dui-btn-ghost dui-btn-sm" href="edit_course.php?id=<?php echo $course_id; ?>">
                        <?php echo icon('edit', 'ic ic-xs'); ?> تعديل الدورة
                    </a>
                    <a class="dui-btn dui-btn-danger dui-btn-sm"
                       href="delete_course.php?id=<?php echo $course_id; ?>"
                       onclick="return confirm('هل أنت متأكد من حذف الدورة؟');">
                        <?php echo icon('trash', 'ic ic-xs'); ?> حذف
                    </a>
                </div>

            </article>

        <?php } ?>

    <?php } ?>

</section>

<script>
window.addEventListener('load', function () {
    var link = document.querySelector('.sidebar a[href="categories.php"]');
    if (link) link.classList.add('active');
});
</script>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
