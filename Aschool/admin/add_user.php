<?php

session_start();

require_once "../includes/connection.php";


if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    echo "ليس لديك صلاحية";
    exit();
}


if (isset($_POST['add'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // تشفير كلمة المرور
    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email,birth_date,gender, password, role)
            VALUES ('$name', '$email','$birth_date','$gender' '$password', '$role')";

    if (mysqli_query($conn, $sql)) {

        echo "تم إضافة المستخدم بنجاح";

        header("Location: users.php");
        exit();

    } else {

        echo "حدث خطأ أثناء إضافة المستخدم";

    }

}
?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إضافة مستخدم</title>

    <link rel="stylesheet" href="../css/style.css?v=8">

</head>

<body>


<div class="navbar">

    <h2>لوحة الإدارة</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>


<div class="sidebar">

        <h2>لوحة الإدارة</h2>

        <div class="sidebar-section-title">القائمة الرئيسية</div>

        <a href="dashboard.php" class="active">
            🏠 الرئيسية
        </a>

        <a href="users.php" class="active">
            👥 المستخدمون
        </a>

        <a href="#">
            🔐 الصلاحيات
        </a>

        <div class="sidebar-section-title">المحتوى</div>

        <a href="categories.php">
            📂 التصنيفات
        </a>

        <a href="courses.php">
            📚 الدورات
        </a>

        <a href="course_requests.php">
            ⏳ طلبات المراجعة
        </a>

        <div class="sidebar-section-title">النظام</div>

        <a href="settings.php">
            ⚙️ الإعدادات
        </a>

        <div class="sidebar-footer">
            منصة <strong>Aschool</strong> التعليمية
        </div>

    </div>


<div class="content">

    <a href="users.php" class="btn-back">
        العودة إلى المستخدمين
    </a>

    <h1>إضافة مستخدم جديد</h1>

    <p class="welcome">
        إدخال بيانات المستخدم الجديد
    </p>


    <div class="profile-form">

        <form action="" method="POST">

            <div class="form-Group">
                <label>Name</label>
                <input type="text" name="name" required autofocus>
            </div>

            <div class="form-Group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-Group">
                <label>Birth Date</label>
                <input type="date" name="birth_date" required>
            </div>

            <div class="form-Group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-Group">
                <label>Gender</label>
                <select name="gender">
                    <option value="man">ذكر</option>
                    <option value="women">انثى</option>
                </select>
            </div>

            <div class="form-Group">
                <label>Role</label>
                <select name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            
            <button type="submit" name="add" class="button save-button">
                إضافة 
            </button>
            
            <a href="users.php" class="button cancel-button">الغاء</a>
        </form>

    </div>

</div>


</body>

</html>