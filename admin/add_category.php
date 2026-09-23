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


if (isset($_POST["save"])) {

    $name = $_POST["name"];

    $description = $_POST["description"];


    $sql = "INSERT INTO categories (name, description)
            VALUES ('$name', '$description')";


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

    <title>إضافة تصنيف</title>

   <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="content">

    <a href="categories.php" class="btn-back">
        العودة إلى التصنيفات
    </a>

    <h1>إضافة تصنيف جديد</h1>


    <div class="profile-form">

        <form method="POST">


            <div class="form-group">

                <label>
                    اسم التصنيف
                </label>

                <input
                    type="text" name="name"  required autofocus  >

            </div>


            <div class="form-group">

                <label>
                    وصف التصنيف
                </label>

                <textarea
                    name="description"
                    rows="5"
                ></textarea>

            </div>


            <button type="submit"  name="save"  class="btn btn-success btn-sm">
                حفظ 
            </button>


            <a
                href="categories.php" class="btn btn-danger btn-sm">

                إلغاء

            </a>


        </form>

    </div>

</div>


</body>

</html>