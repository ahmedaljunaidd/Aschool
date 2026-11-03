<?php

require_once "../includes/connection.php";
if (!isset($conn)) {
    die("لم يتم الاتصال بقاعدة البيانات");
}


$sql = "SELECT * FROM courses WHERE status = 'active'";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>الدورات المقبوله  </title>

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

        <h1>  الدورات المقبوله</h1>

    </div>


  
    <div class="table-wrap">

        <table class="table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>description</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            <?php

            while ($courses = mysqli_fetch_assoc($result)) {

            ?>

            <tr>

                <td>
                    <?php echo $courses['id']; ?>
                </td>

                <td>
                    <?php echo $courses['title']; ?>
                </td>

                <td>
                    <?php echo $courses['description']; ?>
                </td>

                <td>
                    <span class="badge badge-active">
                        <?php echo $courses['status']; ?>
                    </span>
                </td>

                <td>
                     <a class="btn-ghost"
                       href="edit_course.php?id=<?php echo $courses["id"]; ?>">
                        تعديل
                    </a>


                    
                    <a class="btn btn-success btn-sm"
                    href="lessons.php?course_id=<?php echo $courses["id"]; ?>">
                    الدروس
                </a>
                
                <a class="btn btn-danger btn-sm" href="delete_course.php?id=<?php echo $courses["id"]; ?>">
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
        ← العودة
    </a>

</div>


<script src="../js/app.js?v=29" defer></script>
</body>

</html>