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


/* جلب التصنيفات */

$sql_categories = "SELECT * FROM categories ORDER BY name";

$categories = mysqli_query($conn, $sql_categories);


/* جلب المدرسين */

$sql_teachers = "SELECT * FROM users WHERE role = 'teacher' ORDER BY name";

$teachers = mysqli_query($conn, $sql_teachers);


/* عند الضغط على حفظ */

if (isset($_POST["save"])) {

    $title = $_POST["title"];

    $description = $_POST["description"];

    $category_id = $_POST["category_id"];

    $teacher_id = $_POST["teacher_id"];


    $sql = "INSERT INTO courses
            (title, description, category_id, teacher_id)

            VALUES
            ('$title', '$description', '$category_id', '$teacher_id')";


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

    <title>إضافة دورة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="courses.php" class="btn-back">
        العودة إلى الدورات
    </a>

    <h1>إضافة دورة جديدة</h1>


    <div class="profile-form">

        <form method="POST">


            <div class="form-group">

                <label>اسم الدورة</label>

                <input type="text"   name="title"  required autofocus  >

            </div>


            <div class="form-group">

                <label>وصف الدورة</label>

                <textarea   name="description"   rows="6" required
                ></textarea>

            </div>


            <div class="form-group">

                <label>التصنيف</label>

                <select name="category_id" required>

                    <option value="">
                        اختر التصنيف
                    </option>


                    <?php

                    while ($category = mysqli_fetch_assoc($categories)) {

                    ?>

                        <option value="<?php echo $category["id"]; ?>">

                            <?php echo $category["name"]; ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>


            <div class="form-group">

                <label>المدرس</label>

                <select name="teacher_id" >

                    <option value="">
                        اختر المدرس
                    </option>


                    <?php

                    while ($teacher = mysqli_fetch_assoc($teachers)) {

                    ?>

                        <option value="<?php echo $teacher["id"]; ?>">

                            <?php echo $teacher["name"]; ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>


            <button
                type="submit"  name="save"  class="button save-button"
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