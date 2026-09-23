<?php 
    require_once "includes/connection.php";
    //  $conn = mysqli_connect("localhost", "root", "", "project");
      $message="";
      if (isset($_POST["submit"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $gender =$_POST["gender"];
    $birth_date =$_POST["birth_date"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];


    if($password != $confirm_password){
        $message = "<div class='alert alert-danger'> كلمة المرور غير مطابقه</div>";
    }else{
        $sql = "select * from users where email='$email'";
        $result = mysqli_query($conn ,$sql);

    if(mysqli_num_rows($result) > 0){
         $message = "<div class='alert alert-danger'> هذا البريد مسجل بالفعل</div>";
    }else{
         $password = password_hash($password,PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name,email,password,birth_date,role,status,gender)
        VALUES ('$name','$email','$password','$birth_date','user','pending','$gender')";

        mysqli_query($conn,$sql);
        
    }
     header("Location: /Aschool/login.php");
            exit;
    
    }
}

  
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css?v=8">
    <title>انشاء حساب</title>
</head>
<body class="auth-page">
        

    <div class="form">

        <div class="auth-header">

                <div class="auth-icon">
                    👤
                </div>

                <h1>
                    إنشاء حساب جديد
                </h1>

                <p>
                    قم بإدخال بياناتك لإنشاء حساب
                </p>

            </div>

            <a href="index.php" class="btn-back">
                العودة إلى الرئيسية
            </a>

    <form  method="POST" action="">
        <?php echo $message; ?>
        <div class="form-Group">
            <label for="name"></label>
            <input type="text" id="name" class="form-control" name="name" placeholder=" الاسم" autofocus required >

        </div>
        <div class="form-Group">
            <label for="email"> </label>
            <input type="email" id="email" class="form-control" name="email" placeholder=" البريد الإلكتروني" required>

        </div>
        <div class="form-Group">
    <input
        type="date" id="birth_date" name="birth_date" class="form-control" required
    >

</div>
   <select name="gender" class="form-control">

            <option value="man">ذكر</option>

            <option value="women">انثى</option>

        </select>
        <div class="form-Group">
            <label for="password"> </label>
            <input type="password" id="password" class="form-control" name="password" placeholder="انشاء كلمة المرور" required>

        </div>
        <div class="form-Group">
            <label for="confirm_password"> </label>
            <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="تأكيد كلمة المرور" required>

        </div>
         <button type="submit" class="submit-login" name="submit">انشاء حساب</button>
        
        </form>
         <div class="auth-divider">

                <span>أو</span>

            </div>
            <p class="register-link">
                
                لديك حساب بالفعل؟
                <a href="login.php">
                    تسجيل الدخول
                </a>
                
            </p>


    </div>

    
</body>
</html>