<?php

session_start();

include "../includes/connection.php";
require_once "../includes/student_helpers.php";
require_once "../includes/format.php";

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

$lesson_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($lesson_id <= 0) {
    header("Location: my_courses.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM lessons WHERE id = $lesson_id LIMIT 1");

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: my_courses.php");
    exit;
}

$lesson = mysqli_fetch_assoc($result);
$course_id = (int) $lesson["course_id"];

if (!stu_is_enrolled($conn, $user_id, $course_id)) {
    header("Location: course_detail.php?id=$course_id");
    exit;
}

$course = stu_course($conn, $course_id);

if (!$course || !in_array($course["status"], array("active", "approved"), true)) {
    header("Location: course.php?id=$course_id");
    exit;
}

$lessons_result = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY lesson_order ASC, id ASC");
$lessons = array();
if ($lessons_result) {
    while ($row = mysqli_fetch_assoc($lessons_result)) {
        $lessons[] = $row;
    }
}

$current_index = 0;
$prev_lesson = null;
$next_lesson = null;

foreach ($lessons as $index => $row) {
    if ((int) $row["id"] === $lesson_id) {
        $current_index = $index;
        if ($index > 0) $prev_lesson = $lessons[$index - 1];
        if ($index < count($lessons) - 1) $next_lesson = $lessons[$index + 1];
        break;
    }
}

$completed_ids = stu_completed_lesson_ids($conn, $user_id, $course_id);
$progress = stu_progress($conn, $user_id, $course_id);
$is_done = isset($completed_ids[$lesson_id]);
$is_last = $next_lesson === null;

$sidebar_letter = stu_first_char($user_name);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo stu_e($lesson["title"]); ?> — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title><?php echo stu_e($lesson["title"]); ?> — Aschool</title>
    <link rel="stylesheet" href="../css/style.css?v=29">
    <link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">
    <link rel="stylesheet" href="../css/student.css?v=29">
</head>

<body class="theme-dark student-page shell-page lesson-page" data-user-name="<?php echo stu_e($user_name); ?>">

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

<div class="stu-player">

    <div class="stu-player-head">
        <a class="stu-btn stu-btn-ghost stu-btn-sm" href="course.php?id=<?php echo $course_id; ?>">
            <?php echo icon("arrow-right", "ic ic-xs"); ?> الدورة
        </a>
        <div class="stu-player-head-body">
            <span class="stu-player-kicker"><?php echo stu_e($course["title"]); ?> · الدرس <?php echo $current_index + 1; ?> من <?php echo count($lessons); ?></span>
            <h1><?php echo stu_e($lesson["title"]); ?></h1>
        </div>
        <?php if ($is_done) { ?>
            <span class="stu-btn stu-btn-soft stu-btn-sm stu-btn-static"><?php echo icon("check-circle", "ic ic-xs"); ?> مكتمل</span>
        <?php } ?>
    </div>

    <div class="stu-player-progress">
        <div class="stu-progress<?php echo $progress["percent"] >= 100 ? " is-done" : ""; ?>">
            <span style="width: <?php echo (int) $progress["percent"]; ?>%"></span>
        </div>
        <b><?php echo (int) $progress["percent"]; ?>%</b>
    </div>

    <div class="stu-player-card">
        <?php if (isset($lesson["video"]) && trim((string) $lesson["video"]) !== "") {
            echo stu_video_embed($lesson["video"]);
        } ?>

        <?php if (isset($lesson["content"]) && trim((string) $lesson["content"]) !== "") { ?>
            <div class="lesson-content">
                <?php echo render_lesson_content($lesson["content"]); ?>
            </div>
        <?php } elseif (!isset($lesson["video"]) || trim((string) $lesson["video"]) === "") { ?>
            <div class="stu-empty">
                <span class="stu-empty-ic"><?php echo icon("file", "ic ic-xl"); ?></span>
                <h3>لا يوجد محتوى لهذا الدرس</h3>
                <p>انتقل إلى الدرس التالي أو عد لاحقاً.</p>
            </div>
        <?php } ?>

        <div class="stu-player-nav">
            <?php if ($prev_lesson !== null) { ?>
                <a class="stu-btn stu-btn-ghost" href="lesson.php?id=<?php echo (int) $prev_lesson["id"]; ?>">
                    <?php echo icon("arrow-right", "ic ic-sm"); ?> الدرس السابق
                </a>
            <?php } else { ?>
                <a class="stu-btn stu-btn-ghost" href="course.php?id=<?php echo $course_id; ?>">
                    <?php echo icon("arrow-right", "ic ic-sm"); ?> الدورة
                </a>
            <?php } ?>

            <form method="post" action="next_lesson.php">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                <button type="submit" class="stu-btn stu-btn-primary">
                    <?php echo $is_last ? "إنهاء الدرس" : "الدرس التالي"; ?>
                    <?php echo icon($is_last ? "check" : "arrow-left", "ic ic-sm"); ?>
                </button>
            </form>
        </div>
    </div>

</div>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
