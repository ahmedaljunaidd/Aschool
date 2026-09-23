<?php

session_start();

require_once "../includes/connection.php";

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}



?>
<!DOCTYPE html>
<html lang="ar">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة المستخدمين</title>

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

        <a href="dashboard.php">
            🏠 الرئيسية
        </a>

        <a href="users.php" class="active">
            👥 المستخدمون
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

    <div class="page-head">

        <h1>إدارة المستخدمين</h1>

        <a class="btn btn-primary btn-sm" href="add_user.php">
            + إضافة مستخدم جديد
        </a>

    </div>

    <p class="welcome">
        قائمة الحسابات المفعلة
    </p>


    <div class="table-wrap">

        <table class="table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>التحكم</th> 
                    <!-- <th>Birth Date</th>
                    <th>Gender</th>
                     <th>Status</th> -->
                </tr>
            </thead>

            <tbody>

            <?php

            $sql = "SELECT * FROM users
                   WHERE status = 'active'";


            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {


            ?>

                <tr>
                    <td>
                        <?php echo $row['id']; ?>
                    </td>

                    <td>
                        <?php echo $row['name']; ?>
                    </td>

                    <td>
                        <?php echo $row['email']; ?>
                    </td>
<!-- 
                    <td>
                        <?php echo $row['birth_date']; ?>
                    </td>

                    <td>
                        <?php echo $row['gender']; ?>
                    </td>

                    <!-- 
                        <td>
                            <span class="badge badge-active">
                                <?php echo $row['status']; ?>
                            </span>
                        </td> -->
                        <td>
                            <span class="badge badge-active">
                                <?php echo $row['role']; ?>
                            </span>
                        </td> 

                    <td>

                        <a class="btn btn-view btn-sm" href="view_user.php?id=<?php echo $row['id']; ?>">
                            عرض
                        </a>

                        <a class="btn btn-success btn-sm" href="edit_user.php?id=<?php echo $row['id']; ?>">
                            تعديل
                        </a>

                        <a class="btn btn-danger btn-sm" href="delete_user.php?id=<?php echo $row['id']; ?>"
                           onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">

                            حذف

                        </a>

                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>
            

        </table>

        

    </div>


    <a class="back-link" href="dashboard.php">
        ← العودة إلى لوحة التحكم
    </a>

</div>


</body>

</html>