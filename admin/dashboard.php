<?php

session_start();

require_once "../includes/connection.php";
include '../includes/icons.php';

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}
/* التأكد أن المستخدم مسجل الدخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/* التأكد أنه Admin */

if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* عدد المستخدمين */

$sql_users = "SELECT COUNT(*) AS total FROM users";

$result_users = mysqli_query($conn, $sql_users);

$users_count = mysqli_fetch_assoc($result_users);


/* عدد الحسابات المفعلة */

$sql_active = "SELECT COUNT(*) AS total
               FROM users
               WHERE status = 'active'";

$result_active = mysqli_query($conn, $sql_active);

$active_count = mysqli_fetch_assoc($result_active);


/* عدد الحسابات غير المفعلة */

$sql_pending = "SELECT COUNT(*) AS total
                FROM users
                WHERE status = 'pending'";

$result_pending = mysqli_query($conn, $sql_pending);

$pending_count = mysqli_fetch_assoc($result_pending);


$sql_rejected = "SELECT COUNT(*) AS total
                FROM users
                WHERE status = 'rejected'";

$result_rejected = mysqli_query($conn, $sql_rejected);

$rejected_count = mysqli_fetch_assoc($result_rejected);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة — Aschool</title>

    <link rel="stylesheet" href="../css/style.css?v=29">
<link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">

</head>

<body class="theme-dark dash-page">

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
                <small>لوحة الإدارة</small>
            </span>
        </div>

        <div class="side-user">
            <span class="side-avatar"><?php echo function_exists('mb_substr') ? mb_substr($_SESSION["name"], 0, 1, 'UTF-8') : 'A'; ?></span>
            <span class="side-user-meta">
                <strong><?php echo $_SESSION["name"]; ?></strong>
                <small>مدير المنصة</small>
            </span>
        </div>

        <div class="sidebar-section-title">القائمة الرئيسية</div>

        <a href="dashboard.php" class="active">
            <span class="nav-ic"><?php echo icon('home', 'ic ic-sm'); ?></span>
            الرئيسية
        </a>

        <a href="users.php">
            <span class="nav-ic"><?php echo icon('users', 'ic ic-sm'); ?></span>
            المستخدمون
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
                <small>نظرة عامة</small>
                <h2>لوحة التحكم</h2>
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
                    <p>من هنا تدير منصتك التعليمية بكل سهولة. تابع المستخدمين، راجع الدورات، وابقَ على اطلاع بكل جديد.</p>
                </div>

                <div class="hero-actions">
                    <a href="add_user.php" class="btn btn-primary"><?php echo icon('user-plus', 'ic ic-sm'); ?> مستخدم جديد</a>
                    <a href="courses.php" class="btn btn-secondary"><?php echo icon('book', 'ic ic-sm'); ?> إدارة الدورات</a>
                    <button type="button" class="btn btn-ghost" data-reminder-open><?php echo icon('bell', 'ic ic-sm'); ?> تذكير</button>
                </div>

            </section>

            <div class="kpi-grid">

                <a class="kpi" href="users.php">
                    <span class="kpi-icon tint-blue"><?php echo icon('users', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">إجمالي المستخدمين</span>
                        <strong class="kpi-value"><?php echo $users_count["total"]; ?></strong>
                        <span class="kpi-meta">كل الحسابات المسجلة</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="users.php">
                    <span class="kpi-icon tint-green"><?php echo icon('check-circle', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">الحسابات المفعلة</span>
                        <strong class="kpi-value"><?php echo $active_count["total"]; ?></strong>
                        <span class="kpi-meta">جاهزة للاستخدام</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="admin_activation.php">
                    <span class="kpi-icon tint-amber"><?php echo icon('clock', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">بانتظار التفعيل</span>
                        <strong class="kpi-value"><?php echo $pending_count["total"]; ?></strong>
                        <span class="kpi-meta">تحتاج مراجعة</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

                <a class="kpi" href="reject_user.php">
                    <span class="kpi-icon tint-red"><?php echo icon('alert', 'ic ic-lg'); ?></span>
                    <span class="kpi-body">
                        <span class="kpi-label">حسابات مرفوضة</span>
                        <strong class="kpi-value"><?php echo $rejected_count["total"]; ?></strong>
                        <span class="kpi-meta">غير مقبولة</span>
                    </span>
                    <span class="kpi-arrow">←</span>
                </a>

            </div>

            <div class="dash-grid">

                <section class="panel">

                    <div class="panel-head">
                        <h2><?php echo icon('zap', 'ic ic-sm'); ?> إجراءات سريعة</h2>
                    </div>

                    <div class="quick-grid">

                        <a class="quick-link" href="add_user.php">
                            <span class="ql-ic"><?php echo icon('user-plus', 'ic ic-sm'); ?></span>
                            إضافة مستخدم
                        </a>

                        <a class="quick-link" href="categories.php">
                            <span class="ql-ic"><?php echo icon('folder', 'ic ic-sm'); ?></span>
                            التصنيفات
                        </a>

                        <a class="quick-link" href="courses.php">
                            <span class="ql-ic"><?php echo icon('book', 'ic ic-sm'); ?></span>
                            إدارة الدورات
                        </a>

                        <a class="quick-link" href="course_requests.php">
                            <span class="ql-ic"><?php echo icon('clock', 'ic ic-sm'); ?></span>
                            طلبات المراجعة
                        </a>

                        <a class="quick-link" href="admin_activation.php">
                            <span class="ql-ic"><?php echo icon('check-circle', 'ic ic-sm'); ?></span>
                            تفعيل الحسابات
                        </a>

                        <a class="quick-link" href="../courses.php">
                            <span class="ql-ic"><?php echo icon('globe', 'ic ic-sm'); ?></span>
                            عرض المنصة
                        </a>

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

            <div class="dash-grid">

                <section class="panel">

                    <div class="panel-head">
                        <h2><?php echo icon('chart', 'ic ic-sm'); ?> حالة المنصة</h2>
                        <a class="panel-action" href="users.php">عرض التفاصيل</a>
                    </div>

                    <div class="status-list">

                        <div class="status-row">
                            <div class="status-top">
                                <span>حسابات مفعلة</span>
                                <strong><?php echo $active_count["total"]; ?></strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill is-green" data-bar="<?php echo $active_count["total"]; ?>" data-max="<?php echo $users_count["total"]; ?>"></span>
                            </div>
                        </div>

                        <div class="status-row">
                            <div class="status-top">
                                <span>بانتظار التفعيل</span>
                                <strong><?php echo $pending_count["total"]; ?></strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill is-amber" data-bar="<?php echo $pending_count["total"]; ?>" data-max="<?php echo $users_count["total"]; ?>"></span>
                            </div>
                        </div>

                        <div class="status-row">
                            <div class="status-top">
                                <span>حسابات مرفوضة</span>
                                <strong><?php echo $rejected_count["total"]; ?></strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill is-red" data-bar="<?php echo $rejected_count["total"]; ?>" data-max="<?php echo $users_count["total"]; ?>"></span>
                            </div>
                        </div>

                    </div>

                </section>

                <section class="panel">

                    <div class="panel-head">
                        <h2><?php echo icon('sparkles', 'ic ic-sm'); ?> ملاحظة سريعة</h2>
                    </div>

                    <div class="dash-note">
                        <?php echo icon('info', 'ic'); ?>
                        <span>استخدم زر <strong>التذكير</strong> العائم لتسجيل المهام المهمة، وزر <strong>وضع التركيز</strong> لبدء جلسة عمل هادئة. كل ذلك يعمل فوراً وبدون إنترنت.</span>
                    </div>

                </section>

            </div>

        </main>

    </div>

</div>

<script src="../js/app.js?v=29" defer></script>

</body>

</html>
