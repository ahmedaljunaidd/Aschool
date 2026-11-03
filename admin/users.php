<?php

session_start();

require_once "../includes/connection.php";
include '../includes/icons.php';

if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة المستخدمين</title>

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

    <div class="page-head">

        <h1><?php echo icon('users','ic'); ?> إدارة المستخدمين</h1>

        <a class="btn btn-primary btn-sm" href="add_user.php">
            <?php echo icon('plus','ic ic-sm'); ?> إضافة مستخدم جديد
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
                            <?php echo icon('eye','ic ic-sm'); ?> عرض
                        </a>

                        <a class="btn btn-success btn-sm" href="edit_user.php?id=<?php echo $row['id']; ?>">
                            <?php echo icon('edit','ic ic-sm'); ?> تعديل
                        </a>

                        <a class="btn btn-danger btn-sm" href="delete_user.php?id=<?php echo $row['id']; ?>"
                           onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                            <?php echo icon('trash','ic ic-sm'); ?> حذف
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


<script src="../js/app.js?v=29" defer></script>
</body>

</html>
