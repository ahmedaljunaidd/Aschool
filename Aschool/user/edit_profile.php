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


$user_id = $_SESSION["user_id"];

$message = "";


/* عند الضغط على حفظ */

if (isset($_POST["save"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $gender = $_POST["gender"];
    $birth_date = $_POST["birth_date"];


    /* جلب الصورة القديمة */

    $sql_old = "SELECT image FROM users WHERE id = $user_id";

    $result_old = mysqli_query($conn, $sql_old);

    $old_user = mysqli_fetch_assoc($result_old);

    $image = $old_user["image"];


    /* إذا اختار المستخدم صورة جديدة */

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $image_name = time() . "_" . $_FILES["image"]["name"];

        $image_path = "../uploads/profile/" . $image_name;


        if (move_uploaded_file(
            $_FILES["image"]["tmp_name"],
            $image_path
        )) {

            $image = $image_name;

        }

    }


    /* تحديث جميع البيانات */

    $sql_update = "UPDATE users SET

                    name = '$name',
                    email = '$email',
                    gender = '$gender',
                    birth_date = '$birth_date',
                    image = '$image'

                   WHERE id = $user_id";


    if (mysqli_query($conn, $sql_update)) {

        $_SESSION["name"] = $name;

        $message = "تم تحديث البيانات بنجاح";

          header("Location: profile.php");
        exit;

    } else {

        $message = "حدث خطأ: " . mysqli_error($conn);

    }

}


/* جلب البيانات الجديدة بعد التحديث */

$sql = "SELECT * FROM users WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>

<html lang="ar">

<head>

    <meta charset="UTF-8">

    <title>تعديل البيانات</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>


<body>


<div class="navbar">

    <h2>المنصة التعليمية</h2>

    <div class="dash-nav">

        <a href="dashboard.php">الرئيسية</a>

        <a href="my_courses.php">دوراتي</a>

        <a href="favorites.php">المفضلة</a>

        <a href="profile.php">الملف الشخصي</a>

        <a href="edit_profile.php" class="active">تعديل البيانات</a>

        <a href="settings.php">الإعدادات</a>

    </div>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="content">

    <div class="profile-form">

        <h1>تعديل البيانات</h1>


        <?php if ($message != "") { ?>
        
        <p>
            <?php echo $message; ?>
        </p>
        
        <?php } ?>
        
        
        <form method="POST"
        enctype="multipart/form-data">

        <label class="avatar-upload">

            <input type="file"   name="image"  id="imageInput"  accept="image/*">

            <?php if ($user["image"] != "") { ?>

                <img id="avatarPreview"
                     src="../uploads/profile/<?php echo $user["image"]; ?>"
                     alt="">

            <?php } else { ?>

                <img id="avatarPreview"
                     style="display:none;">

            <?php } ?>

            <span class="avatar-plus">+</span>

        </label>

            <div class="form-group">
            <label>الاسم</label>

            <input class="form-control" type="text" name="name"  value="<?php echo $user["name"]; ?>"   required>
            </div>

            <div class="form-group">
            <label>البريد الإلكتروني</label>

            <input class="form-control" type="email"  name="email"   value="<?php echo $user["email"]; ?>"  required>
            </div>

            <div class="form-group">
            <label>الجنس</label>

            <select class="form-control" name="gender">

                <option value="man"
                    <?php if ($user["gender"] == "man") { echo "selected"; } ?>>
                    ذكر
                </option>

                <option value="women"
                    <?php if ($user["gender"] == "women") { echo "selected"; } ?>>
                    أنثى
                </option>

            </select>
            </div>

            <div class="form-group">
            <label>تاريخ الميلاد</label>

            <input class="form-control" type="date"  name="birth_date"  value="<?php echo $user["birth_date"]; ?>">
            </div>

            <button class="button save-button"  type="submit"  name="save">

                حفظ 

            </button>

            <a href="profile.php" class="button cancel-button">
                إلغاء
            </a>


        </form>

    </div>

</div>





</body>

</html>