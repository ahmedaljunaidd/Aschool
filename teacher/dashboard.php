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


/* عدد دورات المدرس */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id";

$result = mysqli_query($conn, $sql);

$courses = mysqli_fetch_assoc($result);


/* الدورات المقبولة */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id
        AND status = 'approved'";

$result = mysqli_query($conn, $sql);

$approved = mysqli_fetch_assoc($result);


/* الدورات المنتظرة */

$sql = "SELECT COUNT(*) AS total
        FROM courses
        WHERE teacher_id = $teacher_id
        AND status = 'pending'";

$result = mysqli_query($conn, $sql);

$pending = mysqli_fetch_assoc($result);

?>

<?php
require_once "../includes/dash_helpers.php";

$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "معلم";

// عدد الدورات المنشورة
$active_result = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM courses
     WHERE teacher_id = $teacher_id
     AND status = 'active'");
$active_row = mysqli_fetch_assoc($active_result);
$active_count = (int) $active_row["total"];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="لوحة المدرس في منصة Aschool: تابع دوراتك ودروسك وحالة دوراتك وإحصاءاتك التعليمية.">
    <title>لوحة المدرس — Aschool</title>

    <link rel="stylesheet" href="../css/style.css?v=29">
    <link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">
    <link rel="stylesheet" href="../css/dash-ui.css?v=29">

</head>

<body class="theme-dark dash-page dash-ui">

<script>
(function () {
    try {
        if (localStorage.getItem('aschool.theme') === 'light') {
            document.body.classList.remove('theme-dark');
            document.body.classList.add('theme-light');
        }
    } catch (e) { /* ignore */ }
})();
</script>

<button type="button" class="side-backdrop" data-sidebar-toggle tabindex="-1" aria-label="إغلاق القائمة"></button>

<div class="dash-shell">

    <aside class="sidebar dash-sidebar">

        <div class="side-brand">
            <span class="brand-mark">A</span>
            <span class="side-brand-text">
                <strong>Aschool</strong>
                <small>لوحة المدرس</small>
            </span>
        </div>

        <div class="side-user">
            <span class="side-avatar"><?php echo function_exists('mb_substr') ? mb_substr($_SESSION["name"], 0, 1, 'UTF-8') : 'T'; ?></span>
            <span class="side-user-meta">
                <strong><?php echo $_SESSION["name"]; ?></strong>
                <small>مدرّس</small>
            </span>
        </div>

        <div class="sidebar-section-title">القائمة الرئيسية</div>

        <a href="dashboard.php" class="active">
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
            عرض المنصة
        </a>

        <div class="sidebar-footer">
            منصة <strong>Aschool</strong> التعليمية
        </div>

    </aside>

    <div class="dash-body">

        <header class="navbar dash-topbar">

            <button type="button" class="icon-btn side-toggle" data-sidebar-toggle aria-label="فتح القائمة">
                <?php echo icon('menu', 'ic'); ?>
            </button>

            <div class="topbar-title">
                <small>مساحتي التعليمية</small>
                <h2>لوحة المدرس</h2>
            </div>

            <div class="topbar-actions">

                <button type="button" class="icon-btn" data-reminder-open aria-label="التذكيرات">
                    <?php echo icon('bell', 'ic'); ?>
                    <span class="icon-badge" data-reminder-badge hidden>0</span>
                </button>

                <button type="button" class="icon-btn" data-focus-open aria-label="وضع التركيز">
                    <?php echo icon('clock', 'ic'); ?>
                </button>

                <a class="logout" href="../logout.php">
                    <?php echo icon('log-out', 'ic ic-sm'); ?> تسجيل الخروج
                </a>

            </div>

        </header>

        <main class="content dash-content">

            <section class="dash-hero">

                <div>
                    <span class="hero-eyebrow"><?php echo icon('sparkles', 'ic ic-sm'); ?> <span data-today>اليوم</span></span>
                    <h1>مرحباً، <?php echo $_SESSION["name"]; ?> 👋</h1>
                    <p>تابع دوراتك، أضف دروساً جديدة، وراجع حالة دوراتك بسهولة من مكان واحد.</p>
                </div>

                <div class="hero-actions">
                    <a href="add_course.php" class="btn btn-primary"><?php echo icon('plus', 'ic ic-sm'); ?> دورة جديدة</a>
                    <a href="courses.php" class="btn btn-secondary"><?php echo icon('book', 'ic ic-sm'); ?> دوراتي</a>
                    <button type="button" class="btn btn-ghost" data-reminder-open><?php echo icon('bell', 'ic ic-sm'); ?> تذكير</button>
                </div>

            </section>

            <div class="kpi-grid">

                <a class="kpi" href="courses.php">
                    <span class="kpi-icon tint-blue"><?php echo icon('book', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">جميع دوراتي</span>
                        <strong class="kpi-value"><?php echo $courses["total"]; ?></strong>
                        <span class="kpi-meta">إجمالي الدورات</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="teacher_active.php">
                    <span class="kpi-icon tint-green"><?php echo icon('check-circle', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">الدورات المقبولة</span>
                        <strong class="kpi-value"><?php echo $active_count; ?></strong>
                        <span class="kpi-meta">منشورة على المنصة</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="cuorse_pending.php">
                    <span class="kpi-icon tint-amber"><?php echo icon('clock', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">قيد المراجعة</span>
                        <strong class="kpi-value"><?php echo $pending["total"]; ?></strong>
                        <span class="kpi-meta">بانتظار موافقة الإدارة</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="../courses.php">
                    <span class="kpi-icon tint-violet"><?php echo icon('globe', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">المنصة</span>
                        <strong class="kpi-value is-word">تصفح الكورسات</strong>
                        <span class="kpi-meta">شاهد ما هو متاح</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

            </div>

            <div class="dash-grid">

               <section class="panel">

                    <div class="panel-head">
                        <h2><?php echo icon('chart', 'ic ic-sm'); ?> حالة الدورات</h2>
                        <a class="panel-action" href="courses.php">عرض الدورات</a>
                    </div>

                    <div class="status-list">

                        <div class="status-row">
                            <div class="status-top">
                                <span>دورات مقبولة</span>
                                <strong><?php echo $active_count; ?></strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill is-green" data-bar="<?php echo $active_count; ?>" data-max="<?php echo $courses["total"]; ?>"></span>
                            </div>
                        </div>

                        <div class="status-row">
                            <div class="status-top">
                                <span>قيد المراجعة</span>
                                <strong><?php echo $pending["total"]; ?></strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill is-amber" data-bar="<?php echo $pending["total"]; ?>" data-max="<?php echo $courses["total"]; ?>"></span>
                            </div>
                        </div>

                    </div>

                </section>



                <section class="panel">

                    <div class="panel-head">
                        <h2><?php echo icon('bell', 'ic ic-sm'); ?> تذكيراتي</h2>
                        <button type="button" class="panel-action" data-reminder-open><?php echo icon('plus', 'ic ic-sm'); ?> جديد</button>
                    </div>

                    <div data-reminder-inline></div>

                </section>

            </div>

            <section class="dui-section dui-section-gap">
                <div class="dui-sec-head">
                    <h2><?php echo icon('book-open', 'ic ic-sm'); ?> دوراتي</h2>
                    <a class="dui-link" href="courses.php">
                        عرض كل الدورات
                        <?php echo icon('arrow-left', 'ic ic-xs'); ?>
                    </a>
                </div>

                <?php
                // عدد دورات المعلم في كل تصنيف
                $cat_result = mysqli_query($conn,
                    "SELECT courses.category_id, categories.name, COUNT(*) AS total
                     FROM courses
                     LEFT JOIN categories ON categories.id = courses.category_id
                     WHERE courses.teacher_id = $teacher_id
                     GROUP BY courses.category_id, categories.name");

                if (mysqli_num_rows($cat_result) > 0) { ?>
                    <div class="dui-chips">
                        <?php while ($cat = mysqli_fetch_assoc($cat_result)) {
                            $cat_id = (int) $cat["category_id"];
                            $cat_name = trim($cat["name"]);

                            if ($cat_name == "") {
                                $cat_name = "بدون تصنيف";
                            }
                            ?>
                            <a class="dui-chip" href="courses.php?category=<?php echo $cat_id; ?>">
                                <?php echo dui_e($cat_name); ?>
                                <small><?php echo $cat["total"]; ?></small>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php
                // أحدث 6 دورات للمعلم
                $recent_result = mysqli_query($conn,
                    "SELECT * FROM courses
                     WHERE teacher_id = $teacher_id
                     ORDER BY id DESC
                     LIMIT 6");

                if (mysqli_num_rows($recent_result) == 0) { ?>
                    <div class="dui-empty">
                        <span class="dui-empty-ic"><?php echo icon('book', 'ic ic-xl'); ?></span>
                        <h3>لا توجد دورات بعد</h3>
                        <p>أنشئ دورتك الأولى وابدأ بإضافة الدروس.</p>
                        <a class="dui-btn dui-btn-primary dui-btn-sm" href="add_course.php">
                            <?php echo icon('plus', 'ic ic-xs'); ?> إنشاء دورة
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="dui-course-grid">
                        <?php while ($course = mysqli_fetch_assoc($recent_result)) {
                            $course_id = (int) $course["id"];
                            $course_title = $course["title"];
                            $course_cat = (int) $course["category_id"];

                            // اسم التصنيف
                            $category_name = "بدون تصنيف";
                            if ($course_cat > 0) {
                                $name_result = mysqli_query($conn, "SELECT name FROM categories WHERE id = $course_cat LIMIT 1");
                                if (mysqli_num_rows($name_result) > 0) {
                                    $name_row = mysqli_fetch_assoc($name_result);
                                    $category_name = trim($name_row["name"]);
                                }
                            }

                            // عدد دروس الدورة
                            $count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons WHERE course_id = $course_id");
                            $count_row = mysqli_fetch_assoc($count_result);
                            $lesson_count = (int) $count_row["total"];

                            // عدد متعلمي الدورة
                            $count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE course_id = $course_id");
                            $count_row = mysqli_fetch_assoc($count_result);
                            $learner_count = (int) $count_row["total"];

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
                            } elseif ($course_status == "draft") {
                                $status_label = "مسودة";
                                $status_class = "is-draft";
                                $status_icon = "edit";
                            } else {
                                $status_label = "غير محددة";
                                $status_class = "is-draft";
                                $status_icon = "info";
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

                            // أول حرف من اسم المعلم
                            if (function_exists("mb_substr")) {
                                $teacher_letter = mb_substr($user_name, 0, 1, "UTF-8");
                            } else {
                                $teacher_letter = substr($user_name, 0, 1);
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
                                    <div class="dui-meta-row">
                                        <span><?php echo icon('play', 'ic ic-xs'); ?> <?php echo $lesson_count; ?> درس</span>
                                        <span><?php echo icon('users', 'ic ic-xs'); ?> <?php echo $learner_count; ?> متعلم</span>
                                    </div>
                                    <span class="dui-teacher">
                                        <span class="dui-avatar"><?php echo dui_e($teacher_letter); ?></span>
                                        <?php echo dui_e($user_name); ?>
                                    </span>
                                </div>
                                <div class="dui-course-foot">
                                    <a class="dui-btn dui-btn-primary dui-btn-sm" href="lessons.php?course_id=<?php echo $course_id; ?>">
                                        <?php echo icon('play', 'ic ic-xs'); ?> إدارة الدروس
                                    </a>
                                </div>
                            </article>

                        <?php } ?>
                    </div>
                <?php } ?>
            </section>

            </div>

        </main>

    </div>

</div>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
