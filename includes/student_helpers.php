<?php

/*
 * مساعدات صفحات الطالب — إضافات جديدة فقط
 * لا تغيّر هذه الملفات أي منطق قديم في المنصة.
 */

require_once __DIR__ . '/icons.php';

if (!function_exists('stu_e')) {
    function stu_e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('stu_first_char')) {
    function stu_first_char($text) {
        $text = trim((string) $text);
        if ($text === '') return 'A';
        if (function_exists('mb_substr')) return mb_substr($text, 0, 1, 'UTF-8');
        return substr($text, 0, 1);
    }
}

if (!function_exists('stu_initials')) {
    function stu_initials($name) {
        return stu_first_char($name);
    }
}

if (!function_exists('stu_category_icon')) {
    function stu_category_icon($category_id) {
        $map = array(
            5 => 'hash',
            6 => 'globe',
            7 => 'layers',
            8 => 'graduation',
        );
        $id = (int) $category_id;
        return isset($map[$id]) ? $map[$id] : 'book-open';
    }
}

if (!function_exists('stu_teacher_name')) {
    function stu_teacher_name($course) {
        $name = isset($course['teacher_name']) ? trim((string) $course['teacher_name']) : '';
        return $name !== '' ? $name : 'فريق Aschool';
    }
}

if (!function_exists('stu_cover')) {
    function stu_cover($course, $extra_class = '') {
        $category_id = isset($course['category_id']) ? (int) $course['category_id'] : 0;
        $class = 'cat-' . ($category_id > 0 ? $category_id : 'x');
        $image = isset($course['image']) ? trim((string) $course['image']) : '';
        $title = isset($course['title']) ? (string) $course['title'] : '';

        $html = '<div class="stu-cover ' . $class;
        if ($extra_class !== '') $html .= ' ' . $extra_class;
        $html .= '">';

        if ($image !== '') {
            if (preg_match('~^https?://~i', $image)) {
                $src = $image;
            } elseif (strpos($image, '../') === 0) {
                $src = $image;
            } else {
                $src = '../' . ltrim($image, '/');
            }
            $html .= '<img src="' . stu_e($src) . '" alt="' . stu_e($title) . '" loading="lazy">';
        } else {
            $html .= '<span class="stu-cover-icon">' . icon(stu_category_icon($category_id), 'ic ic-xl') . '</span>';
            if ($title !== '') {
                $html .= '<span class="stu-cover-letter">' . stu_e(stu_first_char($title)) . '</span>';
            }
        }

        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('stu_progress')) {
    /**
     * حساب تقدم الطالب في دورة واحدة.
     * يعيد: total, done, percent, next (صف الدرس التالي أو null)
     */
    function stu_progress($conn, $user_id, $course_id) {
        $user_id = (int) $user_id;
        $course_id = (int) $course_id;

        $total = 0;
        $done = 0;
        $next = null;

        $result = mysqli_query($conn, "SELECT COUNT(*) AS c FROM lessons WHERE course_id = $course_id");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $total = (int) $row['c'];
        }

        $result = mysqli_query($conn,
            "SELECT COUNT(DISTINCT lp.lesson_id) AS c
             FROM lesson_progress lp
             INNER JOIN lessons l ON l.id = lp.lesson_id
             WHERE lp.user_id = $user_id
             AND l.course_id = $course_id
             AND lp.completed = 1");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $done = (int) $row['c'];
        }

        $result = mysqli_query($conn,
            "SELECT l.* FROM lessons l
             WHERE l.course_id = $course_id
             AND l.id NOT IN (
                 SELECT lesson_id FROM lesson_progress
                 WHERE user_id = $user_id AND completed = 1
             )
             ORDER BY l.lesson_order ASC, l.id ASC
             LIMIT 1");
        if ($result && mysqli_num_rows($result) > 0) {
            $next = mysqli_fetch_assoc($result);
        }

        $percent = $total > 0 ? (int) round(($done / $total) * 100) : 0;
        if ($percent > 100) $percent = 100;

        return array(
            'total' => $total,
            'done' => $done,
            'percent' => $percent,
            'next' => $next,
        );
    }
}

if (!function_exists('stu_completed_lesson_ids')) {
    function stu_completed_lesson_ids($conn, $user_id, $course_id) {
        $user_id = (int) $user_id;
        $course_id = (int) $course_id;
        $ids = array();

        $result = mysqli_query($conn,
            "SELECT DISTINCT lp.lesson_id
             FROM lesson_progress lp
             INNER JOIN lessons l ON l.id = lp.lesson_id
             WHERE lp.user_id = $user_id
             AND l.course_id = $course_id
             AND lp.completed = 1");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[(int) $row['lesson_id']] = true;
            }
        }
        return $ids;
    }
}

if (!function_exists('stu_is_enrolled')) {
    function stu_is_enrolled($conn, $user_id, $course_id) {
        $user_id = (int) $user_id;
        $course_id = (int) $course_id;
        $result = mysqli_query($conn,
            "SELECT id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id LIMIT 1");
        return $result && mysqli_num_rows($result) > 0;
    }
}

if (!function_exists('stu_course_href')) {
    function stu_course_href($course, $enrolled) {
        $id = isset($course['id']) ? (int) $course['id'] : 0;
        return $enrolled ? 'course.php?id=' . $id : 'course_detail.php?id=' . $id;
    }
}

if (!function_exists('stu_excerpt')) {
    function stu_excerpt($text, $limit = 130) {
        $text = trim(strip_tags((string) $text));
        if ($text === '') return '';
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($text, 'UTF-8') > $limit) {
                return mb_substr($text, 0, $limit, 'UTF-8') . '…';
            }
            return $text;
        }
        return strlen($text) > $limit ? substr($text, 0, $limit) . '…' : $text;
    }
}

if (!function_exists('stu_video_embed')) {
    function stu_video_embed($url) {
        $url = trim((string) $url);
        if ($url === '') return '';

        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i', $url, $m)) {
            return '<div class="stu-video"><iframe src="https://www.youtube.com/embed/' . stu_e($m[1]) . '" title="فيديو الدرس" allowfullscreen loading="lazy"></iframe></div>';
        }

        $src = preg_match('~^https?://~i', $url) ? $url : '../' . ltrim($url, '/');
        return '<div class="stu-video"><video controls preload="metadata" src="' . stu_e($src) . '"></video></div>';
    }
}

/* ============================================================
   استعلامات الطالب — قراءة فقط + مساعدات عرض مشتركة
   ============================================================ */

if (!function_exists('stu_categories')) {
    /**
     * كل التصنيفات مع عدد الدورات المعتمدة في كل تصنيف.
     */
    function stu_categories($conn) {
        $rows = array();
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM courses co
                         WHERE co.category_id = c.id
                           AND co.status IN ('active', 'approved')) AS course_count
                FROM categories c
                ORDER BY c.name ASC";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('stu_courses')) {
    /**
     * الدورات المعتمدة مع اسم المعلم والتصنيف وعدد الدروس والمتعلمين.
     *
     * خيارات: category_id, q, ids (مصفوفة), limit, exclude (مصفوفة)
     */
    function stu_courses($conn, $options = array()) {
        $where = array("c.status IN ('active', 'approved')");

        if (!empty($options['category_id'])) {
            $where[] = 'c.category_id = ' . (int) $options['category_id'];
        }

        if (isset($options['q']) && trim((string) $options['q']) !== '') {
            $term = mysqli_real_escape_string($conn, trim((string) $options['q']));
            $where[] = "(c.title LIKE '%$term%' OR c.description LIKE '%$term%')";
        }

        if (!empty($options['ids']) && is_array($options['ids'])) {
            $ids = array();
            foreach ($options['ids'] as $id) $ids[] = (int) $id;
            if (count($ids) > 0) $where[] = 'c.id IN (' . implode(',', $ids) . ')';
        }

        if (!empty($options['exclude']) && is_array($options['exclude'])) {
            $ids = array();
            foreach ($options['exclude'] as $id) $ids[] = (int) $id;
            if (count($ids) > 0) $where[] = 'c.id NOT IN (' . implode(',', $ids) . ')';
        }

        $sql = "SELECT c.*,
                       cat.name AS category_name,
                       u.name AS teacher_name,
                       (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lesson_count,
                       (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS learner_count
                FROM courses c
                LEFT JOIN categories cat ON cat.id = c.category_id
                LEFT JOIN users u ON u.id = c.teacher_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.id DESC";

        if (!empty($options['limit'])) {
            $sql .= ' LIMIT ' . (int) $options['limit'];
        }

        $rows = array();
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('stu_course')) {
    function stu_course($conn, $course_id) {
        $course_id = (int) $course_id;
        $sql = "SELECT c.*,
                       cat.name AS category_name,
                       u.name AS teacher_name,
                       (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lesson_count,
                       (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS learner_count
                FROM courses c
                LEFT JOIN categories cat ON cat.id = c.category_id
                LEFT JOIN users u ON u.id = c.teacher_id
                WHERE c.id = $course_id
                LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }
}

if (!function_exists('stu_enrolled_ids')) {
    function stu_enrolled_ids($conn, $user_id) {
        $user_id = (int) $user_id;
        $ids = array();
        $result = mysqli_query($conn, "SELECT course_id FROM enrollments WHERE user_id = $user_id");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[(int) $row['course_id']] = true;
            }
        }
        return $ids;
    }
}

if (!function_exists('stu_favorite_ids')) {
    function stu_favorite_ids($conn, $user_id) {
        $user_id = (int) $user_id;
        $ids = array();
        $result = mysqli_query($conn, "SELECT course_id FROM favorites WHERE user_id = $user_id");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[(int) $row['course_id']] = true;
            }
        }
        return $ids;
    }
}

if (!function_exists('stu_my_courses')) {
    /**
     * دورات الطالب المسجّل فيها + تقدمه في كل دورة.
     */
    function stu_my_courses($conn, $user_id) {
        $user_id = (int) $user_id;
        $rows = array();

        $sql = "SELECT c.*,
                       cat.name AS category_name,
                       u.name AS teacher_name,
                       (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lesson_count,
                       (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS learner_count
                FROM enrollments en
                INNER JOIN courses c ON c.id = en.course_id
                LEFT JOIN categories cat ON cat.id = c.category_id
                LEFT JOIN users u ON u.id = c.teacher_id
                WHERE en.user_id = $user_id
                AND c.status IN ('active', 'approved')
                ORDER BY c.id DESC";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('stu_course_card')) {
    /**
     * بطاقة دورة موحّدة لكل صفحات الطالب.
     *
     * options: enrolled(bool), favorite(bool), progress(array), return(string)
     */
    function stu_course_card($course, $options = array()) {
        $enrolled = !empty($options['enrolled']);
        $favorite = !empty($options['favorite']);
        $progress = isset($options['progress']) ? $options['progress'] : null;
        $return = isset($options['return']) ? (string) $options['return'] : '';

        $href = stu_course_href($course, $enrolled);
        $teacher = stu_teacher_name($course);
        $category = isset($course['category_name']) ? trim((string) $course['category_name']) : '';
        $lesson_count = isset($course['lesson_count']) ? (int) $course['lesson_count'] : 0;
        $learner_count = isset($course['learner_count']) ? (int) $course['learner_count'] : 0;

        ob_start();
        ?>
        <article class="stu-course">
            <a class="stu-cover-link" href="<?php echo stu_e($href); ?>" aria-label="<?php echo stu_e($course['title']); ?>">
                <?php echo stu_cover($course); ?>
            </a>
            <div class="stu-course-body">
                <span class="stu-course-cat"><?php echo icon(stu_category_icon(isset($course['category_id']) ? $course['category_id'] : 0), 'ic ic-xs'); ?> <?php echo stu_e($category !== '' ? $category : 'دورة'); ?></span>
                <a class="stu-course-title" href="<?php echo stu_e($href); ?>"><?php echo stu_e($course['title']); ?></a>
                <span class="stu-course-teacher">
                    <span class="stu-mini-avatar"><?php echo stu_e(stu_first_char($teacher)); ?></span>
                    <?php echo stu_e($teacher); ?>
                </span>
                <?php if (stu_excerpt($course['description'], 120) !== '') { ?>
                    <p class="stu-course-desc"><?php echo stu_e(stu_excerpt($course['description'], 120)); ?></p>
                <?php } ?>
                <div class="stu-course-meta">
                    <span><?php echo icon('book-open', 'ic ic-xs'); ?> <?php echo $lesson_count; ?> درس</span>
                    <span><?php echo icon('users', 'ic ic-xs'); ?> <?php echo $learner_count; ?> متعلم</span>
                </div>
                <?php if ($enrolled && is_array($progress)) { ?>
                    <div class="stu-progress-row">
                        <div class="stu-progress<?php echo $progress['percent'] >= 100 ? ' is-done' : ''; ?>">
                            <span style="width: <?php echo (int) $progress['percent']; ?>%"></span>
                        </div>
                        <b class="stu-progress-num"><?php echo (int) $progress['percent']; ?>%</b>
                    </div>
                <?php } ?>
            </div>
            <div class="stu-course-foot">
                <a class="stu-btn stu-btn-primary stu-btn-sm" href="<?php echo stu_e($href); ?>">
                    <?php echo icon($enrolled ? 'play' : 'eye', 'ic ic-xs'); ?>
                    <?php echo $enrolled ? 'متابعة التعلم' : 'عرض الدورة'; ?>
                </a>
                <form method="post" action="toggle_favorite.php">
                    <input type="hidden" name="course_id" value="<?php echo (int) $course['id']; ?>">
                    <input type="hidden" name="return" value="<?php echo stu_e($return); ?>">
                    <button type="submit" class="stu-fav<?php echo $favorite ? ' is-on' : ''; ?>" title="<?php echo $favorite ? 'إزالة من المفضلة' : 'إضافة للمفضلة'; ?>" aria-label="<?php echo $favorite ? 'إزالة من المفضلة' : 'إضافة للمفضلة'; ?>"><?php echo icon('heart', 'ic ic-sm'); ?></button>
                </form>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }
}
if (!function_exists('stu_stat')) {
    function stu_stat($icon_name, $value, $label, $tint = '') {
        $class = 'stu-stat-ic';
        if ($tint !== '') $class .= ' ' . $tint;
        echo '<div class="stu-stat">';
        echo '<span class="' . stu_e($class) . '">' . icon($icon_name, 'ic') . '</span>';
        echo '<span class="stu-stat-body"><b>' . stu_e($value) . '</b><span>' . stu_e($label) . '</span></span>';
        echo '</div>';
    }
}

if (!function_exists('stu_lesson_row')) {
    /**
     * صف درس داخل قائمة دروس الدورة.
     */
    function stu_lesson_row($lesson, $index, $is_done, $is_current = false) {
        $href = 'lesson.php?id=' . (int) $lesson['id'];
        $class = 'stu-lesson';
        if ($is_done) $class .= ' is-done';
        echo '<a class="' . $class . '" href="' . stu_e($href) . '">';
        echo '<span class="stu-lesson-num">' . ($is_done ? icon('check', 'ic ic-xs') : (int) $index) . '</span>';
        echo '<span class="stu-lesson-body">';
        echo '<span class="stu-lesson-title">' . stu_e($lesson['title']) . '</span>';
        echo '<span class="stu-lesson-meta">الدرس ' . (int) $index . (isset($lesson['lesson_order']) ? ' · ترتيب ' . (int) $lesson['lesson_order'] : '') . '</span>';
        echo '</span>';
        if ($is_current) {
            echo '<span class="stu-lesson-state">' . icon('play', 'ic ic-xs') . ' الآن</span>';
        } elseif ($is_done) {
            echo '<span class="stu-lesson-state">' . icon('check-circle', 'ic ic-xs') . ' مكتمل</span>';
        } else {
            echo '<span class="stu-lesson-state">' . icon('chevron-left', 'ic ic-xs') . ' ابدأ</span>';
        }
        echo '</a>';
    }
}