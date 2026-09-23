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


if ($_SESSION["role"] != "teacher") {

    header("Location: ../index.php");
    exit;

}


$teacher_id = $_SESSION["user_id"];


/* جلب التصنيفات */

$sql = "SELECT * FROM categories ORDER BY name";

$result_categories = mysqli_query($conn, $sql);


if (isset($_POST["save"])) {


    $title = $_POST["title"];

    $description = $_POST["description"];

    $category_id = $_POST["category_id"];


    $sql = "INSERT INTO courses

            (title, description, category_id, teacher_id, status)

            VALUES

            ('$title',
             '$description',
             '$category_id',
             '$teacher_id',
             'draft')";


    if (mysqli_query($conn, $sql)) {

        header("Location: courses.php");
        exit;

    }

}

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>إنشاء دورة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="courses.php" class="btn-back">
        العودة إلى دوراتي
    </a>

    <div class="profile-form">

        <h2>إنشاء دورة جديدة</  >

        <br>


        <form method="POST">


            <div class="form-group">

                <label>اسم الدورة</label>

                <input type="text"
                       name="title"
                       required>

            </div>


            <div class="form-group">

                <label>وصف الدورة</label>

                <textarea name="description"
                          rows="6"
                          required></textarea>

            </div>


            <div class="form-group">

                <label>التصنيف</label>

                <select name="category_id" required>

                    <option value="">
                        اختر التصنيف
                    </option>


                    <?php

                    while ($category = mysqli_fetch_assoc($result_categories)) {

                    ?>

                        <option value="<?php echo $category["id"]; ?>">

                            <?php echo $category["name"]; ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>


            <button class="button save-button"
                    type="submit"
                    name="save">

                حفظ كمسودة

            </button>


            <a class="button cancel-button"
               href="courses.php">

                إلغاء

            </a>


        </form>

    </div>

</div>

</body>

</html>