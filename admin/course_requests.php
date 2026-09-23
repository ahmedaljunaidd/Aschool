<?php

session_start();
require_once "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



// التأكد أن المستخدم مسجل دخول
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


// التأكد أن المستخدم Admin
if ($_SESSION["role"] != "admin") {
    die("ليس لديك صلاحية لدخول هذه الصفحة");
}


// جلب الدورات التي تنتظر المراجعة
$sql = "SELECT * FROM courses WHERE status = 'pending'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("خطأ في استعلام الدورات: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>

    <meta charset="UTF-8">

    <title>طلبات الدورات</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>

  <div class="navbar">
        
        <h2>لوحة الإدارة</h2>
        
        <a class="logout" href="../logout.php">
            تسجيل الخروج
        </a>
        
    </div>


<!-- القائمة الجانبية -->

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

        <a href="categories.php">
            📂 التصنيفات
        </a>

        <a href="courses.php">
            📚 الدورات
        </a>

        <a href="course_requests.php"  class="active">
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


<!-- المحتوى -->

<div class="content">

    <a href="dashboard.php" class="btn-back">
        العودة إلى لوحة التحكم
    </a>

    <h1>طلبات الدورات</h1>


    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="lesson-empty">
            لا توجد دورات تنتظر المراجعة.
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
                    <strong>الوصف:</strong>
                    <?php echo $course["description"]; ?>
                </span>

                <?php

                // جلب بيانات المعلم
                $teacher_id = $course["teacher_id"];


                if ($teacher_id != "") {

                    $sql_teacher =
                        "SELECT * FROM users WHERE id = $teacher_id";

                    $result_teacher =
                        mysqli_query($conn, $sql_teacher);


                    if (!$result_teacher) {

                        die(
                            "خطأ في استعلام المعلم: "
                            . mysqli_error($conn)
                        );

                    }


                    $teacher =
                        mysqli_fetch_assoc($result_teacher);


                    if ($teacher) {

                        ?>

                        <span class="entry-desc">
                            <strong>المعلم:</strong>
                            <?php echo $teacher["name"]; ?>
                        </span>

                        <?php

                    }

                }


                // جلب التصنيف
                $category_id = $course["category_id"];


                if ($category_id != "") {

                    $sql_category =
                        "SELECT * FROM categories WHERE id = $category_id";

                    $result_category =
                        mysqli_query($conn, $sql_category);


                    if (!$result_category) {

                        die(
                            "خطأ في استعلام التصنيف: "
                            . mysqli_error($conn)
                        );

                    }


                    $category =
                        mysqli_fetch_assoc($result_category);


                    if ($category) {

                        ?>

                        <span class="entry-desc">
                            <strong>التصنيف:</strong>
                            <?php echo $category["name"]; ?>
                        </span>

                        <?php

                    }

                }

                ?>

                <span class="entry-status is-pending">
                    ⏳ بانتظار المراجعة
                </span>

            </div>

            <div class="lesson-actions">

                <a
                    class="btn-ghost"
                    href="view_course.php?id=<?php echo $course["id"]; ?>"
                >
                     عرض الدورة
                </a>

                <a
                    class="btn-success button"
                    href="approve_course.php?id=<?php echo $course["id"]; ?>"
                >
                    قبول الدورة
                </a>

                <a
                    class="btn-danger"
                    href="reject_course.php?id=<?php echo $course["id"]; ?>"
                >
                    رفض الدورة
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