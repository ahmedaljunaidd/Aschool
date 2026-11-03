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

$course_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($course_id <= 0) {
    header("Location: browse.php");
    exit;
}

/* المسجلون ينتقلون مباشرة إلى صفحة التعلم */
if (stu_is_enrolled($conn, $user_id, $course_id)) {
    header("Location: course.php?id=$course_id");
    exit;
}

$course = stu_course($conn, $course_id);

if (!$course || !in_array($course["status"], array("active", "approved"), true)) {
    $sidebar_letter = stu_first_char($user_name);
    ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="الدورة غير متاحة — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title>الدورة غير متاحة — Aschool</title>
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
    <div class="stu-empty">
        <span class="stu-empty-ic"><?php echo icon("alert", "ic ic-xl"); ?></span>
        <h3>هذه الدورة غير متاحة</h3>
        <p>قد تكون الدورة قيد المراجعة أو تم حذفها.</p>
        <a class="stu-btn stu-btn-primary" href="browse.php"><?php echo icon("compass", "ic ic-sm"); ?> استكشف الدورات</a>
    </div>
    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
    <?php
    exit;
}

$lessons_result = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY lesson_order ASC, id ASC");
$lessons = array();
if ($lessons_result) {
    while ($row = mysqli_fetch_assoc($lessons_result)) {
        $lessons[] = $row;
    }
}

$favorite_ids = stu_favorite_ids($conn, $user_id);
$is_favorite = isset($favorite_ids[$course_id]);
$teacher = stu_teacher_name($course);
$category = isset($course["category_name"]) ? trim((string) $course["category_name"]) : "";

$sidebar_letter = stu_first_char($user_name);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo stu_e($course["title"]); ?> — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title><?php echo stu_e($course["title"]); ?> — Aschool</title>
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

<?php if (isset($_GET["error"]) && $_GET["error"] === "unavailable") { ?>
    <div class="stu-alert">
        <?php echo icon("alert", "ic"); ?>
        <span>تعذّر إتمام التسجيل في هذه الدورة. ربما لم تعد معتمدة. جرّب دورة أخرى.</span>
    </div>
<?php } ?>

<section class="stu-detail-hero">
    <?php echo stu_cover($course); ?>
    <div class="stu-detail-info">
        <span class="stu-course-cat"><?php echo icon(stu_category_icon($course["category_id"]), "ic ic-xs"); ?> <?php echo stu_e($category !== "" ? $category : "دورة"); ?></span>
        <h1><?php echo stu_e($course["title"]); ?></h1>
        <p><?php echo stu_e(stu_excerpt($course["description"], 260)); ?></p>
        <span class="stu-course-teacher">
            <span class="stu-mini-avatar"><?php echo stu_e(stu_first_char($teacher)); ?></span>
            <?php echo stu_e($teacher); ?>
        </span>
        <div class="stu-detail-meta">
            <span><?php echo icon("book-open", "ic ic-xs"); ?> <?php echo count($lessons); ?> درس</span>
            <span><?php echo icon("users", "ic ic-xs"); ?> <?php echo (int) $course["learner_count"]; ?> متعلم</span>
            <span><?php echo icon("award", "ic ic-xs"); ?> شهادة إتمام</span>
        </div>
    </div>
</section>

<div class="stu-detail-grid">
    <section class="stu-panel">
        <div class="stu-panel-head">
            <h2><?php echo icon("list", "ic ic-sm"); ?> محتوى الدورة</h2>
            <span class="stu-muted"><?php echo count($lessons); ?> درس</span>
        </div>

        <?php if (count($lessons) === 0) { ?>
            <div class="stu-empty">
                <span class="stu-empty-ic"><?php echo icon("book-open", "ic ic-xl"); ?></span>
                <h3>لا توجد دروس بعد</h3>
                <p>سيتم إضافة الدروس قريباً.</p>
            </div>
        <?php } else { ?>
            <div class="stu-lessons">
                <?php foreach ($lessons as $index => $lesson) {
                    $num = $index + 1;
                    ?>
                    <div class="stu-lesson">
                        <span class="stu-lesson-num"><?php echo $num; ?></span>
                        <span class="stu-lesson-body">
                            <span class="stu-lesson-title"><?php echo stu_e($lesson["title"]); ?></span>
                            <span class="stu-lesson-meta">الدرس <?php echo $num; ?></span>
                        </span>
                        <span class="stu-lesson-state"><?php echo icon("lock", "ic ic-xs"); ?> بعد التسجيل</span>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

    <aside class="stu-panel">
        <div class="stu-panel-head">
            <h2><?php echo icon("zap", "ic ic-sm"); ?> ابدأ الآن</h2>
        </div>
        <div class="stu-enroll-box">
            <form method="post" action="enroll_course.php">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <button type="submit" class="stu-btn stu-btn-primary">
                    <?php echo icon("play", "ic ic-sm"); ?> سجّل في الدورة مجاناً
                </button>
            </form>
            <form method="post" action="toggle_favorite.php">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="return" value="course_detail.php?id=<?php echo $course_id; ?>">
                <button type="submit" class="stu-btn stu-btn-ghost">
                    <?php echo icon("heart", "ic ic-sm"); ?> <?php echo $is_favorite ? "إزالة من المفضلة" : "أضف إلى المفضلة"; ?>
                </button>
            </form>
            <div class="stu-enroll-note">
                <?php echo icon("check-circle", "ic ic-sm"); ?>
                <span>وصول كامل لكل الدروس، وتُحفظ نسبة تقدمك تلقائياً بعد كل درس.</span>
            </div>
        </div>
    </aside>
</div>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
