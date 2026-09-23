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

    header("Location: categories.php");
    exit;

}


$id = $_GET["id"];


$sql = "SELECT * FROM categories WHERE id = $id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "التصنيف غير موجود";
    exit;

}


$category = mysqli_fetch_assoc($result);


if (isset($_POST["save"])) {

    $name = $_POST["name"];

    $description = $_POST["description"];


    $sql = "UPDATE categories

            SET name = '$name',
                description = '$description'

            WHERE id = $id";


    mysqli_query($conn, $sql);


    header("Location: categories.php");
    exit;

}

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تعديل التصنيف</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="categories.php" class="btn-back">
        العودة إلى التصنيفات
    </a>

    <h1>تعديل التصنيف</h1>


    <div class="profile-form">

        <form method="POST">


            <div class="form-group">

                <label>
                    اسم التصنيف
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo $category["name"]; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    وصف التصنيف
                </label>

                <textarea
                    name="description"
                    rows="5"
                ><?php echo $category["description"]; ?></textarea>

            </div>


            <button
                type="submit"
                name="save"
                class="button save-button">

                حفظ 

            </button>


            <a
                href="categories.php"
                class="button cancel-button">

                إلغاء

            </a>


        </form>

    </div>

</div>


</body>

</html>