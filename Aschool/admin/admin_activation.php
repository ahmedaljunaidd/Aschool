<?php

require_once "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


$sql = "SELECT * FROM users WHERE status = 'pending'";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>طلبات تفعيل الحسابات</title>

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

        <a href="users.php">
            👥 المستخدمون
        </a>

        <div class="sidebar-section-title">المحتوى</div>

        <a href="categories.php">
            📂 التصنيفات
        </a>

        <a href="courses.php">
            📚 الدورات
        </a>

        <a href="course_requests.php" class="active">
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

    <div class="page-head">

        <h1>طلبات تفعيل الحسابات</h1>

    </div>

    <p class="welcome">
        الحسابات بانتظار الموافقة من المسؤول
    </p>


    <div class="table-wrap">

        <table class="table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php

            while ($user = mysqli_fetch_assoc($result)) {

            ?>

            <tr>

                <td>
                    <?php echo $user['id']; ?>
                </td>

                <td>
                    <?php echo $user['name']; ?>
                </td>

                <td>
                    <?php echo $user['email']; ?>
                </td>

                <td>
                    <span class="badge badge-pending">
                        <?php echo $user['status']; ?>
                    </span>
                </td>

                <td>

                    <a class="btn btn-success btn-sm" href="activate.php?id=<?php echo $user['id']; ?>">
                        تفعيل
                    </a>

                  <a class="btn btn-danger btn-sm" href="reject_user.php?id=<?php echo $user["id"]; ?>">
                      رفض
                    </a>

                </td>

            </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>


    <a class="back-link" href="users.php">
        ← العودة
    </a>

</div>


</body>

</html>