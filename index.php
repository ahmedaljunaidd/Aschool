<?php include 'includes/icons.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aschool — المنصة التعليمية</title>
    <link rel="stylesheet" href="css/style.css?v=29">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            Aschool
        </div>

        <ul class="nav-links">
            <li><a href="index.php"><?php echo icon('home','ic ic-sm'); ?> الرئيسية</a></li>
            <li><a href="courses.php"><?php echo icon('book-open','ic ic-sm'); ?> الكورسات</a></li>
            <li><a href="#"><?php echo icon('info','ic ic-sm'); ?> عن الموقع</a></li>
        </ul>

        <div class="nav-cta">
            <a href="register.php" class="btn-login btn-login-filled"><?php echo icon('user-plus','ic ic-sm'); ?> إنشاء حساب</a>
            <a href="login.php" class="btn-login"><?php echo icon('log-in','ic ic-sm'); ?> تسجيل الدخول</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-decoration"></div>

        <div class="hero-content">

            <div class="hero-badge">
                <?php echo icon('sparkles','ic ic-sm'); ?>
                <span>منصة تعليمية متكاملة</span>
            </div>

            <h1>مرحبًا في منصة <span class="brand-gradient">Aschool</span> التعليمية</h1>

            <p>
                تعلم مهارات جديدة من خلال دورات تعليمية منظمة وسهلة
                بإشراف نخبة من أفضل المدرسين المتخصصين.
            </p>

            <div class="hero-buttons">
                <a href="courses.php" class="btn-guide"><?php echo icon('book-open','ic ic-sm'); ?> استكشف الدورات</a>
                <a href="register.php" class="btn-login btn-login-filled"><?php echo icon('user-plus','ic ic-sm'); ?> إنشاء حساب</a>
                <a href="login.php" class="btn-login"><?php echo icon('log-in','ic ic-sm'); ?> تسجيل الدخول</a>
            </div>

        </div>

        <div class="hero-wave"></div>
    </section>


    <!-- Features -->
    <section class="features">

        <h2>لماذا تختار Aschool؟</h2>
        <p class="section-sub">كل ما تحتاجه للتعلم في مكان واحد</p>

        <div class="feature-container">

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('zap','ic ic-lg'); ?></div>
                <h3>سهولة الاستخدام</h3>
                <p>واجهة بسيطة وعصرية تجعل التعلم تجربة ممتعة وسلسة للجميع.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('shield','ic ic-lg'); ?></div>
                <h3>الأمان الكامل</h3>
                <p>حماية متكاملة لبيانات المستخدمين والحسابات بأعلى معايير الأمان.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('trending','ic ic-lg'); ?></div>
                <h3>سرعة الوصول</h3>
                <p>وصول فوري إلى جميع الخدمات والمحتوى التعليمي بدون انتظار.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('graduation','ic ic-lg'); ?></div>
                <h3>مدرسون متخصصون</h3>
                <p>نخبة من أفضل المدرسين ذوي الخبرة العالية في مجالاتهم.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('video','ic ic-lg'); ?></div>
                <h3>محتوى متنوع</h3>
                <p>فيديوهات ومقالات ودروس تفاعلية تناسب جميع مستويات التعلم.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><?php echo icon('award','ic ic-lg'); ?></div>
                <h3>شهادات معتمدة</h3>
                <p>احصل على شهادة إتمام عند إنهاء كل دورة تعليمية بنجاح.</p>
            </div>

        </div>

    </section>


    <!-- Stats -->
    <section class="stats-section">

        <div class="stat-box">
            <h3>+500</h3>
            <p>مستخدم مسجل</p>
        </div>

        <div class="stat-box">
            <h3>+50</h3>
            <p>دورة تعليمية</p>
        </div>

        <div class="stat-box">
            <h3>+1000</h3>
            <p>درس مكتمل</p>
        </div>

        <div class="stat-box">
            <h3>99%</h3>
            <p>رضا المستخدمين</p>
        </div>

    </section>


    <!-- About -->
    <section class="about-section">

        <div class="about-content">

            <h2>منصة واحدة لجميع احتياجاتك التعليمية</h2>

            <p>
                نوفر لك تجربة تعلم سهلة وسريعة وآمنة،
                مع إمكانية الوصول إلى محتوى تعليمي عالي الجودة
                من خلال واجهة حديثة ومنظمة تعمل على جميع الأجهزة.
            </p>

            <a href="courses.php" class="btn-guide">
                <?php echo icon('book','ic ic-sm'); ?> اكتشف الدورات
            </a>

        </div>

    </section>


    <!-- CTA -->
    <section class="cta-section">

        <h2>جاهز للبدء؟</h2>

        <p>
            أنشئ حسابك الآن مجانًا واستفد من جميع الدورات التعليمية المتاحة.
        </p>

        <a href="register.php" class="btn-guide">
            <?php echo icon('user-plus','ic ic-sm'); ?> إنشاء حساب مجاني
        </a>

    </section>


    <footer class="site-footer">

        <div class="footer-brand">Aschool</div>

        <p>
            منصة تعليمية تهدف إلى نشر المعرفة وتسهيل الوصول إلى الدورات التعليمية.
        </p>

    </footer>

</body>
</html>
