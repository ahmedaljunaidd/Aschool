<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


/* التأكد من تسجيل الدخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


/* التأكد أن المستخدم Admin */

if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


/* التأكد من وجود رقم الدورة */

if (!isset($_GET["course_id"])) {

    header("Location: courses.php");
    exit;

}


$course_id = $_GET["course_id"];


/* جلب الدورة */

$sql = "SELECT * FROM courses WHERE id = $course_id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "الدورة غير موجودة";
    exit;

}


$course = mysqli_fetch_assoc($result);


/* عند الضغط على حفظ */

if (isset($_POST["save"])) {

    $title = $_POST["title"];

    $content = $_POST["content"];

    $lesson_order = $_POST["lesson_order"];


    $sql = "INSERT INTO lessons
            (course_id, title, content, lesson_order)

            VALUES
            ('$course_id', '$title', '$content', '$lesson_order')";


    mysqli_query($conn, $sql);


    header("Location: lessons.php?course_id=$course_id");

    exit;

}

// الحساب التلقائي لترتيب الدرس الجديد
$next_order = 1;

$sql_order = "SELECT MAX(lesson_order) AS mx
              FROM lessons
              WHERE course_id = $course_id";

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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إضافة درس</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-back">
        العودة إلى دروس الدورة
    </a>

    <h1>إضافة درس جديد</h1>


    <p class="welcome">

        الدورة:

        <?php echo $course["title"]; ?>

    </p>


    <div class="profile-form">


        <form method="POST">


            <div class="form-group">

                <label>
                    عنوان الدرس
                </label>

                <input
                    class="form-control" type="text"  name="title"  placeholder="مثال: مقدمة عن PHP"   required autofocus >

            </div>


            <div class="form-group">

                <label>
                    محتوى الدرس
                </label>

                <textarea
                    class="form-control"  name="content"  rows="10"   placeholder="اكتب محتوى الدرس هنا..."  required
                ></textarea>

            </div>


            <div class="form-group">

                <label>
                    ترتيب الدرس
                </label>

                <input
                    class="form-control"  type="number"   name="lesson_order"  value="<?php echo $next_order; ?>"   min="1"   readonly
                >

                <span class="lesson-meta" style="margin-top:6px;">
                    💡 الترتيب يُحسب تلقائيًا بعد آخر درس.
                </span>

            </div>


            <div class="profile-actions">

            <button  type="submit"  name="save"  class="button save-button">

                حفظ الدرس

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