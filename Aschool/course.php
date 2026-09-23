<?php

session_start();

include "includes/connection.php";


if (!isset($_GET["id"])) {

    die("الكورس غير موجود");

}


$course_id = $_GET["id"];


/* جلب الكورس */

$sql = "SELECT * FROM courses
        WHERE id = $course_id
        AND status = 'approved'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    die("الكورس غير موجود");

}


$course = mysqli_fetch_assoc($result);


/* جلب الدروس */

$sql_lessons = "SELECT * FROM lessons
                WHERE course_id = $course_id
                ORDER BY lesson_order ASC";

$result_lessons = mysqli_query($conn, $sql_lessons);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title><?php echo $course["title"]; ?></title>

    <link rel="stylesheet" href="css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="index.php">الرئيسية</a>

        <a href="courses.php">الكورسات</a>

    </div>

    <?php

    if (isset($_SESSION["user_id"])) {

    ?>

        <a class="btn-login btn-login-filled" href="user/dashboard.php">
            حسابي
        </a>

    <?php

    } else {

    ?>

        <div class="nav-cta">

            <a href="register.php" class="btn-login btn-login-filled">إنشاء حساب</a>

            <a href="login.php" class="btn-login">تسجيل الدخول</a>

        </div>

    <?php

    }

    ?>

</div>


<div class="content">

    <a href="courses.php" class="btn-back">
        العودة إلى الكورسات
    </a>

    <div class="card">

        <h1>
            <?php echo $course["title"]; ?>
        </h1>

        <p>
            <?php echo $course["description"]; ?>
        </p>


        <?php

        if (isset($_SESSION["user_id"]) &&
            $_SESSION["role"] == "user") {

        ?>

            <a class="btn-guide"
               href="enroll.php?id=<?php echo $course_id; ?>">

                التسجيل في الكورس

            </a>

        <?php

        } else if (!isset($_SESSION["user_id"])) {

        ?>

            <p>
                يجب تسجيل الدخول للتسجيل في الكورس.
            </p>

            <a class="btn-login" href="login.php">
                تسجيل الدخول
            </a>

        <?php

        }

        ?>

    </div>


    <h2>دروس الكورس</h2>


    <?php

    if (mysqli_num_rows($result_lessons) > 0) {

        while ($lesson = mysqli_fetch_assoc($result_lessons)) {

    ?>

            <div class="card">

                <h3>

                    <?php echo $lesson["lesson_order"]; ?>

                    -

                    <?php echo $lesson["title"]; ?>

                </h3>

            </div>

    <?php

        }

    } else {

    ?>

        <p>
            لا توجد دروس حاليًا.
        </p>

    <?php

    }

    ?>

</div>


<footer class="site-footer">

    <div class="footer-brand">Aschool</div>

    <p>
        منصة تعليمية تهدف إلى نشر المعرفة وتسهيل الوصول إلى الدورات التعليمية.
    </p>

</footer>

</body>

</html>