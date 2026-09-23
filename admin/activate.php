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
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل الحساب</title>
    <link rel="stylesheet" href="../css/style.css?v=8">
</head>
<body>

<div class="navbar">

    <h2>لوحة الإدارة</h2>

    <a class="logout" href="../logout.php">
        تسجيل الخروج
    </a>

</div>

<div class="content">

    <div class="alert alert-danger">
        حدث خطأ أثناء تنفيذ العملية
    </div>

    <a class="back-link" href="admin_activation.php">
        ← العودة لطلبات التفعيل
    </a>

</div>

</body>
</html>