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

$favorite_ids = stu_favorite_ids($conn, $user_id);
$enrolled_ids = stu_enrolled_ids($conn, $user_id);

$courses = array();
if (count($favorite_ids) > 0) {
    $courses = stu_courses($conn, array("ids" => array_keys($favorite_ids)));
}

$return = "favorites.php";

$sidebar_letter = stu_first_char($user_name);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="المفضلة — منصة Aschool التعليمية: دورات معتمدة، دروس تفاعلية، وتقدّم محفوظ تلقائياً.">
    <title>المفضلة — Aschool</title>
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
        <span class="stu-eyebrow"><?php echo icon("heart", "ic ic-xs"); ?> قائمتك</span>
        <h1>المفضلة</h1>
        <p>الدورات التي حفظتها للرجوع إليها. سجّل في أي دورة عندما تكون جاهزاً.</p>
    </div>
    <div class="stu-hero-actions">
        <a class="stu-btn stu-btn-primary" href="browse.php"><?php echo icon("compass", "ic ic-sm"); ?> استكشف المزيد</a>
    </div>
</section>

<?php if (count($courses) === 0) { ?>
    <div class="stu-empty">
        <span class="stu-empty-ic"><?php echo icon("heart", "ic ic-xl"); ?></span>
        <h3>قائمة المفضلة فارغة</h3>
        <p>اضغط على رمز القلب في أي دورة لحفظها هنا.</p>
        <a class="stu-btn stu-btn-primary" href="browse.php"><?php echo icon("compass", "ic ic-sm"); ?> استكشف الدورات</a>
    </div>
<?php } else { ?>
    <section class="stu-section">
        <div class="stu-sec-head">
            <h2><?php echo icon("heart", "ic ic-sm"); ?> دورات محفوظة</h2>
            <span class="stu-muted"><?php echo count($courses); ?> دورة</span>
        </div>

        <div class="stu-courses">
            <?php foreach ($courses as $course) {
                $course_id = (int) $course["id"];
                $is_enrolled = isset($enrolled_ids[$course_id]);
                $progress = $is_enrolled ? stu_progress($conn, $user_id, $course_id) : null;
                echo stu_course_card($course, array(
                    "enrolled" => $is_enrolled,
                    "favorite" => true,
                    "progress" => $progress,
                    "return" => $return,
                ));
            } ?>
        </div>
    </section>
<?php } ?>

    </div>
</main>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
