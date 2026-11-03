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


if ($_SESSION["role"] != "teacher") {

    header("Location: ../index.php");
    exit;

}


$teacher_id = $_SESSION["user_id"];


$sql = "SELECT * FROM courses

        WHERE teacher_id = $teacher_id

        ORDER BY id DESC";


$result = mysqli_query($conn, $sql);

?>

<?php
require_once "../includes/dash_helpers.php";

$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "معلم";
$active_cat = isset($_GET["category"]) ? (int) $_GET["category"] : 0;
$total_courses = mysqli_num_rows($result);

// عدد الدورات المعتمدة
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses WHERE teacher_id = $teacher_id AND status = 'active'");
$count_row = mysqli_fetch_assoc($count_result);
$active_count = $count_row["total"];

// عدد الدورات قيد المراجعة
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses WHERE teacher_id = $teacher_id AND status = 'pending'");
$count_row = mysqli_fetch_assoc($count_result);
$pending_count = $count_row["total"];

// عدد الدروس في كل دورات المدرس
$count_result = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM lessons
     INNER JOIN courses ON courses.id = lessons.course_id
     WHERE courses.teacher_id = $teacher_id");
$count_row = mysqli_fetch_assoc($count_result);
$lesson_total = $count_row["total"];

// اسم التصنيف المختار في الفلتر
$filter_name = "";
if ($active_cat > 0) {
    $filter_result = mysqli_query($conn, "SELECT name FROM categories WHERE id = $active_cat LIMIT 1");
    if (mysqli_num_rows($filter_result) > 0) {
        $filter_row = mysqli_fetch_assoc($filter_result);
        $filter_name = trim($filter_row["name"]);
    }
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
    <meta name="description" content="دوراتي — لوحة المعلم في منصة Aschool لإدارة الدورات والدروس.">
    <title>دوراتي — Aschool</title>
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

<a class="dui-back" href="dashboard.php">
    <?php echo icon('arrow-right', 'ic ic-sm'); ?>
    العودة إلى لوحة التحكم
</a>

<section class="dui-hero">
    <div class="dui-hero-body">
        <span class="dui-eyebrow"><?php echo icon('graduation', 'ic ic-xs'); ?> مساحة المعلم</span>
        <h1>دوراتي</h1>
        <p>كل دوراتك مصنّفة في مكان واحد. تابع حالة النشر، أضف الدروس، واعرف عدد المتعلمين في كل دورة.</p>
    </div>
    <div class="dui-hero-actions">
        <a class="dui-btn dui-btn-primary" href="add_course.php">
            <?php echo icon('plus', 'ic ic-sm'); ?>
            إنشاء دورة جديدة
        </a>
        <a class="dui-btn dui-btn-ghost" href="dashboard.php">
            <?php echo icon('home', 'ic ic-sm'); ?>
            لوحة التحكم
        </a>
    </div>
</section>

<div class="dui-stats">

    <div class="dui-stat">
        <span class="dui-stat-ic"><?php echo icon('book', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $total_courses; ?></b>
            <span>إجمالي الدورات</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-green"><?php echo icon('check-circle', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $active_count; ?></b>
            <span>دورات معتمدة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-amber"><?php echo icon('clock', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $pending_count; ?></b>
            <span>قيد المراجعة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-violet"><?php echo icon('play', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $lesson_total; ?></b>
            <span>إجمالي الدروس</span>
        </span>
    </div>

</div>

<?php if ($total_courses == 0) { ?>

    <div class="dui-empty">
        <span class="dui-empty-ic"><?php echo icon('book', 'ic ic-xl'); ?></span>
        <h3>لا توجد لديك دورات بعد</h3>
        <p>أنشئ دورتك الأولى، أضف دروسها، ثم أرسلها للمراجعة لتظهر على المنصة.</p>
        <a class="dui-btn dui-btn-primary dui-btn-sm" href="add_course.php">
            <?php echo icon('plus', 'ic ic-xs'); ?> إنشاء دورة
        </a>
    </div>

<?php } else { ?>

    <div class="dui-chips">

        <a class="dui-chip<?php if ($active_cat == 0) { echo ' is-active'; } ?>" href="courses.php">
            كل دوراتي
            <small><?php echo $total_courses; ?></small>
        </a>

        <?php
        $chips_result = mysqli_query($conn,
            "SELECT courses.category_id, categories.name, COUNT(*) AS total
             FROM courses
             LEFT JOIN categories ON categories.id = courses.category_id
             WHERE courses.teacher_id = $teacher_id
             GROUP BY courses.category_id, categories.name
             ORDER BY categories.name");

        while ($chip = mysqli_fetch_assoc($chips_result)) {
            $chip_id = (int) $chip["category_id"];
            $chip_name = trim($chip["name"]);

            if ($chip_name == "") {
                $chip_name = "بدون تصنيف";
            }
            ?>

            <a class="dui-chip<?php if ($active_cat == $chip_id) { echo ' is-active'; } ?>" href="courses.php?category=<?php echo $chip_id; ?>">
                <?php echo dui_e($chip_name); ?>
                <small><?php echo $chip["total"]; ?></small>
            </a>

        <?php } ?>

    </div>

    <section class="dui-section">

        <div class="dui-sec-head">
            <h2><?php echo icon('grid', 'ic ic-sm'); ?> <?php echo $filter_name != "" ? dui_e($filter_name) : "كل الدورات"; ?></h2>
            <span class="dui-sec-meta"><?php echo $total_courses; ?> دورة · <?php echo $lesson_total; ?> درس</span>
        </div>

        <div class="dui-course-grid">

            <?php
            $matched = 0;

            while ($course = mysqli_fetch_assoc($result)) {

                $course_id = $course["id"];
                $course_cat = (int) $course["category_id"];

                if ($active_cat > 0 && $course_cat != $active_cat) {
                    continue;
                }

                $matched++;

                $course_title = $course["title"];
                $course_desc = trim($course["description"]);

                // اسم التصنيف
                $category_name = "بدون تصنيف";
                if ($course_cat > 0) {
                    $cat_result = mysqli_query($conn, "SELECT name FROM categories WHERE id = $course_cat LIMIT 1");
                    if (mysqli_num_rows($cat_result) > 0) {
                        $cat_row = mysqli_fetch_assoc($cat_result);
                        $category_name = trim($cat_row["name"]);
                    }
                }

                // عدد دروس الدورة
                $course_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons WHERE course_id = $course_id");
                $course_row = mysqli_fetch_assoc($course_query);
                $course_lessons = $course_row["total"];

                // عدد متعلمي الدورة
                $course_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE course_id = $course_id");
                $course_row = mysqli_fetch_assoc($course_query);
                $course_learners = $course_row["total"];

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

                // أيقونة ولون التصنيف
                if ($course_cat == 5) {
                    $cat_icon = "hash";
                    $cat_tint = "dui-tint-5";
                } elseif ($course_cat == 6) {
                    $cat_icon = "globe";
                    $cat_tint = "dui-tint-6";
                } elseif ($course_cat == 7) {
                    $cat_icon = "layers";
                    $cat_tint = "dui-tint-7";
                } elseif ($course_cat == 8) {
                    $cat_icon = "graduation";
                    $cat_tint = "dui-tint-8";
                } else {
                    $cat_icon = "folder";
                    $cat_tint = "dui-tint-x";
                }

                // أول حرف من عنوان الدورة
                if (function_exists("mb_substr")) {
                    $course_letter = mb_substr($course_title, 0, 1, "UTF-8");
                } else {
                    $course_letter = substr($course_title, 0, 1);
                }

                // وصف مختصر
                if ($course_desc != "") {
                    if (function_exists("mb_substr")) {
                        $course_desc_short = mb_substr($course_desc, 0, 130, "UTF-8");
                    } else {
                        $course_desc_short = substr($course_desc, 0, 130);
                    }
                } else {
                    $course_desc_short = "";
                }
                ?>

                <article class="dui-course">

                    <div class="dui-course-cover <?php echo $cat_tint; ?>">
                        <span class="dui-cover-ic"><?php echo icon($cat_icon, 'ic ic-lg'); ?></span>
                        <span class="dui-cover-letter"><?php echo dui_e($course_letter); ?></span>
                    </div>

                    <div class="dui-course-body">

                        <div class="dui-course-top">
                            <span class="dui-kicker">
                                <?php echo icon('folder', 'ic ic-xs'); ?>
                                <?php echo dui_e($category_name); ?>
                            </span>
                            <span class="dui-badge <?php echo $status_class; ?>">
                                <?php echo icon($status_icon, 'ic ic-xs'); ?>
                                <?php echo $status_label; ?>
                            </span>
                        </div>

                        <h3 class="dui-course-title">
                            <a class="dui-course-link" href="lessons.php?course_id=<?php echo $course_id; ?>"><?php echo dui_e($course_title); ?></a>
                        </h3>

                        <?php if ($course_desc_short != "") { ?>
                            <p class="dui-course-desc"><?php echo dui_e($course_desc_short); ?></p>
                        <?php } ?>

                        <div class="dui-meta-row">
                            <span><?php echo icon('play', 'ic ic-xs'); ?> <?php echo $course_lessons; ?> درس</span>
                            <span><?php echo icon('users', 'ic ic-xs'); ?> <?php echo $course_learners; ?> متعلم</span>
                        </div>

                        <span class="dui-teacher">
                            <span class="dui-avatar"><?php echo dui_e($user_name); ?></span>
                            <?php echo dui_e($user_name); ?>
                        </span>

                    </div>

                    <div class="dui-course-foot">

                        <a class="dui-btn dui-btn-primary dui-btn-sm" href="lessons.php?course_id=<?php echo $course_id; ?>">
                            <?php echo icon('play', 'ic ic-xs'); ?> إدارة الدروس
                        </a>

                        <a class="dui-btn dui-btn-ghost dui-btn-sm" href="edit_course.php?id=<?php echo $course_id; ?>">
                            <?php echo icon('edit', 'ic ic-xs'); ?> تعديل
                        </a>

                        <?php if ($course_status == "draft" || $course_status == "rejected") { ?>
                            <a class="dui-btn dui-btn-soft dui-btn-sm" href="submit_course.php?id=<?php echo $course_id; ?>">
                                <?php echo icon('upload', 'ic ic-xs'); ?> إرسال للمراجعة
                            </a>
                        <?php } ?>

                        <a class="dui-btn dui-btn-danger dui-btn-sm" href="delete_course.php?id=<?php echo $course_id; ?>" onclick="return confirm('هل أنت متأكد من حذف الدورة؟');">
                            <?php echo icon('trash', 'ic ic-xs'); ?> حذف
                        </a>

                    </div>

                </article>

            <?php } ?>

        </div>

        <?php if ($matched == 0) { ?>
            <div class="dui-empty">
                <span class="dui-empty-ic"><?php echo icon('filter', 'ic ic-xl'); ?></span>
                <h3>لا توجد دورات في هذا التصنيف</h3>
                <p>اختر تصنيفاً آخر أو اعرض كل دوراتك.</p>
                <a class="dui-btn dui-btn-primary dui-btn-sm" href="courses.php">
                    <?php echo icon('grid', 'ic ic-xs'); ?> كل دوراتي
                </a>
            </div>
        <?php } ?>

    </section>

<?php } ?>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
