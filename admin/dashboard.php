<?php

session_start();

require_once "../includes/connection.php";

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

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>لوحة الإدارة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


    
    <div class="navbar">
        
        <h2>لوحة الإدارة</h2>
        
        <a class="logout" href="../logout.php">
            تسجيل الخروج
        </a>
        
    </div>
    
    
    
    <div class="sidebar">

        <h2>لوحة الإدارة</h2>

        <div class="sidebar-section-title">القائمة الرئيسية</div>

        <a href="dashboard.php" class="active">
            🏠 الرئيسية
        </a>

        <a href="users.php">
            👥 المستخدمون
        </a>


        <div class="sidebar-section-title">المحتوى</div>

        <a href="categories.php" >
            📂 التصنيفات
        </a>

        <a href="courses.php">
            📚 الدورات
        </a>

        <a href="course_requests.php">
            ⏳ طلبات المراجعة
        </a>

        <div class="sidebar-section-title">النظام</div>

        <a href="settings.php">
            ⚙️ الإعدادات
        </a>

        <div class="sidebar-footer">
            منصة <strong>Aschool</strong> التعليمية
        </div>

    </div>


<div class="content">

    <a href="../index.php" class="btn-back">
        العودة إلى الرئيسية
    </a>

    <h1>
        لوحة التحكم
    </h1>

    <p class="welcome">

        مرحباً
        <?php echo $_SESSION["name"]; ?>
        👋
        من هنا تدير منصتك التعليمية بكل سهولة

    </p>


    <div class="cards">


        <div class="stat-card">

            <div class="stat-card-icon icon-blue">👥</div>

            <div class="stat-card-body">

                <span class="stat-card-title">إجمالي المستخدمين</span>

                <strong class="stat-card-value">
                    <?php echo $users_count["total"]; ?>
                </strong>

                <a class="stat-card-link" href="users.php">
                    عرض الكل ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-green">✅</div>

            <div class="stat-card-body">

                <span class="stat-card-title">الحسابات المفعلة</span>

                <strong class="stat-card-value">
                    <?php echo $active_count["total"]; ?>
                </strong>

                <a class="stat-card-link" href="users.php">
                    عرض الكل ←
                </a>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-card-icon icon-amber">⏳</div>

            <div class="stat-card-body">

                <span class="stat-card-title">بانتظار التفعيل</span>

                <strong class="stat-card-value">
                    <?php echo $pending_count["total"]; ?>
                </strong>

                <a class="stat-card-link" href="admin_activation.php">
                    عرض الكل ←
                </a>

            </div>

        </div>


        <!-- <div class="stat-card">

            <div class="stat-card-icon icon-red">🚫</div>

            <div class="stat-card-body">

                <span class="stat-card-title">حسابات مرفوضة</span>

                <strong class="stat-card-value">
                    <?php echo $rejected_count["total"]; ?>
                </strong>

                <a class="stat-card-link" href="reject_user.php">
                    عرض الكل ←
                </a>

            </div>

        </div> -->


    </div>

</div>


</body>

</html>