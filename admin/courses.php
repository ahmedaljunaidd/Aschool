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


if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* جلب الدورات */

$sql = "SELECT * FROM courses ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة الدورات</title>

    <link rel="stylesheet" href="../css/style.css?v=29">
<link rel="stylesheet" href="../css/theme-dark.css?v=29">
    <link rel="stylesheet" href="../css/theme-light.css?v=29">

</head>

<body class="theme-dark shell-page">

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




<div class="content">

    <a href="dashboard.php" class="btn-back">
        العودة إلى لوحة التحكم
    </a>

    <div class="entry-head">

        <h1>إدارة الدورات</h1>

        <a class="button" href="add_course.php">
             إضافة دورة
        </a>

    </div>

    <p class="welcome">
        إضافة وتعديل وحذف الدورات التعليمية
    </p>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دورات حاليًا.
        </div>

    <?php

    }

    ?>


    <div class="entry-list">

    <?php

    while ($course = mysqli_fetch_assoc($result)) {

    ?>

        <div class="entry-item">

            <div class="entry-body">

                <span class="entry-title">
                    <?php echo $course["title"]; ?>
                </span>

                <span class="entry-desc">
                    <?php echo $course["description"]; ?>
                </span>

            </div>

            <div class="lesson-actions">

                <a class="btn-ghost"
                   href="edit_course.php?id=<?php echo $course["id"]; ?>">
                    <?php echo icon('edit') ?> تعديل
                </a>

                <a class="btn btn-danger btn-sm"
                   href="delete_course.php?id=<?php echo $course["id"]; ?>"
                   onclick="return confirm('هل أنت متأكد من حذف الدورة؟');">
                   <?php echo icon('trash') ?>  حذف
                </a>

                <a class="btn-ghost"
                   href="lessons.php?course_id=<?php echo $course["id"]; ?>">
                    الدروس
                </a>

            </div>

        </div>

    <?php

    }

    ?>

    </div>

</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>