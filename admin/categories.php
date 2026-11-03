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


$sql = "SELECT * FROM categories ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<?php
require_once "../includes/dash_helpers.php";

$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "مدير المنصة";

// عدد التصنيفات
$total_categories = mysqli_num_rows($result);

// عدد الدورات
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
$count_row = mysqli_fetch_assoc($count_result);
$total_courses = $count_row["total"];

// عدد الدروس
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons");
$count_row = mysqli_fetch_assoc($count_result);
$total_lessons = $count_row["total"];

// عدد المدرسين
$count_result = mysqli_query($conn, "SELECT COUNT(DISTINCT teacher_id) AS total FROM courses WHERE teacher_id IS NOT NULL");
$count_row = mysqli_fetch_assoc($count_result);
$total_teachers = $count_row["total"];

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
    <meta name="description" content="التصنيفات — لوحة Aschool لإدارة المحتوى التعليمي والدورات والدروس.">
    <title>التصنيفات — Aschool</title>
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

<a class="dui-back" href="dashboard.php">
    <?php echo icon('arrow-right', 'ic ic-sm'); ?>
    العودة إلى لوحة التحكم
</a>

<section class="dui-hero">
    <div class="dui-hero-body">
        <span class="dui-eyebrow"><?php echo icon('folder', 'ic ic-xs'); ?> إدارة المحتوى</span>
        <h1>التصنيفات</h1>
        <p>نظّم دورات المنصة داخل تصنيفات واضحة. اضغط على أي تصنيف لعرض دوراته ودروسه وأسماء المدرسين المسؤولين عنه.</p>
    </div>
    <div class="dui-hero-actions">
        <a class="dui-btn dui-btn-primary" href="add_category.php">
            <?php echo icon('folder-plus', 'ic ic-sm'); ?>
            إضافة تصنيف
        </a>
        <a class="dui-btn dui-btn-ghost" href="courses.php">
            <?php echo icon('book', 'ic ic-sm'); ?>
            كل الدورات
        </a>
    </div>
</section>

<div class="dui-stats">

    <div class="dui-stat">
        <span class="dui-stat-ic"><?php echo icon('folder', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $total_categories; ?></b>
            <span>تصنيف</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-violet"><?php echo icon('book', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $total_courses; ?></b>
            <span>دورة</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-green"><?php echo icon('play', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $total_lessons; ?></b>
            <span>درس</span>
        </span>
    </div>

    <div class="dui-stat">
        <span class="dui-stat-ic tint-amber"><?php echo icon('users', 'ic'); ?></span>
        <span class="dui-stat-body">
            <b><?php echo $total_teachers; ?></b>
            <span>مدرس</span>
        </span>
    </div>

</div>

<section class="dui-section">
    <div class="dui-sec-head">
        <h2><?php echo icon('grid', 'ic ic-sm'); ?> كل التصنيفات</h2>
        <span class="dui-sec-meta"><?php echo $total_categories; ?> تصنيف</span>
    </div>

    <?php if ($total_categories == 0) { ?>

        <div class="dui-empty">
            <span class="dui-empty-ic"><?php echo icon('folder', 'ic ic-xl'); ?></span>
            <h3>لا توجد تصنيفات بعد</h3>
            <p>ابدأ بإنشاء أول تصنيف لتنظيم دورات المنصة.</p>
            <a class="dui-btn dui-btn-primary dui-btn-sm" href="add_category.php">
                <?php echo icon('plus', 'ic ic-xs'); ?> إضافة تصنيف
            </a>
        </div>

    <?php } else { ?>

        <div class="dui-cat-grid">

            <?php while ($category = mysqli_fetch_assoc($result)) { ?>

                <?php
                $category_id = $category["id"];
                $category_name = trim($category["name"]);
                $category_desc = trim($category["description"]);

                // عدد دورات التصنيف
                $cat_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses WHERE category_id = $category_id");
                $cat_row = mysqli_fetch_assoc($cat_query);
                $category_courses = $cat_row["total"];

                // عدد دروس التصنيف
                $cat_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons INNER JOIN courses ON courses.id = lessons.course_id WHERE courses.category_id = $category_id");
                $cat_row = mysqli_fetch_assoc($cat_query);
                $category_lessons = $cat_row["total"];

                // عدد مدرسي التصنيف
                $cat_query = mysqli_query($conn, "SELECT COUNT(DISTINCT teacher_id) AS total FROM courses WHERE category_id = $category_id AND teacher_id IS NOT NULL");
                $cat_row = mysqli_fetch_assoc($cat_query);
                $category_teachers = $cat_row["total"];

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

                // أول حرف من اسم التصنيف
                if (function_exists("mb_substr")) {
                    $first_char = mb_substr($category_name, 0, 1, "UTF-8");
                } else {
                    $first_char = substr($category_name, 0, 1);
                }

                // وصف مختصر
                if ($category_desc == "") {
                    $short_desc = "تصنيف تعليمي داخل منصة Aschool.";
                } else {
                    if (function_exists("mb_substr")) {
                        $short_desc = mb_substr($category_desc, 0, 95, "UTF-8");
                    } else {
                        $short_desc = substr($category_desc, 0, 95);
                    }
                }
                ?>

                <article class="dui-cat">

                    <a class="dui-cat-main" href="category_courses.php?id=<?php echo $category_id; ?>">

                        <div class="dui-cat-cover <?php echo $cat_tint; ?>">
                            <span class="dui-cover-ic"><?php echo icon($cat_icon, 'ic ic-lg'); ?></span>
                            <span class="dui-cover-letter"><?php echo dui_e($first_char); ?></span>
                        </div>

                        <div class="dui-cat-body">
                            <h3 class="dui-cat-title"><?php echo dui_e($category_name); ?></h3>
                            <span class="dui-cat-desc"><?php echo dui_e($short_desc); ?></span>

                            <div class="dui-cat-meta">
                                <span><?php echo icon('book', 'ic ic-xs'); ?> <?php echo $category_courses; ?> دورة</span>
                                <span><?php echo icon('play', 'ic ic-xs'); ?> <?php echo $category_lessons; ?> درس</span>
                                <span><?php echo icon('users', 'ic ic-xs'); ?> <?php echo $category_teachers; ?> مدرس</span>
                            </div>
                        </div>

                    </a>

                    <div class="dui-cat-foot">

                        <span class="dui-cat-open">
                            <?php echo icon('eye', 'ic ic-xs'); ?>
                            عرض الدورات
                        </span>

                        <div class="dui-actions">
                            <a class="dui-btn dui-btn-ghost dui-btn-sm" href="edit_category.php?id=<?php echo $category_id; ?>">
                                <?php echo icon('edit', 'ic ic-xs'); ?> تعديل
                            </a>
                            <a class="dui-btn dui-btn-danger dui-btn-sm"
                               href="delete_category.php?id=<?php echo $category_id; ?>"
                               onclick="return confirm('هل أنت متأكد من حذف التصنيف؟');">
                                <?php echo icon('trash', 'ic ic-xs'); ?> حذف
                            </a>
                        </div>

                    </div>

                </article>

            <?php } ?>

        </div>

    <?php } ?>

</section>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
