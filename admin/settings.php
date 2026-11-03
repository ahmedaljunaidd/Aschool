<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



// جلب الإعدادات
$sql = "SELECT * FROM settings WHERE id = 1";

$result = mysqli_query($conn, $sql);

$settings = mysqli_fetch_assoc($result);


// عندما يضغط الأدمن حفظ
if (isset($_POST['save'])) {

    $site_name = $_POST['site_name'];

    $site_description = $_POST['site_description'];

    $site_email = $_POST['site_email'];

    $site_phone = $_POST['site_phone'];


    // تحديث الإعدادات
    $sql = "UPDATE settings SET

            site_name = '$site_name',

            site_description = '$site_description',

            site_email = '$site_email',

            site_phone = '$site_phone'

            WHERE id = 1";


    if (mysqli_query($conn, $sql)) {

        $message = "<div class='alert alert-success'>تم حفظ الإعدادات بنجاح</div>";

        // إعادة جلب البيانات
        $sql = "SELECT * FROM settings WHERE id = 1";

        $result = mysqli_query($conn, $sql);

        $settings = mysqli_fetch_assoc($result);

    } else {

        $message = "<div class='alert alert-danger'>حدث خطأ أثناء حفظ الإعدادات</div>";

    }

}

?>

<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إعدادات الموقع</title>

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

    <h1>إعدادات الموقع</h1>

    <p class="welcome">
        تعديل معلومات الموقع الأساسية
    </p>


    <?php

    if (isset($message)) {
        echo $message;
    }

    ?>


    <div class="profile-form">

        <form method="POST">

            <div class="form-Group">
                <label>اسم الموقع</label>
                <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" required>
            </div>

            <div class="form-Group">
                <label>وصف الموقع</label>
                <textarea name="site_description" rows="5"><?php echo $settings['site_description']; ?></textarea>
            </div>

            <div class="form-Group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="site_email" value="<?php echo $settings['site_email']; ?>">
            </div>

            <div class="form-Group">
                <label>رقم الهاتف</label>
                <input type="text" name="site_phone" value="<?php echo $settings['site_phone']; ?>">
            </div>

            <button type="submit" name="save" class="button save-button">
                حفظ الإعدادات
            </button>

        </form>

    </div>


    <a class="back-link" href="dashboard.php">
        ← العودة إلى لوحة التحكم
    </a>

</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>