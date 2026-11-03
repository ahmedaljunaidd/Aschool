<?php

session_start();

include "../includes/connection.php";
require_once "../includes/student_helpers.php";

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

$user_id = (int) $_SESSION["user_id"];
$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "طالب";

$q = isset($_GET["q"]) ? trim((string) $_GET["q"]) : "";
$active_category = isset($_GET["category"]) ? (int) $_GET["category"] : 0;

$categories = stu_categories($conn);
$courses = stu_courses($conn, array(
    "q" => $q,
    "category_id" => $active_category,
    "limit" => 60,
));

$enrolled_ids = stu_enrolled_ids($conn, $user_id);
$favorite_ids = stu_favorite_ids($conn, $user_id);

$active_category_name = "";
foreach ($categories as $category) {
    if ((int) $category["id"] === $active_category) {
        $active_category_name = trim((string) $category["name"]);
        break;
    }
}

$return = "browse.php";
$query_parts = array();
if ($q !== "") $query_parts[] = "q=" . rawurlencode($q);
if ($active_category > 0) $query_parts[] = "category=" . $active_category;
if (count($query_parts) > 0) $return .= "?" . implode("&", $query_parts);

$sidebar_letter = stu_first_char($user_name);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="استكشف الدورات — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title>استكشف الدورات — Aschool</title>
    <link rel="stylesheet" href="../css/style.css?v=29">
    <link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">
    <link rel="stylesheet" href="../css/student.css?v=29">
</head>

<body class="theme-dark student-page shell-page" data-user-name="<?php echo stu_e($user_name); ?>">

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
            <small>لوحة الطالب</small>
        </span>
    </div>

    <div class="side-user">
        <span class="side-avatar"><?php echo stu_e($sidebar_letter); ?></span>
        <span class="side-user-meta">
            <strong><?php echo stu_e($user_name); ?></strong>
            <small>حساب طالب</small>
        </span>
    </div>

    <div class="sidebar-section-title">التعلّم</div>

    <a href="dashboard.php">
        <span class="nav-ic"><?php echo icon('home', 'ic ic-sm'); ?></span>
        الرئيسية
    </a>

    <a href="browse.php">
        <span class="nav-ic"><?php echo icon('compass', 'ic ic-sm'); ?></span>
        استكشف الدورات
    </a>

    <a href="my_courses.php">
        <span class="nav-ic"><?php echo icon('book-open', 'ic ic-sm'); ?></span>
        دوراتي
    </a>

    <a href="favorites.php">
        <span class="nav-ic"><?php echo icon('heart', 'ic ic-sm'); ?></span>
        المفضلة
    </a>

    <div class="sidebar-section-title">الحساب</div>

    <a href="profile.php">
        <span class="nav-ic"><?php echo icon('user', 'ic ic-sm'); ?></span>
        الملف الشخصي
    </a>

    <div class="sidebar-footer">
        منصة <strong>Aschool</strong> التعليمية
    </div>

</aside>

<button type="button" class="side-backdrop" data-sidebar-toggle tabindex="-1" aria-label="إغلاق القائمة"></button>

<div class="navbar">
    <a class="logout" href="../logout.php">تسجيل الخروج</a>
</div>

<main class="content student-content">
    <div class="student-wrap">

<section class="stu-hero">
    <div class="stu-hero-body">
        <span class="stu-eyebrow"><?php echo icon("compass", "ic ic-xs"); ?> اكتشف مسارك</span>
        <h1>استكشف الدورات</h1>
        <p>كل الدورات المعتمدة في مكان واحد. اختر التصنيف الذي يناسبك وابدأ التعلم مباشرة.</p>
    </div>
    <div class="stu-hero-actions">
        <a class="stu-btn stu-btn-ghost" href="my_courses.php"><?php echo icon("book-open", "ic ic-sm"); ?> دوراتي</a>
    </div>
</section>

<form class="stu-search" method="get" action="browse.php">
    <span class="stu-search-ic"><?php echo icon("search", "ic ic-sm"); ?></span>
    <input type="search" name="q" value="<?php echo stu_e($q); ?>" placeholder="ابحث عن دورة أو موضوع..." aria-label="البحث عن دورة">
    <?php if ($active_category > 0) { ?>
        <input type="hidden" name="category" value="<?php echo $active_category; ?>">
    <?php } ?>
    <button type="submit">بحث</button>
</form>

<div class="stu-chips">
    <a class="stu-chip<?php echo $active_category === 0 ? " is-active" : ""; ?>" href="browse.php<?php echo $q !== "" ? "?q=" . rawurlencode($q) : ""; ?>">
        كل الدورات
    </a>
    <?php foreach ($categories as $category) {
        $cat_id = (int) $category["id"];
        $cat_href = "browse.php?category=" . $cat_id;
        if ($q !== "") $cat_href .= "&q=" . rawurlencode($q);
        ?>
        <a class="stu-chip<?php echo $active_category === $cat_id ? " is-active" : ""; ?>" href="<?php echo stu_e($cat_href); ?>">
            <?php echo stu_e(trim((string) $category["name"])); ?>
            <small><?php echo (int) $category["course_count"]; ?></small>
        </a>
    <?php } ?>
</div>

<section class="stu-section">
    <div class="stu-sec-head">
        <h2>
            <?php echo icon("grid", "ic ic-sm"); ?>
            <?php
            if ($active_category_name !== "") {
                echo stu_e($active_category_name);
            } elseif ($q !== "") {
                echo "نتائج البحث";
            } else {
                echo "أحدث الدورات";
            }
            ?>
        </h2>
        <span class="stu-muted"><?php echo count($courses); ?> دورة</span>
    </div>

    <?php if (count($courses) === 0) { ?>
        <div class="stu-empty">
            <span class="stu-empty-ic"><?php echo icon("search", "ic ic-xl"); ?></span>
            <h3>لا توجد دورات مطابقة</h3>
            <p>جرّب كلمة بحث أخرى أو اختر تصنيفاً مختلفاً.</p>
            <a class="stu-btn stu-btn-primary" href="browse.php"><?php echo icon("compass", "ic ic-sm"); ?> عرض كل الدورات</a>
        </div>
    <?php } else { ?>
        <div class="stu-courses">
            <?php foreach ($courses as $course) {
                $course_id = (int) $course["id"];
                $is_enrolled = isset($enrolled_ids[$course_id]);
                $progress = $is_enrolled ? stu_progress($conn, $user_id, $course_id) : null;
                echo stu_course_card($course, array(
                    "enrolled" => $is_enrolled,
                    "favorite" => isset($favorite_ids[$course_id]),
                    "progress" => $progress,
                    "return" => $return,
                ));
            } ?>
        </div>
    <?php } ?>
</section>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
