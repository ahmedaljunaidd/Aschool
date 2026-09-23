<?php
    session_start();

require_once "includes/connection.php";


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

            header("Location: /Aschool/user/dashboard.php");
            exit;

        } else if ($user["role"] == "teacher") {

            header("Location: /Aschool/teacher/dashboard.php");
            exit;

        } else if ($user["role"] == "admin") {

            header("Location: /Aschool/admin/dashboard.php");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=8">
    <title>Login</title>
</head>
<body class="auth-page">
    
    <div class="form">
        <div class="auth-header">

                <div class="auth-icon">
                    🔐
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
            <button class="submit-login" name="submit" >تسجيل دخول</button>
        </form>
         <div class="auth-divider">

                <span>أو</span>

            </div>
            <p class="register-link">
                
                    ليس لديك حساب؟
                     <a href="register.php">انشاء حساب جديد</a>
                
                
            </p>

    </div>
    
</body>
</html>