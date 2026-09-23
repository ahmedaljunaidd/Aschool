<?php

include "includes/connection.php";


$search = "";


if (isset($_GET["search"])) {

    $search = $_GET["search"];

}


/* البحث عن الكورسات */

$sql = "SELECT * FROM courses
        WHERE status = 'approved'
        AND title LIKE '%$search%'
        ORDER BY id DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>الكورسات</title>

    <link rel="stylesheet" href="css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="index.php">الرئيسية</a>

        <a href="course.php">الكورسات</a>

    </div>

    <div class="nav-cta">

        <a href="register.php" class="btn-login btn-login-filled">إنشاء حساب</a>

        <a href="login.php" class="btn-login">تسجيل الدخول</a>

    </div>

</div>


<div class="content">

    <a href="index.php" class="btn-back">العودة إلى الرئيسية</a>

    <h1>الكورسات</h1>


    <!-- البحث -->

    <form method="GET" class="search-form">

        <input type="text"
               name="search"
               placeholder="ابحث عن كورس"
               value="<?php echo $search; ?>">

        <button type="submit" class="btn btn-primary">
            بحث
        </button>

    </form>


    <br>


    <div class="cards">

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

                </div>

        <?php

            }

        } else {

        ?>

            <p>
                لا توجد نتائج.
            </p>

        <?php

        }

        ?>

    </div>

</div>

<footer class="site-footer">

    <div class="footer-brand">Aschool</div>

    <p>
        منصة تعليمية تهدف إلى نشر المعرفة وتسهيل الوصول إلى الدورات التعليمية.
    </p>

</footer>

</body>

</html>