<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


// نأخذ ID المستخدم من الرابط
$id = $_GET['id'];


// جلب بيانات المستخدم
$sql = "SELECT * FROM users WHERE id = $id";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);


if (!$user) {

    echo "المستخدم غير موجود";
    exit();

}


// عندما يضغط الأدمن حفظ
if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET
            name = '$name',
            email = '$email',
            birth_date = '$birth_date',
            gender = '$gender',
            role = '$role'
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {

        header("Location: users.php");
        exit();

    } else {

        echo "حدث خطأ أثناء التعديل";

    }

}

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تعديل المستخدم</title>

   <link rel="stylesheet" href="../css/style.css?v=29">
<link rel="stylesheet" href="../css/theme-dark.css?v=29">
   <link rel="stylesheet" href="../css/theme-light.css?v=29">

</head>

<body class="theme-dark shell-page">

<script>
(function () {
    try {
        if (localStorage.getItem('aschool.theme') === 'light') {
            document.body.classList.remove('theme-dark');
            document.body.classList.add('theme-light');
        }
    } catch (e) { /* ignore */ }
})();
</script>


    <div class="content">
        
        <h1>تعديل المستخدم</h1>
        
        <p class="welcome">
            تعديل بيانات المستخدم
            <?php echo $user['name']; ?>
        </p>
        
        <a class="btn-back">← العودة للمستخدمين</a>

    <div  class="profile-form">

        <form method="POST">

            <div class="form-Group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
            </div>

            <div class="form-Group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>

            <div class="form-Group">
                <label>Birth Date</label>
                <input type="date" name="birth_date" value="<?php echo $user['birth_date']; ?>" required>
            </div>

            <div class="form-Group">
                <label>Gender</label>
                <select name="gender">
                    <option value="man"
                        <?php
                        if ($user['gender'] == 'man') {
                            echo "selected";
                        }
                        ?>
                    >
                        ذكر
                    </option>
                    <option value="women"
                        <?php
                        if ($user['gender'] == 'women') {
                            echo "selected";
                        }
                        ?>
                    >
                        انثى
                    </option>
                </select>
            </div>

            <div class="form-Group">
                <label>Role</label>
                <select name="role">
                    <option value="user"
                        <?php
                        if ($user['role'] == 'user') {
                            echo "selected";
                        }
                        ?>
                    >
                        User
                    </option>
                    <option value="admin"
                        <?php
                        if ($user['role'] == 'admin') {
                            echo "selected";
                        }
                        ?>
                    >
                        Admin
                    </option>
                    <option value="teacher"
                        <?php
                        if ($user['role'] == 'teacher') {
                            echo "selected";
                        }
                        ?>
                    >
                        Teacher
                    </option>
                </select>
            </div>

            <button type="submit" name="update" class="button save-button">
                حفظ 
            </button>

            <a href="users.php" class="button cancel-button">
                إلغاء
            </a>

        </form>

    </div>


    <br>


</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>