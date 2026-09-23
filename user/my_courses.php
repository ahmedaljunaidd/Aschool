<?php

session_start();

include "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}

$search = "";


if (isset($_GET["search"])) {

    $search = $_GET["search"];

}



if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "user") {

    die("ليس لديك صلاحية");

}


$user_id = $_SESSION["user_id"];


$sql = "SELECT courses.*
        FROM courses
        INNER JOIN enrollments
        ON courses.id = enrollments.course_id
        WHERE enrollments.user_id = $user_id
        ORDER BY enrollments.id DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>دوراتي</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


   
    <div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <!-- <a href="../courses.php">الكورسات</a> -->

        <a href="my_courses.php" class="active">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

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

    <h1>دوراتي</h1>

    <form method="GET" class="search-form">
    
    <input type="text"
         name="search"
         placeholder="ابحث عن كورس"
         value="<?php echo $search; ?>">
    
    <button type="submit" class="btn btn-primary">
      بحث
    </button>
    
    </form>

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

                    دخول إلى الكورس

                </a>


                <a class="button"
                   href="progress.php?id=<?php echo $course["id"]; ?>">

                    متابعة التقدم

                </a>

            </div>

    <?php

        }

    } else {

    ?>

        <div class="card">

            <p>
                لم تسجل في أي كورس بعد.
            </p>

            <a class="btn-guide" href="../courses.php">
                مشاهدة الكورسات
            </a>

        </div>

    <?php

    }

    ?>

</div>


</body>

</html>