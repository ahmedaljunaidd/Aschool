<?php

require_once "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


$sql = "SELECT * FROM users WHERE status = 'pending'";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>طلبات تفعيل الحسابات</title>

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

                  <a class="btn btn-view btn-sm" href="view_user.php?id=<?php echo $user['id']; ?>">
                            عرض
                        </a>

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


    <a class="back-link" href="dashboard.php">
        ← العودة
    </a>

</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>