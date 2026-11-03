<?php

require_once "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


$id = $_GET['id'];

$sql = "UPDATE users SET status = 'active' WHERE id = $id";

$result = mysqli_query($conn, $sql);

if ($result) {

    header("Location: admin_activation.php");
    exit();

} else {

    echo "حدث خطأ";

}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل الحساب</title>
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

    <div class="alert alert-danger">
        حدث خطأ أثناء تنفيذ العملية
    </div>

    <a class="back-link" href="admin_activation.php">
        ← العودة لطلبات التفعيل
    </a>

</div>

<script src="../js/app.js?v=29" defer></script>
</body>
</html>