<?php

session_start();

include "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



/* التأكد من تسجيل الدخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "user") {

    die("ليس لديك صلاحية");

}


$user_id = $_SESSION["user_id"];


/* جلب المفضلة */

$sql = "SELECT courses.*
        FROM favorites
        INNER JOIN courses
        ON favorites.course_id = courses.id
        WHERE favorites.user_id = $user_id
        ORDER BY favorites.id DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>المفضلة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>



    <div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <!-- <a href="../courses.php">الكورسات</a> -->

        <a href="my_courses.php">دوراتي</a>

        <a href="favorites.php" class="active">المفضلة</a>

        <a href="profile.php">الملف الشخصي</a>

        <a href="edit_profile.php">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">تسجيل الخروج</a>

</div>


<div class="content">

    <a href="dashboard.php" class="btn-back">
        العودة للوحة التحكم
    </a>

    <h1>الكورسات المفضلة</h1>


    <?php

    if (mysqli_num_rows($result) > 0) {

        while ($course = mysqli_fetch_assoc($result)) {

    ?>

            <div class="card">

                <h2>
                    <?php echo $course["title"]; ?>
                </h2>

                <p>
                    <?php echo $course["description"]; ?>
                </p>

                <a class="btn-guide"
                   href="course.php?id=<?php echo $course["id"]; ?>">

                    مشاهدة الكورس

                </a>

                <a class="button"
                   href="remove_favorite.php?id=<?php echo $course["id"]; ?>">

                    إزالة من المفضلة

                </a>

            </div>

    <?php

        }

    } else {

    ?>

        <div class="card">

            <p>
                لا توجد كورسات في المفضلة.
            </p>

        </div>

    <?php

    }

    ?>

</div>


</body>

</html>