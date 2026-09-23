<?php

session_start();

include "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



// التأكد من تسجيل الدخول
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


// التأكد أن المستخدم معلم
if ($_SESSION["role"] != "teacher") {
    die("ليس لديك صلاحية");
}


// الحصول على رقم الدورة
if (!isset($_GET["course_id"])) {
    die("الدورة غير موجودة");
}

$course_id = $_GET["course_id"];


// إضافة الدرس
if (isset($_POST["add"])) {

    $title = $_POST["title"];
    $content = $_POST["content"];
    $video = $_POST["video"];
    $lesson_order = $_POST["lesson_order"];


    $sql = "INSERT INTO lessons
            (course_id, title, content, video, lesson_order)
            VALUES
            ('$course_id', '$title', '$content', '$video', '$lesson_order')";


    if (mysqli_query($conn, $sql)) {

        // بعد إضافة درس تصبح الدورة بانتظار المراجعة
        $sql2 = "UPDATE courses
                 SET status = 'pending'
                 WHERE id = '$course_id'
                 AND teacher_id = '" . $_SESSION["user_id"] . "'";

        mysqli_query($conn, $sql2);

        header("Location: lessons.php?course_id=$course_id");
        exit;

    } else {

        echo "حدث خطأ: " . mysqli_error($conn);
}

}

// الحساب التلقائي لترتيب الدرس الجديد
$next_order = 1;

$sql_order = "SELECT MAX(lesson_order) AS mx
              FROM lessons
              WHERE course_id = '$course_id'";

$res_order = mysqli_query($conn, $sql_order);

if ($res_order) {

    $row_order = mysqli_fetch_assoc($res_order);

    if ($row_order["mx"] !== null) {

        $next_order = (int)$row_order["mx"] + 1;

    }

}

?>


<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>إضافة درس</title>

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

    <a href="dashboard.php">
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

<a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-back">
    العودة إلى دروس الدورة
</a>

<div class="profile-form">
<h1>إضافة درس جديد</h1>

<p class="welcome">
    يُضاف الدرس تلقائيًا بعد آخر درس في الدورة (ترتيب <strong><?php echo $next_order; ?></strong>).
</p>
    <form method="POST">
    

        <div class="form-group">

            <label>عنوان الدرس</label>

            <input
                class="form-control"
                type="text"
                name="title"
                placeholder="مثال: مقدمة عن PHP"
                required
            >

        </div>


        <div class="form-group">

            <label>محتوى الدرس</label>

            <textarea
                class="form-control"
                name="content"
                rows="10"
                placeholder="اكتب محتوى الدرس هنا..."
            ></textarea>

        </div>


        <div class="form-group">

            <label>ترتيب الدرس</label>

            <input
                class="form-control"
                type="number"
                name="lesson_order"
                value="<?php echo $next_order; ?>"
                readonly
            >

            <span class="lesson-meta" style="margin-top:6px;">
                💡 الترتيب يُحسب تلقائيًا بعد آخر درس — عدّله في صفحة التعديل إذا أردت.
            </span>

        </div>


        <div class="profile-actions">

        <button
            type="submit"
            name="add"
            class="button save-button"
        >
            إضافة الدرس
        </button>

        <a
                href="lessons.php?course_id=<?php echo $course_id; ?>"
               class="button cancel-button">

                إلغاء

            </a>

        </div>
    </form>


</div>

</div>


</body>

</html>