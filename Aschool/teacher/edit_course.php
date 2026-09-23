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

$id = $_GET["id"];


/* جلب الدورة الخاصة بالمدرس */

$sql = "SELECT * FROM courses

        WHERE id = $id

        AND teacher_id = $teacher_id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "الدورة غير موجودة";

    exit;

}


$course = mysqli_fetch_assoc($result);


/* التصنيفات */

$sql = "SELECT * FROM categories ORDER BY name";

$result_categories = mysqli_query($conn, $sql);


/* التعديل */

if (isset($_POST["save"])) {

    $title = $_POST["title"];

    $description = $_POST["description"];

    $category_id = $_POST["category_id"];


    $sql = "UPDATE courses

            SET title = '$title',
                description = '$description',
                category_id = '$category_id',
                status = 'pending'

            WHERE id = $id

            AND teacher_id = $teacher_id";


    mysqli_query($conn, $sql);


    header("Location: courses.php");

    exit;

}

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>تعديل الدورة</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="courses.php" class="btn-back">
        العودة إلى دوراتي
    </a>

    <div class="profile-form">

        <h2>تعديل الدورة</h2>

        <p>
            بعد تعديل الدورة سيتم إرسالها للمراجعة مرة أخرى.
        </p>

        <br>


        <form method="POST">


            <div class="form-group">

                <label>اسم الدورة</label>

                <input type="text"
                       name="title"
                       value="<?php echo $course["title"]; ?>"
                       required>

            </div>


            <div class="form-group">

                <label>الوصف</label>

                <textarea name="description"
                          rows="6"
                          required><?php echo $course["description"]; ?></textarea>

            </div>


            <div class="form-group">

                <label>التصنيف</label>

                <select name="category_id" required>

                    <?php

                    while ($category = mysqli_fetch_assoc($result_categories)) {

                    ?>

                        <option value="<?php echo $category["id"]; ?>"

                            <?php

                            if ($category["id"] == $course["category_id"]) {
                                echo "selected";
                            }

                            ?>>

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

                حفظ التعديل

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