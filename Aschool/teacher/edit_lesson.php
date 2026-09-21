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


$lesson_id = $_GET["id"];


// جلب الدرس
$sql = "SELECT lessons.*, courses.teacher_id
        FROM lessons, courses
        WHERE lessons.id = '$lesson_id'
        AND lessons.course_id = courses.id
        AND courses.teacher_id = '$teacher_id'";


$result = mysqli_query($conn, $sql);


if (!$result) {
    die("حدث خطأ: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) == 0) {
    die("الدرس غير موجود");
}


$lesson = mysqli_fetch_assoc($result);


// عند الضغط على حفظ
if (isset($_POST["update"])) {

    $title = $_POST["title"];
    $content = $_POST["content"];
    $video = $_POST["video"];
    $lesson_order = $_POST["lesson_order"];


    $sql = "UPDATE lessons SET

            title = '$title',

            content = '$content',

            video = '$video',

            lesson_order = '$lesson_order'

            WHERE id = '$lesson_id'";


    if (mysqli_query($conn, $sql)) {

        // الدورة تحتاج مراجعة من جديد
        $course_id = $lesson["course_id"];

        $sql2 = "UPDATE courses
                 SET status = 'pending'
                 WHERE id = '$course_id'";

        mysqli_query($conn, $sql2);


        header("Location: lessons.php?course_id=$course_id");
        exit;

    } else {

        echo "حدث خطأ: " . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>تعديل الدرس</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>


<body>


    
    <div class="content">

    <a href="lessons.php?course_id=<?php echo $lesson["course_id"]; ?>" class="btn-back">
        العودة إلى دروس الدورة
    </a>

    <div class="profile-form">

        <h1>تعديل الدرس</h1>

        <form method="POST">

            <div class="form-group">

                <label>عنوان الدرس</label>

                <input
                    class="form-control"
                    type="text"
                    name="title"
                    value="<?php echo $lesson["title"]; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>محتوى الدرس</label>

                <textarea
                    class="form-control"
                    name="content"
                    rows="10"
                ><?php echo $lesson["content"]; ?></textarea>

            </div>


            <div class="form-group">

                <label>رابط الفيديو</label>

                <input
                    class="form-control"
                    type="text"
                    name="video"
                    value="<?php echo $lesson["video"]; ?>"
                >

            </div>


            <div class="form-group">

                <label>ترتيب الدرس</label>

                <input
                    class="form-control"
                    type="number"
                    name="lesson_order"
                    value="<?php echo $lesson["lesson_order"]; ?>"
                >

            </div>


            <!-- <div class="profile-actions"> -->

                <button
                    type="submit"
                    name="update"
                    class="button save-button"
                >
                    حفظ التعديل
                </button>

                <a href="lessons.php?course_id=<?php echo $lesson["course_id"]; ?>" class="button cancel-button">
                    إلغاء
                </a>

            <!-- </div> -->

        </form>

    </div>

</div>


</body>

</html>