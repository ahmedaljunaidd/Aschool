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
    header("Location: my_courses.php");
    exit;
}

/* غير المسجلين ينتقلون إلى صفحة التعريف بالدورة */
if (!stu_is_enrolled($conn, $user_id, $course_id)) {
    header("Location: course_detail.php?id=$course_id");
    exit;
}

$course = stu_course($conn, $course_id);

if (!$course || !in_array($course["status"], array("active", "approved"), true)) {
    header("Location: my_courses.php");
    exit;
}

$lessons_result = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY lesson_order ASC, id ASC");
$lessons = array();
if ($lessons_result) {
    while ($row = mysqli_fetch_assoc($lessons_result)) {
        $lessons[] = $row;
    }
}

$completed_ids = stu_completed_lesson_ids($conn, $user_id, $course_id);
$progress = stu_progress($conn, $user_id, $course_id);

$favorite_ids = stu_favorite_ids($conn, $user_id);
$is_favorite = isset($favorite_ids[$course_id]);

$target = "course.php?id=$course_id";
if (!empty($progress["next"])) {
    $target = "lesson.php?id=" . (int) $progress["next"]["id"];
} elseif (count($lessons) > 0) {
    $target = "lesson.php?id=" . (int) $lessons[0]["id"];
}

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

<?php if (isset($_GET["done"])) { ?>
    <div class="stu-alert stu-alert-success">
        <?php echo icon("award", "ic"); ?>
        <span>أكملت جميع دروس هذه الدورة. يمكنك مراجعة أي درس في أي وقت.</span>
    </div>
<?php } ?>

<section class="stu-detail-hero">
    <?php echo stu_cover($course); ?>
    <div class="stu-detail-info">
        <span class="stu-course-cat"><?php echo icon(stu_category_icon($course["category_id"]), "ic ic-xs"); ?> <?php echo stu_e($category !== "" ? $category : "دورة"); ?></span>
        <h1><?php echo stu_e($course["title"]); ?></h1>
        <span class="stu-course-teacher">
            <span class="stu-mini-avatar"><?php echo stu_e(stu_first_char($teacher)); ?></span>
            <?php echo stu_e($teacher); ?>
        </span>
        <div class="stu-progress-row">
            <div class="stu-progress<?php echo $progress["percent"] >= 100 ? " is-done" : ""; ?>">
                <span style="width: <?php echo (int) $progress["percent"]; ?>%"></span>
            </div>
            <b class="stu-progress-num"><?php echo (int) $progress["done"]; ?>/<?php echo (int) $progress["total"]; ?> · <?php echo (int) $progress["percent"]; ?>%</b>
        </div>
        <div class="stu-detail-meta">
            <span><?php echo icon("book-open", "ic ic-xs"); ?> <?php echo count($lessons); ?> درس</span>
            <span><?php echo icon("users", "ic ic-xs"); ?> <?php echo (int) $course["learner_count"]; ?> متعلم</span>
        </div>
    </div>
    <div class="stu-enroll-box stu-enroll-box-wide">
        <a class="stu-btn stu-btn-primary" href="<?php echo stu_e($target); ?>">
            <?php echo icon("play", "ic ic-sm"); ?> <?php echo $progress["percent"] >= 100 ? "مراجعة الدورة" : "متابعة التعلم"; ?>
        </a>
        <form method="post" action="toggle_favorite.php">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="hidden" name="return" value="course.php?id=<?php echo $course_id; ?>">
            <button type="submit" class="stu-btn stu-btn-ghost">
                <?php echo icon("heart", "ic ic-sm"); ?> <?php echo $is_favorite ? "إزالة من المفضلة" : "أضف إلى المفضلة"; ?>
            </button>
        </form>
    </div>
</section>

<section class="stu-panel">
    <div class="stu-panel-head">
        <h2><?php echo icon("list", "ic ic-sm"); ?> دروس الدورة</h2>
        <span class="stu-muted"><?php echo (int) $progress["done"]; ?> مكتمل من <?php echo (int) $progress["total"]; ?></span>
    </div>

    <?php if (count($lessons) === 0) { ?>
        <div class="stu-empty">
            <span class="stu-empty-ic"><?php echo icon("book-open", "ic ic-xl"); ?></span>
            <h3>لا توجد دروس بعد</h3>
            <p>ستظهر الدروس هنا بمجرد إضافتها من المعلم.</p>
        </div>
    <?php } else { ?>
        <div class="stu-lessons">
            <?php foreach ($lessons as $index => $lesson) {
                $num = $index + 1;
                $lesson_id = (int) $lesson["id"];
                stu_lesson_row($lesson, $num, isset($completed_ids[$lesson_id]), !empty($progress["next"]) && (int) $progress["next"]["id"] === $lesson_id);
            } ?>
        </div>
    <?php } ?>
</section>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
