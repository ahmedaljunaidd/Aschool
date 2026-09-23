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


if ($_SESSION["role"] != "teacher") {

    header("Location: ../index.php");
    exit;

}


$teacher_id = $_SESSION["user_id"];


$sql = "SELECT * FROM courses

        WHERE teacher_id = $teacher_id

        ORDER BY id DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>دوراتي</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>دوراتي</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="sidebar">

    <h2>لوحة المدرس</h2>

    <div class="sidebar-section-title">القائمة الرئيسية</div>

    <a href="dashboard.php" >
        🏠 الرئيسية
    </a>

    <a href="courses.php" class="active">
        📚 دوراتي
    </a>

    <div class="sidebar-section-title">المنصة</div>

    <a href="../courses.php">
        🌐 المنصة
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

        <h1>دوراتي</h1>

        <a class="button" href="add_course.php">
            ➕ إنشاء دورة جديدة
        </a>

    </div>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد لديك دورات.
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

                    <span class="entry-status
                         <?php
                         if ($course["status"] == "draft") {
                             echo "is-draft";
                         } elseif ($course["status"] == "pending") {
                             echo "is-pending";
                         } elseif ($course["status"] == "approved") {
                             echo "is-approved";
                         } elseif ($course["status"] == "rejected") {
                             echo "is-rejected";
                         }
                         ?>">

                        <?php

                        if ($course["status"] == "draft") {

                            echo "📝 مسودة";

                        } elseif ($course["status"] == "pending") {

                            echo "⏳ قيد المراجعة";

                        } elseif ($course["status"] == "approved") {

                            echo "✅ مقبولة";

                        } elseif ($course["status"] == "rejected") {

                            echo "❌ مرفوضة";

                        }

                        ?>

                    </span>

                </div>

                <div class="lesson-actions">

                    <a class="btn-ghost"
                       href="edit_course.php?id=<?php echo $course["id"]; ?>">
                        تعديل
                    </a>

                    <a class="btn-danger"
                       href="delete_course.php?id=<?php echo $course["id"]; ?>"
                       onclick="return confirm('هل أنت متأكد من حذف الدورة؟');">
                        حذف
                    </a>

                    <a class="btn-ghost"
                       href="lessons.php?course_id=<?php echo $course["id"]; ?>">
                        📚 الدروس
                    </a>

                    <?php

                    if ($course["status"] == "draft" ||
                        $course["status"] == "rejected") {

                    ?>

                        <a class="button"
                           href="submit_course.php?id=<?php echo $course["id"]; ?>">
                            📤 إرسال للمراجعة
                        </a>

                    <?php

                    }

                    ?>

                </div>

            </div>

        <?php

        }

        ?>

    </div>

</div>

</body>

</html>