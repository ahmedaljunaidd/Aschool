<?php

session_start();

require_once "../includes/connection.php";


if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;

}


if ($_SESSION["role"] != "admin") {

    header("Location: ../index.php");
    exit;

}


if (!isset($_GET["id"])) {

    header("Location: courses.php");
    exit;

}


$id = $_GET["id"];


/* جلب الدرس */

$sql = "SELECT * FROM lessons WHERE id = $id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "الدرس غير موجود";
    exit;

}


$lesson = mysqli_fetch_assoc($result);


$course_id = $lesson["course_id"];


/* عند الضغط على حفظ */

if (isset($_POST["save"])) {

    $title = $_POST["title"];

    $content = $_POST["content"];

    $lesson_order = $_POST["lesson_order"];


    $sql = "UPDATE lessons

            SET title = '$title',
                content = '$content',
                lesson_order = '$lesson_order'

            WHERE id = $id";


    mysqli_query($conn, $sql);


    header("Location: lessons.php?course_id=$course_id");

    exit;

}

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تعديل الدرس</title>

   <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-back">
        العودة إلى دروس الدورة
    </a>

    <h1>تعديل الدرس</h1>


    <div class="profile-form">


        <form method="POST">


            <div class="form-group">

                <label>
                    عنوان الدرس
                </label>

                <input
                    class="form-control"
                    type="text"
                    name="title"
                    value="<?php echo $lesson["title"]; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    محتوى الدرس
                </label>

                <textarea
                    class="form-control"
                    name="content"
                    rows="10"
                    required
                ><?php echo $lesson["content"]; ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    ترتيب الدرس
                </label>

                <input
                    class="form-control"
                    type="number"
                    name="lesson_order"
                    value="<?php echo $lesson["lesson_order"]; ?>"
                    min="1"
                    required
                >

            </div>


            <div class="profile-actions">

            <button
                type="submit"
                name="save"
                class="button save-button">

                حفظ

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