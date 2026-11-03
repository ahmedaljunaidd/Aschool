<?php

/*
 * محرك عرض محتوى الدروس
 * يدعم: عناوين (# و##)، نقاط (-) وأرقام (1.)، عريض (**)، كود سطري (`)،
 * وصناديق أكواد برمجية (``` مع اسم اللغة اختياري).
 */

function lesson_inline($s) {
    $s = htmlspecialchars(trim($s), ENT_QUOTES, "UTF-8");
    $s = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $s);
    $s = preg_replace('/`([^`]+)`/u', '<code class="inline-code">$1</code>', $s);
    return $s;
}

function lesson_close_lists(&$ul_open, &$ol_open) {
    $out = "";
    if ($ul_open) { $out .= "</ul>"; $ul_open = false; }
    if ($ol_open) { $out .= "</ol>"; $ol_open = false; }
    return $out;
}

function render_lesson_content($text) {

    $text = str_replace(array("\r\n", "\r"), "\n", $text);
    $lines = explode("\n", $text);

    $html = "";
    $ul_open = false;
    $ol_open = false;
    $in_code = false;
    $code_lang = "";
    $code_buf = "";

    foreach ($lines as $line) {

        $trimmed = trim($line);

        /* بداية/نهاية كتلة الكود */
        if (preg_match('/^```\s*([A-Za-z0-9+#.]*)\s*$/', $trimmed, $m)) {

            if (!$in_code) {

                $html .= lesson_close_lists($ul_open, $ol_open);
                $in_code = true;
                $code_lang = $m[1];
                $code_buf = "";

            } else {

                $html .= "<div class=\"code-block\">";
                if ($code_lang != "") {
                    $html .= "<span class=\"code-lang\">" . htmlspecialchars($code_lang) . "</span>";
                }
                $html .= "<pre><code>"
                       . htmlspecialchars(rtrim($code_buf), ENT_QUOTES, "UTF-8")
                       . "</code></pre></div>";
                $in_code = false;

            }

            continue;
        }

        if ($in_code) {

            $code_buf .= $line . "\n";
            continue;

        }

        if ($trimmed === "") {

            $html .= lesson_close_lists($ul_open, $ol_open);
            continue;

        }

        /* عنوان رئيسي # */
        if (preg_match('/^#\s+(.*)$/u', $line, $m)) {
            $html .= lesson_close_lists($ul_open, $ol_open);
            $html .= "<h2 class=\"lesson-h\">" . lesson_inline($m[1]) . "</h2>";
            continue;
        }

        /* عنوان فرعي ## */
        if (preg_match('/^##\s+(.*)$/u', $line, $m)) {
            $html .= lesson_close_lists($ul_open, $ol_open);
            $html .= "<h3 class=\"lesson-h\">" . lesson_inline($m[1]) . "</h3>";
            continue;
        }

        /* نقاط مجمعة */
        if (preg_match('/^[-*]\s+(.*)$/u', $line, $m)) {
            if ($ol_open) { $html .= "</ol>"; $ol_open = false; }
            if (!$ul_open) {
                $html .= "<ul class=\"lesson-notes\">";
                $ul_open = true;
            }
            $html .= "<li>" . lesson_inline($m[1]) . "</li>";
            continue;
        }

        /* قائمة مرقمة */
        if (preg_match('/^\d+[.)]\s+(.*)$/u', $line, $m)) {
            if ($ul_open) { $html .= "</ul>"; $ul_open = false; }
            if (!$ol_open) {
                $html .= "<ol class=\"lesson-notes\">";
                $ol_open = true;
            }
            $html .= "<li>" . lesson_inline($m[1]) . "</li>";
            continue;
        }

        /* فقرة */
        $html .= lesson_close_lists($ul_open, $ol_open);
        $html .= "<p>" . lesson_inline($line) . "</p>";
    }

    /* كتلة كود غير مغلقة */
    if ($in_code) {
        $html .= lesson_close_lists($ul_open, $ol_open);
        $html .= "<div class=\"code-block\">";
        if ($code_lang != "") {
            $html .= "<span class=\"code-lang\">" . htmlspecialchars($code_lang) . "</span>";
        }
        $html .= "<pre><code>"
               . htmlspecialchars(rtrim($code_buf), ENT_QUOTES, "UTF-8")
               . "</code></pre></div>";
    }

    $html .= lesson_close_lists($ul_open, $ol_open);

    return $html;
}