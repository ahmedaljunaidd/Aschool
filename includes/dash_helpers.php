<?php

/*
 * ملف بسيط يحتوي على دالة واحدة لتنقية النصوص قبل عرضها.
 * لا يحتوي على أي استعلامات قاعدة بيانات.
 * هيكل الصفحة (HTML) موجود داخل كل صفحة بشكل واضح ومباشر.
 */

require_once __DIR__ . '/icons.php';

if (!function_exists('dui_e')) {
    function dui_e($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}
