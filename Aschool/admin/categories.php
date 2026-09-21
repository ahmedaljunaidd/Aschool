<?php

session_start();

require_once "../includes/connection.php";
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

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>التصنيفات</title>

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

        <a href="dashboard.php">
            🏠 الرئيسية
        </a>

        <a href="users.php">
            👥 المستخدمون
        </a>

        <div class="sidebar-section-title">المحتوى</div>

        <a href="categories.php" class="active">
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

    <a href="dashboard.php" class="btn-back">
        العودة إلى لوحة التحكم
    </a>

    <div class="entry-head">

        <h1>التصنيفات</h1>

        <a class="button" href="add_category.php">
            ➕ إضافة تصنيف
        </a>

    </div>

    <p class="welcome">
        إدارة تصنيفات الدورات التعليمية
    </p>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد تصنيفات حاليًا.
        </div>

    <?php

    }

    ?>


    <div class="entry-list">

    <?php

    while ($category = mysqli_fetch_assoc($result)) {

    ?>

        <div class="entry-item">

            <div class="entry-body">

                <span class="entry-title">
                    <?php echo $category["name"]; ?>
                </span>

                <span class="entry-desc">
                    <?php echo $category["description"]; ?>
                </span>

            </div>

            <div class="lesson-actions">

                <a class="btn-ghost"
                   href="edit_category.php?id=<?php echo $category["id"]; ?>">
                    تعديل
                </a>

                <a class="btn-danger"
                   href="delete_category.php?id=<?php echo $category["id"]; ?>"
                   onclick="return confirm('هل أنت متأكد من حذف التصنيف؟');">
                    حذف
                </a>

            </div>

        </div>

    <?php

    }

    ?>

    </div>

</div>


</body>

</html>