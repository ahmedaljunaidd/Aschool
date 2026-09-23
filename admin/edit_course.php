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


/* جلب الدورة */

$sql = "SELECT * FROM courses WHERE id = $id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "الدورة غير موجودة";
    exit;

}


$course = mysqli_fetch_assoc($result);


/* جلب التصنيفات */

$sql_categories = "SELECT * FROM categories ORDER BY name";

$categories = mysqli_query($conn, $sql_categories);


/* جلب المدرسين */

$sql_teachers = "SELECT * FROM users WHERE role = 'teacher' ORDER BY name";

$teachers = mysqli_query($conn, $sql_teachers);


/* حفظ التعديل */

if (isset($_POST["save"])) {

    $title = $_POST["title"];

    $description = $_POST["description"];

    $category_id = $_POST["category_id"];

    $teacher_id = $_POST["teacher_id"];


    $sql = "UPDATE courses

            SET title = '$title',
                description = '$description',
                category_id = '$category_id',
                teacher_id = '$teacher_id'

            WHERE id = $id";


    mysqli_query($conn, $sql);


    header("Location: courses.php");

    exit;

}

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تعديل الدورة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="courses.php" class="btn-back">
        العودة إلى الدورات
    </a>

    <h1>تعديل الدورة</h1>


    <div class="profile-form">

        <form method="POST">


            <div class="form-group">

                <label>اسم الدورة</label>

                <input
                    type="text"
                    name="title"
                    value="<?php echo $course["title"]; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>وصف الدورة</label>

                <textarea
                    name="description"
                    rows="6"
                    required
                ><?php echo $course["description"]; ?></textarea>

            </div>


            <div class="form-group">

                <label>التصنيف</label>

                <select name="category_id" required>

                    <?php

                    while ($category = mysqli_fetch_assoc($categories)) {

                    ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                            <?php
                            if ($category["id"] == $course["category_id"]) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php echo $category["name"]; ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>


            <div class="form-group">

                <label>المدرس</label>

                <select name="teacher_id">

                    <?php

                    while ($teacher = mysqli_fetch_assoc($teachers)) {

                    ?>

                        <option
                            value="<?php echo $teacher["id"]; ?>"
                            <?php
                            if ($teacher["id"] == $course["teacher_id"]) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php echo $teacher["name"]; ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>


            <button
                type="submit"
                name="save"
                class="button save-button"
            >

                حفظ 

            </button>


            <a
                href="courses.php"
                class="button cancel-button"
            >

                إلغاء

            </a>


        </form>

    </div>

</div>


</body>

</html>