<?php

session_start();

include "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


if ($_SESSION["role"] != "teacher") {
    die("ليس لديك صلاحية");
}


$teacher_id = $_SESSION["user_id"];


if (!isset($_GET["course_id"])) {
    die("الدورة غير موجودة");
}


$course_id = $_GET["course_id"];


// التأكد أن الدورة للمعلم الحالي
$sql = "SELECT * FROM courses
        WHERE id = '$course_id'
        AND teacher_id = '$teacher_id'";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("هذه الدورة ليست لك");
}


$course = mysqli_fetch_assoc($result);


// جلب الدروس
$sql = "SELECT * FROM lessons
        WHERE course_id = '$course_id'
        ORDER BY lesson_order";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}

?>


<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>دروس الدورة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>


<body>
    <div class="navbar">

    <h2>لوحة المدرس</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="sidebar">

    <h2>لوحة المدرس</h2>

    <div class="sidebar-section-title">القائمة الرئيسية</div>

    <a href="dashboard.php" class="active">
        🏠 الرئيسية
    </a>

    <a href="courses.php">
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

    <a href="courses.php" class="btn-back">
        العودة إلى دوراتي
    </a>

    <h1>
        دروس دورة:
        <?php echo $course["title"]; ?>
    </h1>


    <br>


    <a
        href="add_lesson.php?course_id=<?php echo $course_id; ?>"
        class="button"
    >
        إضافة درس جديد
    </a>


    <br><br>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دروس في هذه الدورة بعد — اضغط "إضافة درس جديد".
        </div>

    <?php

    }

    ?>


    <div class="lesson-list">

    <?php

    while ($lesson = mysqli_fetch_assoc($result)) {

        ?>

        <div class="lesson-item">

            <span class="lesson-num">
                <?php echo $lesson["lesson_order"]; ?>
            </span>

            <div class="lesson-item-body">

                <span class="lesson-title">
                    <?php echo $lesson["title"]; ?>
                </span>

                <span class="lesson-meta">
                    <?php if ($lesson["video"] != "") { ?>
                        🎬 فيديو
                    <?php } ?>
                    <?php if ($lesson["content"] != "") { ?>
                        📝 محتوى مكتوب
                    <?php } ?>
                </span>

            </div>

            <div class="lesson-actions">

                <a href="edit_lesson.php?id=<?php echo $lesson["id"]; ?>"
                   class="btn-ghost">
                    تعديل
                </a>

                <a href="delete_lesson.php?id=<?php echo $lesson["id"]; ?>"
                   class="btn-danger"
                   onclick="return confirm('هل تريد حذف هذا الدرس؟');">
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