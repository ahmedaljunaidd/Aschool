<?php
    session_start();

require_once "includes/connection.php";
include 'includes/icons.php';

$message = "";

if (isset($_POST["submit"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];


    // البحث عن الحساب
    $sql = "SELECT * FROM users WHERE email = '$email'";

    $result = mysqli_query($conn, $sql);


    // هل الحساب موجود؟
    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);


        // التأكد من أن الحساب مفعل
        if ($user["status"] != "active") {

            $message = "<div class='alert alert-danger'>
                            حسابك غير مفعل من قبل المسؤول
                        </div>";

        } else {


            // التأكد من كلمة المرور
            if (password_verify($password, $user["password"])) {


                // إنشاء Session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];


                // إذا كان User
                 if ($user["role"] == "user") {

            header("Location: user/dashboard.php");
            exit;

        } else if ($user["role"] == "teacher") {

            header("Location: teacher/dashboard.php");
            exit;

        } else if ($user["role"] == "admin") {

            header("Location: admin/dashboard.php");
            exit;
        }

                // إذا كانت الصلاحية غير معروفة
                else {

                    $message = "<div class='alert alert-danger'>
                                    الصلاحية غير صحيحة
                                </div>";
                }


            } else {

                $message = "<div class='alert alert-danger'>
                                كلمة المرور غير صحيحة
                            </div>";
            }

        }


    } else {

        $message = "<div class='alert alert-danger'>
                        الحساب غير موجود
                    </div>";
    }

}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="تسجيل الدخول إلى منصة Aschool التعليمية للوصول إلى دوراتك ودروسك.">
    <link rel="stylesheet" href="css/style.css?v=29">
    <link rel="stylesheet" href="css/theme-dark.css?v=29">
    <link rel="stylesheet" href="css/theme-light.css?v=29">
    <title>تسجيل الدخول — Aschool</title>
</head>
<body class="auth-page theme-dark">

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
    
    <div class="form">
        <div class="auth-header">

                <div class="auth-icon">
                    <?php echo icon('lock','ic ic-xl'); ?>
                </div>
                <h1>
                    تسجيل الدخول
                </h1>
                <p>
                    أدخل بيانات حسابك للوصول إلى حسابك
                </p>
            </div>

            <a href="index.php" class="btn-back">
                العودة إلى الرئيسية
            </a>

        <form action="" method="POST">
    
              <?php echo $message; ?>
            <div class="form-Group">
                <label for="email"></label>
                <input type="text" id="email" name="email" class="form-control" placeholder="ادخل البريد الالكتروني" autofocus required>
            </div>
            <div class="form-Group">
                <input type="password" name="password" class="form-control" placeholder="ادخل كلمة المرور " required>
            </div>
            <button class="submit-login" name="submit" ><?php echo icon('log-in','ic'); ?> تسجيل دخول</button>
        </form>
         <div class="auth-divider">

                <span>أو</span>

            </div>
            <p class="register-link">
                
                    ليس لديك حساب؟
                     <a href="register.php">انشاء حساب جديد</a>
                
                
            </p>

    </div>
    
<script src="js/app.js?v=29" defer></script>
</body>
</html>
