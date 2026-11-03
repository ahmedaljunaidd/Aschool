<?php

include "includes/connection.php";
include "includes/icons.php";

$search = "";


if (isset($_GET["search"])) {

    $search = $_GET["search"];

}


/* البحث عن الكورسات */

$sql = "SELECT * FROM courses
        WHERE status = 'active'
        AND title LIKE '%$search%'
        ORDER BY id DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>الكورسات</title>

    <link rel="stylesheet" href="css/style.css?v=29">
<link rel="stylesheet" href="css/theme-dark.css?v=29">
    <link rel="stylesheet" href="css/theme-light.css?v=29">

</head>

<body class="theme-dark public-page">

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


<div class="navbar">

    <h2><?php echo icon('graduation','ic ic-sm'); ?> المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="index.php"><?php echo icon('home','ic ic-sm'); ?> الرئيسية</a>

        <a href="courses.php" class="active"><?php echo icon('book-open','ic ic-sm'); ?> الكورسات</a>

    </div>

    <div class="nav-cta">

        <a href="register.php" class="btn-login btn-login-filled"><?php echo icon('user-plus','ic ic-sm'); ?> إنشاء حساب</a>

        <a href="login.php" class="btn-login"><?php echo icon('log-in','ic ic-sm'); ?> تسجيل الدخول</a>

    </div>

</div>


<div class="content">

    <a href="index.php" class="btn-back"><?php echo icon('arrow-right','ic ic-sm'); ?> العودة إلى الرئيسية</a>

    <h1><?php echo icon('book','ic'); ?> الكورسات</h1>


    <!-- البحث -->

    <form method="GET" class="search-form">

        <input type="text"
               name="search"
               placeholder="ابحث عن كورس"
               value="<?php echo $search; ?>">

        <button type="submit" class="btn btn-primary">
            <?php echo icon('search','ic ic-sm'); ?> بحث
        </button>

    </form>


    <br>


    <div class="cards">

        <?php

        if (mysqli_num_rows($result) > 0) {

            while ($course = mysqli_fetch_assoc($result)) {

        ?>

                <div class="course-card">

                    <div class="course-card-cover"></div>

                    <div class="course-card-body">

                        <h2><?php echo $course["title"]; ?></h2>

                        <p><?php echo $course["description"]; ?></p>

                    </div>

                    <div class="course-card-footer">

                        <a class="btn-guide btn-sm"
                           href="course.php?id=<?php echo $course["id"]; ?>">
                            <?php echo icon('play','ic ic-sm'); ?> مشاهدة الكورس
                        </a>

                    </div>

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

<script src="js/app.js?v=29" defer></script>
</body>

</html>
