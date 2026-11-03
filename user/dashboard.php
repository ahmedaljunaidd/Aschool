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

$my_courses = stu_my_courses($conn, $user_id);
$enrolled_ids = stu_enrolled_ids($conn, $user_id);
$favorite_ids = stu_favorite_ids($conn, $user_id);
$categories = stu_categories($conn);

$total_done = 0;
$continue_course = null;
$continue_progress = null;
$fallback_course = null;
$fallback_progress = null;
$progress_map = array();

foreach ($my_courses as $course) {
    $progress = stu_progress($conn, $user_id, (int) $course["id"]);
    $progress_map[(int) $course["id"]] = $progress;
    $total_done += $progress["done"];

    if ($continue_course === null && $progress["percent"] < 100 && $progress["total"] > 0) {
        $continue_course = $course;
        $continue_progress = $progress;
    }

    if ($fallback_course === null && $progress["total"] > 0) {
        $fallback_course = $course;
        $fallback_progress = $progress;
    }
}

if ($continue_course === null) {
    $continue_course = $fallback_course;
    $continue_progress = $fallback_progress;
}

if ($continue_course === null && count($my_courses) > 0) {
    $continue_course = $my_courses[0];
    $continue_progress = $progress_map[(int) $continue_course["id"]];
}

$recommended = stu_courses($conn, array(
    "exclude" => array_keys($enrolled_ids),
    "limit" => 6,
));

$return = "dashboard.php";

$sidebar_letter = stu_first_char($user_name);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="الرئيسية — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title>الرئيسية — Aschool</title>
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
        <span class="stu-eyebrow"><?php echo icon("sparkles", "ic ic-xs"); ?> لوحة الطالب</span>
        <h1>أهلاً، <?php echo stu_e($user_name); ?></h1>
        <p>واصل رحلتك التعليمية. كل درس تكمله يُحدّث تقدمك تلقائياً.</p>
    </div>
    <div class="stu-hero-actions">
        <a class="stu-btn stu-btn-primary" href="browse.php"><?php echo icon("compass", "ic ic-sm"); ?> استكشف الدورات</a>
        <a class="stu-btn stu-btn-ghost" href="my_courses.php"><?php echo icon("book-open", "ic ic-sm"); ?> دوراتي</a>
    </div>
</section>

<div class="stu-stats">
    <?php
    stu_stat("book-open", count($my_courses), "دورة مسجّلة", "");
    stu_stat("check-circle", $total_done, "درس مكتمل", "is-green");
    stu_stat("folder", count($categories), "تصنيف تعليمي", "is-violet");
    ?>
</div>

<?php if ($continue_course !== null) {
    $progress = $continue_progress;
    $target = "course.php?id=" . (int) $continue_course["id"];
    if (!empty($progress["next"])) {
        $target = "lesson.php?id=" . (int) $progress["next"]["id"];
    }
    ?>
    <section class="stu-section">
        <div class="stu-sec-head">
            <h2><?php echo icon("play", "ic ic-sm"); ?> تابع من حيث توقفت</h2>
            <a class="stu-sec-link" href="my_courses.php">كل دوراتي <?php echo icon("arrow-left", "ic ic-xs"); ?></a>
        </div>

        <div class="stu-continue">
            <a href="<?php echo stu_e($target); ?>" aria-label="<?php echo stu_e($continue_course["title"]); ?>">
                <?php echo stu_cover($continue_course); ?>
            </a>
            <div class="stu-continue-body">
                <span class="stu-continue-label"><?php echo icon("clock", "ic ic-xs"); ?> التعلّم مستمر</span>
                <h3><?php echo stu_e($continue_course["title"]); ?></h3>
                <small><?php echo stu_e(stu_teacher_name($continue_course)); ?> · <?php echo (int) $progress["done"]; ?> من <?php echo (int) $progress["total"]; ?> درس</small>
                <div class="stu-progress-row">
                    <div class="stu-progress<?php echo $progress["percent"] >= 100 ? " is-done" : ""; ?>">
                        <span style="width: <?php echo (int) $progress["percent"]; ?>%"></span>
                    </div>
                    <b class="stu-progress-num"><?php echo (int) $progress["percent"]; ?>%</b>
                </div>
            </div>
            <a class="stu-btn stu-btn-primary" href="<?php echo stu_e($target); ?>">
                <?php echo icon("play", "ic ic-sm"); ?> <?php
                if ($progress["total"] === 0) {
                    echo "عرض الدورة";
                } elseif ($progress["percent"] >= 100) {
                    echo "مراجعة الدورة";
                } else {
                    echo "متابعة الدرس";
                }
                ?>
            </a>
        </div>
    </section>
<?php } ?>

<section class="stu-section">
    <div class="stu-sec-head">
        <h2><?php echo icon("folder", "ic ic-sm"); ?> التصنيفات</h2>
        <a class="stu-sec-link" href="browse.php">عرض الكل <?php echo icon("arrow-left", "ic ic-xs"); ?></a>
    </div>

    <div class="stu-cats">
        <?php foreach ($categories as $category) {
            $cat_id = (int) $category["id"];
            ?>
            <a class="stu-cat" href="browse.php?category=<?php echo $cat_id; ?>">
                <span class="stu-cat-ic cat-<?php echo $cat_id; ?>"><?php echo icon(stu_category_icon($cat_id), "ic"); ?></span>
                <span class="stu-cat-body">
                    <strong><?php echo stu_e(trim((string) $category["name"])); ?></strong>
                    <span><?php echo (int) $category["course_count"]; ?> دورة</span>
                </span>
                <span class="stu-cat-arrow"><?php echo icon("chevron-left", "ic ic-sm"); ?></span>
            </a>
        <?php } ?>
    </div>
</section>

<section class="stu-section">
    <div class="stu-sec-head">
        <h2><?php echo icon("sparkles", "ic ic-sm"); ?> مقترحة لك</h2>
        <a class="stu-sec-link" href="browse.php">المزيد <?php echo icon("arrow-left", "ic ic-xs"); ?></a>
    </div>

    <?php if (count($recommended) === 0) { ?>
        <div class="stu-empty">
            <span class="stu-empty-ic"><?php echo icon("award", "ic ic-xl"); ?></span>
            <h3>أنت مسجّل في كل الدورات المتاحة</h3>
            <p>تابع دروسك الحالية أو عد لاحقاً عند إضافة دورات جديدة.</p>
            <a class="stu-btn stu-btn-primary" href="my_courses.php"><?php echo icon("book-open", "ic ic-sm"); ?> دوراتي</a>
        </div>
    <?php } else { ?>
        <div class="stu-courses">
            <?php foreach ($recommended as $course) {
                echo stu_course_card($course, array(
                    "enrolled" => false,
                    "favorite" => isset($favorite_ids[(int) $course["id"]]),
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
