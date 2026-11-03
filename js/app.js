/* ============================================================
   Aschool — app.js v21
   - تذكيرات سريعة (تُحفظ في المتصفح، بدون إنترنت)
   - مؤقت وضع التركيز
   - نسخ الرابط / العودة للأعلى
   - رسائل تحقق عربية قصيرة ومحترمة لحقول الإدخال
   - يفتح القائمة الجانبية في الهواتف
   لا يوجد أي اتصال بالشبكة ولا أي وقت انتظار.
   ============================================================ */
(function () {
  'use strict';

  var REM_KEY = 'aschool.reminders.v1';
  var FOCUS_KEY = 'aschool.focus.v1';
  var THEME_KEY = 'aschool.theme';

  var doc = document;
  var body = doc.body;

  function $(sel, root) { return (root || doc).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || doc).querySelectorAll(sel)); }

  function esc(str) {
    return String(str == null ? '' : str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  var ICON_PATHS = {
    bell: '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
    clock: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    link: '<path d="M10 13a5 5 0 0 0 7.07 0l2.12-2.12a5 5 0 0 0-7.07-7.07L10.7 5.23"/><path d="M14 11a5 5 0 0 0-7.07 0L4.81 13.1a5 5 0 0 0 7.07 7.07l1.42-1.41"/>',
    up: '<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>',
    plus: '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
    x: '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
    check: '<polyline points="20 6 9 17 4 12"/>',
    trash: '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    play: '<polygon points="6 4 20 12 6 20 6 4"/>',
    pause: '<line x1="9" y1="5" x2="9" y2="19"/><line x1="15" y1="5" x2="15" y2="19"/>',
    reset: '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>',
    menu: '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
    calendar: '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
    home: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    book: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
    'book-open': '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
    folder: '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
    settings: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
    globe: '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
    heart: '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
    user: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'user-plus': '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
    'user-check': '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
    edit: '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/>',
    eye: '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    chart: '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
    'log-out': '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
    graduation: '<path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>',
    shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    layers: '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
    alert: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
    sun: '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>',
    moon: '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
    compass: '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>',
    search: '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
    filter: '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
    award: '<circle cx="12" cy="8" r="6"/><polyline points="8.5 13.5 7.5 22 12 19.5 16.5 22 15.5 13.5"/>'
  };

  function svg(name, cls) {
    return '<svg class="ic ' + (cls || '') + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + (ICON_PATHS[name] || '') + '</svg>';
  }

  function readJSON(key, fallback) {
    try {
      var raw = window.localStorage.getItem(key);
      if (!raw) return fallback;
      var val = JSON.parse(raw);
      return val == null ? fallback : val;
    } catch (e) { return fallback; }
  }

  function writeJSON(key, value) {
    try { window.localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* private mode */ }
  }

  /* ==========================================================
     TOASTS
     ========================================================== */
  var toastWrap = null;

  function toast(message, type) {
    if (!toastWrap) {
      toastWrap = doc.createElement('div');
      toastWrap.className = 'fx-toasts';
      toastWrap.setAttribute('aria-live', 'polite');
      body.appendChild(toastWrap);
    }
    var el = doc.createElement('div');
    el.className = 'fx-toast' + (type ? ' ' + type : '');
    el.innerHTML = svg(type === 'err' ? 'bell' : type === 'warn' ? 'clock' : 'check', 'ic-sm') +
      '<span></span>';
    el.lastChild.textContent = message;
    toastWrap.appendChild(el);
    window.setTimeout(function () {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, 2600);
  }

  /* ==========================================================
     REMINDERS
     ========================================================== */
  function loadReminders() {
    var list = readJSON(REM_KEY, []);
    if (!Array.isArray(list)) return [];
    return list.filter(function (r) { return r && typeof r.title === 'string'; });
  }

  function saveReminders(list) { writeJSON(REM_KEY, list); }

  function reminderDate(r) {
    var d = new Date(r.time);
    return isNaN(d.getTime()) ? null : d;
  }

  function formatDate(r) {
    var d = reminderDate(r);
    if (!d) return 'بدون وقت';
    try {
      return d.toLocaleString('ar-IQ', {
        weekday: 'short', day: 'numeric', month: 'short',
        hour: '2-digit', minute: '2-digit'
      });
    } catch (e) {
      return d.toLocaleString('ar');
    }
  }

  function isLate(r) {
    var d = reminderDate(r);
    return !!(d && !r.done && d.getTime() < Date.now());
  }

  function isSoon(r) {
    var d = reminderDate(r);
    if (!d || r.done) return false;
    var diff = d.getTime() - Date.now();
    return diff > 0 && diff < 60 * 60 * 1000;
  }

  function sortReminders(list) {
    return list.slice().sort(function (a, b) {
      if (!!a.done !== !!b.done) return a.done ? 1 : -1;
      var da = reminderDate(a), db = reminderDate(b);
      return (da ? da.getTime() : Infinity) - (db ? db.getTime() : Infinity);
    });
  }

  function updateBadges() {
    var list = loadReminders();
    var open = list.filter(function (r) { return !r.done; }).length;
    $$('[data-reminder-badge]').forEach(function (b) {
      b.textContent = open > 99 ? '99+' : String(open);
      if (open > 0) b.removeAttribute('hidden');
      else b.setAttribute('hidden', 'hidden');
    });
  }

  function renderReminders() {
    var list = sortReminders(loadReminders());
    $$('[data-reminder-inline]').forEach(function (box) {
      if (!list.length) {
        box.innerHTML = '<div class="reminder-empty">' + svg('bell', 'ic-lg') +
          '<p>لا توجد تذكيرات بعد.<br>أضف تذكيرًا سريعًا حتى لا تفوتك مهمة.</p></div>';
      } else {
        box.innerHTML = '<div class="reminder-list">' + list.map(function (r) {
          var timeCls = isLate(r) ? ' is-late' : isSoon(r) ? ' is-soon' : '';
          return '<div class="reminder-item' + (r.done ? ' is-done' : '') + '" data-rem-id="' + esc(r.id) + '">' +
            '<button type="button" class="reminder-check" data-rem-toggle="' + esc(r.id) + '" aria-label="' + (r.done ? 'إعادة فتح التذكير' : 'تم إنجاز التذكير') + '">' + svg('check', 'ic-sm') + '</button>' +
            '<div class="reminder-body">' +
              '<span class="reminder-title">' + esc(r.title) + '</span>' +
              '<span class="reminder-time' + timeCls + '">' + esc(formatDate(r)) + '</span>' +
            '</div>' +
            '<button type="button" class="reminder-del" data-rem-del="' + esc(r.id) + '" aria-label="حذف التذكير">' + svg('trash', 'ic-sm') + '</button>' +
          '</div>';
        }).join('') + '</div>';
      }

      var foot = doc.createElement('div');
      foot.className = 'reminder-foot';
      foot.innerHTML = '<button type="button" class="panel-action" data-reminder-open>' + svg('plus', 'ic-sm') + ' تذكير جديد</button>' +
        '<button type="button" class="reminder-perm" data-notify-perm>تفعيل إشعارات المتصفح</button>';
      box.appendChild(foot);
    });
    updateBadges();
  }

  function addReminder(title, time) {
    var list = loadReminders();
    list.push({
      id: 'r' + Date.now().toString(36) + Math.random().toString(36).slice(2, 6),
      title: title,
      time: time,
      done: false,
      notified: false
    });
    saveReminders(list);
    renderReminders();
  }

  function toggleReminder(id) {
    var list = loadReminders();
    list.forEach(function (r) { if (r.id === id) r.done = !r.done; });
    saveReminders(list);
    renderReminders();
  }

  function removeReminder(id) {
    saveReminders(loadReminders().filter(function (r) { return r.id !== id; }));
    renderReminders();
  }

  function checkDueReminders() {
    var list = loadReminders();
    var changed = false;
    list.forEach(function (r) {
      var d = reminderDate(r);
      if (!r.done && !r.notified && d && d.getTime() <= Date.now()) {
        r.notified = true;
        changed = true;
        toast('تذكير: ' + r.title, 'warn');
        try {
          if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Aschool — تذكير', { body: r.title });
          }
        } catch (e) { /* ignore */ }
      }
    });
    if (changed) { saveReminders(list); renderReminders(); }
  }

  /* ==========================================================
     FOCUS TIMER
     ========================================================== */
  var focus = {
    total: 25 * 60,
    remaining: 25 * 60,
    endAt: 0,
    running: false,
    tick: null
  };

  function loadFocus() {
    var saved = readJSON(FOCUS_KEY, null);
    if (!saved) return;
    focus.total = Math.max(60, Number(saved.total) || 1500);
    if (saved.running && Number(saved.endAt) > Date.now()) {
      focus.running = true;
      focus.endAt = Number(saved.endAt);
      focus.remaining = Math.max(0, Math.round((focus.endAt - Date.now()) / 1000));
    } else {
      focus.remaining = focus.total;
    }
  }

  function saveFocus() {
    writeJSON(FOCUS_KEY, { total: focus.total, running: focus.running, endAt: focus.endAt });
  }

  function focusTick() {
    if (focus.running) {
      focus.remaining = Math.max(0, Math.round((focus.endAt - Date.now()) / 1000));
      if (focus.remaining <= 0) {
        focus.running = false;
        focus.remaining = 0;
        saveFocus();
        toast('انتهى وقت التركيز. أحسنت!', 'ok');
        try {
          if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Aschool — وضع التركيز', { body: 'انتهى وقت التركيز (25 دقيقة).' });
          }
        } catch (e) { /* ignore */ }
      }
    }
    paintFocus();
  }

  function paintFocus() {
    var box = $('[data-focus-box]');
    if (!box) return;
    var m = Math.floor(focus.remaining / 60);
    var s = focus.remaining % 60;
    var timeEl = $('[data-focus-time]', box);
    var statusEl = $('[data-focus-status]', box);
    var barEl = $('[data-focus-bar]', box);
    var startBtn = $('[data-focus-start]');
    if (timeEl) timeEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    if (statusEl) {
      statusEl.textContent = focus.running ? 'التركيز جارٍ… ابتعد عن المشتتات'
        : focus.remaining === 0 ? 'انتهت الجلسة، خذ استراحة قصيرة'
        : 'جاهز للبدء — 25 دقيقة تركيز';
    }
    if (barEl) {
      var pct = focus.total ? ((focus.total - focus.remaining) / focus.total) * 100 : 0;
      barEl.style.width = Math.min(100, Math.max(0, pct)) + '%';
    }
    if (startBtn) {
      startBtn.innerHTML = (focus.running ? svg('pause', 'ic-sm') + ' إيقاف مؤقت' : svg('play', 'ic-sm') + ' ابدأ التركيز');
    }
  }

  function focusStartPause() {
    if (focus.running) {
      focus.running = false;
      focus.remaining = Math.max(1, Math.round((focus.endAt - Date.now()) / 1000));
    } else {
      if (focus.remaining <= 0) focus.remaining = focus.total;
      focus.running = true;
      focus.endAt = Date.now() + focus.remaining * 1000;
    }
    saveFocus();
    paintFocus();
  }

  function focusReset() {
    focus.running = false;
    focus.endAt = 0;
    focus.remaining = focus.total;
    saveFocus();
    paintFocus();
  }

  /* ==========================================================
     UI INJECTION
     ========================================================== */
  /* ==========================================================
     SHARED PAGE SHELL — نفس الهوية لكل صفحات الإدارة/المعلم/الطالب
     ========================================================== */
  var ROLE_META = {
    admin: {
      label: 'لوحة الإدارة',
      short: 'مدير المنصة',
      avatar: 'أ',
      nav: [
        { section: 'القائمة الرئيسية' },
        { href: 'dashboard.php', icon: 'home', label: 'الرئيسية' },
        { href: 'users.php', icon: 'users', label: 'المستخدمون' },
        { href: 'admin_activation.php', icon: 'user-check', label: 'طلبات التفعيل' },
        { section: 'المحتوى' },
        { href: 'categories.php', icon: 'folder', label: 'التصنيفات' },
        { href: 'courses.php', icon: 'book', label: 'الدورات' },
        { href: 'course_requests.php', icon: 'clock', label: 'طلبات المراجعة' },
        { section: 'النظام' },
        { href: 'settings.php', icon: 'settings', label: 'الإعدادات' },
        { href: '../courses.php', icon: 'globe', label: 'عرض المنصة' }
      ]
    },
    teacher: {
      label: 'لوحة المعلم',
      short: 'حساب معلم',
      avatar: 'م',
      nav: [
        { section: 'القائمة الرئيسية' },
        { href: 'dashboard.php', icon: 'home', label: 'الرئيسية' },
        { href: 'courses.php', icon: 'book', label: 'دوراتي' },
        { section: 'المنصة' },
        { href: '../courses.php', icon: 'globe', label: 'استكشاف المنصة' }
      ]
    },
    user: {
      label: 'لوحة الطالب',
      short: 'حساب طالب',
      avatar: 'ط',
      nav: [
        { section: 'التعلّم' },
        { href: 'dashboard.php', icon: 'home', label: 'الرئيسية' },
        { href: 'browse.php', icon: 'compass', label: 'استكشف الدورات' },
        { href: 'my_courses.php', icon: 'book-open', label: 'دوراتي' },
        { href: 'favorites.php', icon: 'heart', label: 'المفضلة' },
        { section: 'الحساب' },
        { href: 'profile.php', icon: 'user', label: 'الملف الشخصي' }
      ]
    }
  };

  var EMOJI_RE = /[\u{1F000}-\u{1FAFF}\u{2300}-\u{23FF}\u{2600}-\u{27BF}\u{FE0F}\u{2B00}-\u{2BFF}]/gu;

  function hasEmoji(text) {
    EMOJI_RE.lastIndex = 0;
    return EMOJI_RE.test(text);
  }

  function currentRole() {
    var p = window.location.pathname.toLowerCase();
    if (/(^|\/)admin\//.test(p)) return 'admin';
    if (/(^|\/)teacher\//.test(p)) return 'teacher';
    if (/(^|\/)user\//.test(p)) return 'user';
    return null;
  }

  function pageTitle(fallback) {
    var raw = (doc.title || '').split('—')[0].split('|')[0].trim();
    return raw || fallback;
  }

  function makeEl(tag, cls, html) {
    var node = doc.createElement(tag);
    node.className = cls || '';
    if (html != null) node.innerHTML = html;
    return node;
  }

  function iconForHref(href) {
    var h = String(href || '').toLowerCase();
    if (h.indexOf('logout') >= 0) return 'log-out';
    if (h.indexOf('../courses') >= 0) return 'globe';
    if (h.indexOf('browse') >= 0) return 'compass';
    if (h.indexOf('dashboard') >= 0) return 'home';
    if (h.indexOf('admin_activation') >= 0) return 'user-check';
    if (h.indexOf('add_user') >= 0) return 'user-plus';
    if (h.indexOf('edit_user') >= 0 || h.indexOf('view_user') >= 0) return 'user';
    if (h.indexOf('users') >= 0) return 'users';
    if (h.indexOf('category') >= 0 || h.indexOf('categories') >= 0) return 'folder';
    if (h.indexOf('course_requests') >= 0) return 'clock';
    if (h.indexOf('course_detail') >= 0) return 'book-open';
    if (h.indexOf('lesson') >= 0) return 'play';
    if (h.indexOf('course') >= 0) return 'book';
    if (h.indexOf('settings') >= 0) return 'settings';
    if (h.indexOf('favorites') >= 0) return 'heart';
    if (h.indexOf('progress') >= 0) return 'chart';
    if (h.indexOf('edit_profile') >= 0) return 'edit';
    if (h.indexOf('profile') >= 0) return 'user';
    return 'layers';
  }

  function samePage(href) {
    try {
      var a = doc.createElement('a');
      a.href = href;
      var path = a.pathname.replace(/\/+$/, '');
      var here = window.location.pathname.replace(/\/+$/, '');
      return path === here;
    } catch (e) { return false; }
  }

  function isNavActive(href) {
    if (samePage(href)) return true;
    if (String(href || '').indexOf('../') === 0) return false;
    var here = (window.location.pathname.split('/').pop() || '').toLowerCase();
    var target = String(href || '').split('?')[0].split('/').pop().toLowerCase();
    if (/^(view_user|edit_user|add_user)\.php$/.test(here)) return target === 'users.php';
    if (/^(add_category|edit_category)\.php$/.test(here)) return target === 'categories.php';
    if (/^(view_course|edit_course|add_course|submit_course)\.php$/.test(here)) return target === 'courses.php';
    if (/^(add_lesson|edit_lesson|lessons)\.php$/.test(here)) return target === 'courses.php';
    if (/^course_detail\.php$/.test(here)) return target === 'browse.php';
    if (/^(course|lesson|next_lesson)\.php$/.test(here)) return target === 'my_courses.php';
    return false;
  }

  function cleanEmoji(root) {
    var walker = doc.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
    var node;
    var nodes = [];
    while ((node = walker.nextNode())) nodes.push(node);
    nodes.forEach(function (n) {
      n.nodeValue = n.nodeValue.replace(EMOJI_RE, '').replace(/\s{2,}/g, ' ');
    });
  }

  function brandBlock(meta) {
    return makeEl('div', 'side-brand',
      '<span class="brand-mark">A</span>' +
      '<span class="side-brand-text"><strong>Aschool</strong><small>' + esc(meta.label) + '</small></span>');
  }

  function userBlock(meta) {
    var name = '';
    try { name = (body.getAttribute('data-user-name') || '').trim(); } catch (e) { name = ''; }
    var display = name !== '' ? name : 'مرحباً بك';
    var initial = name !== '' ? name.substring(0, 1) : meta.avatar;
    return makeEl('div', 'side-user',
      '<span class="side-avatar">' + esc(initial) + '</span>' +
      '<span class="side-user-meta"><strong>' + esc(display) + '</strong><small>' + esc(meta.short) + '</small></span>');
  }

  function footerBlock() {
    return makeEl('div', 'sidebar-footer', 'منصة <strong>Aschool</strong> التعليمية');
  }

  function addIconToLink(link) {
    if (!link || link.classList.contains('sidebar-section-title')) return;
    var existing = link.querySelector('svg.ic');
    if (existing) {
      if (!existing.closest('.nav-ic')) {
        var wrap = doc.createElement('span');
        wrap.className = 'nav-ic';
        existing.parentNode.insertBefore(wrap, existing);
        wrap.appendChild(existing);
      }
    } else {
      var ic = doc.createElement('span');
      ic.className = 'nav-ic';
      ic.innerHTML = svg(iconForHref(link.getAttribute('href')), 'ic ic-sm');
      link.insertBefore(ic, link.firstChild);
    }
    cleanEmoji(link);
  }

  function buildSidebar(meta, role) {
    var sidebar = $('.sidebar');
    var generated = false;

    if (!sidebar) {
      sidebar = makeEl('aside', 'sidebar');
      body.insertBefore(sidebar, body.firstChild);
      generated = true;
    }

    var h2 = sidebar.querySelector('h2');
    if (h2) h2.parentNode.removeChild(h2);

    if (!sidebar.querySelector('.side-brand')) {
      sidebar.insertBefore(brandBlock(meta), sidebar.firstChild);
    }
    if (!sidebar.querySelector('.side-user')) {
      sidebar.insertBefore(userBlock(meta), sidebar.querySelector('.side-brand').nextSibling);
    }

    var links = $$('a', sidebar);
    if (!links.length) {
      var frag = doc.createDocumentFragment();
      meta.nav.forEach(function (item) {
        if (item.section) {
          frag.appendChild(makeEl('div', 'sidebar-section-title', esc(item.section)));
          return;
        }
        var a = doc.createElement('a');
        a.href = item.href;
        a.innerHTML = '<span class="nav-ic">' + svg(item.icon, 'ic ic-sm') + '</span>' +
          '<span class="nav-label">' + esc(item.label) + '</span>';
        if (isNavActive(item.href)) a.classList.add('active');
        frag.appendChild(a);
      });
      sidebar.appendChild(frag);
    } else {
      links.forEach(function (a) {
        if (a.closest('.side-brand') || a.closest('.side-user')) return;
        a.classList.remove('active');
        if (isNavActive(a.getAttribute('href'))) a.classList.add('active');
        addIconToLink(a);
      });
    }

    if (!sidebar.querySelector('.sidebar-footer')) sidebar.appendChild(footerBlock());
    if (generated || !sidebar.classList.contains('dash-sidebar')) sidebar.classList.add('shell-sidebar');
    return sidebar;
  }

  function buildTopbar(meta) {
    var nav = $('.navbar');
    if (!nav) {
      nav = doc.createElement('header');
      body.appendChild(nav);
    }

    var oldLogout = nav.querySelector('.logout');
    var logoutHref = oldLogout && oldLogout.getAttribute('href') ? oldLogout.getAttribute('href') : '../logout.php';

    nav.className = 'navbar dash-topbar';
    nav.innerHTML =
      '<button type="button" class="icon-btn side-toggle" data-sidebar-toggle aria-label="فتح القائمة">' + svg('menu') + '</button>' +
      '<div class="topbar-title"><small>' + esc(meta.label) + '</small><h2></h2></div>' +
      '<div class="topbar-actions">' +
        '<button type="button" class="icon-btn theme-toggle" data-theme-toggle aria-label="تبديل الوضع">' + svg('sun') + '</button>' +
        '<button type="button" class="icon-btn" data-reminder-open aria-label="التذكيرات">' + svg('bell') +
          '<span class="icon-badge" data-reminder-badge hidden>0</span></button>' +
        '<button type="button" class="icon-btn" data-focus-open aria-label="وضع التركيز">' + svg('clock') + '</button>' +
        '<a class="logout" href="' + esc(logoutHref) + '">' + svg('log-out', 'ic-sm') + ' تسجيل الخروج</a>' +
      '</div>';

    nav.querySelector('.topbar-title h2').textContent = pageTitle(meta.label);
    return nav;
  }

  function enhanceActionButtons() {
    $$('.btn-danger, .btn-delete').forEach(function (btn) {
      if (btn.querySelector('svg')) return;
      btn.insertAdjacentHTML('afterbegin', svg('trash', 'ic ic-sm') + ' ');
    });
    $$('.btn-view').forEach(function (btn) {
      if (btn.querySelector('svg')) return;
      btn.insertAdjacentHTML('afterbegin', svg('eye', 'ic ic-sm') + ' ');
    });
    $$('.btn-success').forEach(function (btn) {
      if (btn.querySelector('svg')) return;
      if (/تعديل/.test(btn.textContent)) btn.insertAdjacentHTML('afterbegin', svg('edit', 'ic ic-sm') + ' ');
    });

    // أزرار قديمة تحتوي إيموجي: نستبدلها بأيقونات مناسبة ونمنح الزر تصميماً واضحاً
    $$('.button, .btn, .btn-ghost, .btn-danger, .btn-success, .btn-view').forEach(function (btn) {
      var text = btn.textContent;
      if (hasEmoji(text)) cleanEmoji(btn);
      if (btn.querySelector('svg')) return;
      var name = '';
      if (/إنشاء|إضافة|جديد|تسجيل|إرسال/.test(text)) name = 'plus';
      else if (/حذف/.test(text)) name = 'trash';
      else if (/تعديل/.test(text)) name = 'edit';
      else if (/عرض|تفاصيل/.test(text)) name = 'eye';
      else if (/درس|دروس|دورة|دورات/.test(text)) name = 'book-open';
      else if (/خروج/.test(text)) name = 'log-out';
      else if (/حفظ|إكمال/.test(text)) name = 'check';
      else if (/إلغاء/.test(text)) name = 'x';
      else if (/المنصة|استكشاف/.test(text)) name = 'globe';
      else if (/المفضلة/.test(text)) name = 'heart';
      if (name) btn.insertAdjacentHTML('afterbegin', svg(name, 'ic ic-sm') + ' ');
    });

    // تنظيف شارات الحالة من الإيموجي لتبدو أنظف
    $$('.entry-status, .badge').forEach(cleanEmoji);
  }

  /* ==========================================================
     THEME — تبديل الوضع الداكن/الفاتح
     ========================================================== */

  function currentTheme() {
    if (body.classList.contains('theme-light')) return 'light';
    if (body.classList.contains('theme-dark')) return 'dark';
    try {
      return window.localStorage.getItem(THEME_KEY) === 'light' ? 'light' : 'dark';
    } catch (e) { return 'dark'; }
  }

  function ensureTheme() {
    if (!body.classList.contains('theme-light') && !body.classList.contains('theme-dark')) {
      body.classList.add('theme-dark');
    }
  }

  function paintThemeButtons() {
    var theme = currentTheme();
    var nextLabel = theme === 'light' ? 'الوضع الليلي' : 'الوضع النهاري';
    $$('[data-theme-toggle]').forEach(function (btn) {
      btn.innerHTML = svg(theme === 'light' ? 'moon' : 'sun');
      btn.setAttribute('aria-label', nextLabel);
      btn.setAttribute('title', nextLabel);
      if (btn.classList.contains('fx-btn')) btn.setAttribute('data-tip', nextLabel);
    });
  }

  function applyTheme(theme, persist) {
    body.classList.remove('theme-dark', 'theme-light');
    body.classList.add(theme === 'light' ? 'theme-light' : 'theme-dark');
    if (persist) {
      try { window.localStorage.setItem(THEME_KEY, theme); } catch (e) { /* private mode */ }
    }
    paintThemeButtons();
  }

  function toggleTheme() {
    applyTheme(currentTheme() === 'light' ? 'dark' : 'light', true);
  }

  function buildShell() {
    if (body.classList.contains('auth-page') || body.classList.contains('dash-page')) return;
    var role = currentRole();
    if (!role) {
      body.classList.add('public-page');
      return;
    }

    var meta = ROLE_META[role];
    body.classList.add('shell-page');

    var sidebar = buildSidebar(meta, role);
    body.classList.add('has-shell-sidebar');

    if (!sidebar) return;

    buildTopbar(meta);

    if (!$('.side-backdrop')) {
      var backdrop = makeEl('button', 'side-backdrop');
      backdrop.type = 'button';
      backdrop.setAttribute('data-sidebar-toggle', '');
      backdrop.setAttribute('tabindex', '-1');
      backdrop.setAttribute('aria-label', 'إغلاق القائمة');
      body.insertBefore(backdrop, body.firstChild);
    }

    enhanceActionButtons();
  }

  /* ==========================================================
     FLOATING CHROME (تذكيرات + تركيز + نسخ + أعلى الصفحة)
     ========================================================== */
  function buildChrome() {
    if ($('#fxDock')) return;

    var dock = doc.createElement('div');
    dock.className = 'fx-dock';
    dock.id = 'fxDock';
    dock.innerHTML =
      '<button type="button" class="fx-btn theme-toggle" data-theme-toggle data-tip="الوضع النهاري" aria-label="تبديل الوضع">' + svg('sun') + '</button>' +
      '<button type="button" class="fx-btn is-accent" data-reminder-open data-tip="تذكير جديد" aria-label="إضافة تذكير">' +
        svg('bell') + '<span class="icon-badge" data-reminder-badge hidden>0</span></button>' +
      '<button type="button" class="fx-btn" data-focus-open data-tip="وضع التركيز" aria-label="وضع التركيز">' + svg('clock') + '</button>' +
      '<button type="button" class="fx-btn" data-copy-link data-tip="نسخ رابط الصفحة" aria-label="نسخ رابط الصفحة">' + svg('link') + '</button>' +
      '<button type="button" class="fx-btn" data-scroll-top data-tip="أعلى الصفحة" aria-label="أعلى الصفحة">' + svg('up') + '</button>';
    body.appendChild(dock);

    var modalWrap = doc.createElement('div');
    modalWrap.innerHTML =
      '<div class="fx-modal" id="fxReminderModal" role="dialog" aria-modal="true" aria-labelledby="fxRemTitle">' +
        '<button type="button" class="fx-backdrop" data-fx-close aria-label="إغلاق"></button>' +
        '<div class="fx-dialog">' +
          '<div class="fx-dialog-head">' +
            '<span class="fx-dialog-icon">' + svg('bell', 'ic-lg') + '</span>' +
            '<div><h3 id="fxRemTitle">تذكير سريع</h3><p>اكتب المهمة ووقتها، وسنذكّرك أثناء تواجدك في الصفحة.</p></div>' +
            '<button type="button" class="fx-close" data-fx-close aria-label="إغلاق">' + svg('x', 'ic-sm') + '</button>' +
          '</div>' +
          '<form id="fxReminderForm" novalidate>' +
            '<div class="fx-field">' +
              '<label for="fxRemText">عنوان التذكير</label>' +
              '<input type="text" id="fxRemText" maxlength="120" placeholder="مثال: مراجعة الدرس الثالث" autocomplete="off" required>' +
            '</div>' +
            '<div class="fx-field">' +
              '<label for="fxRemTime">الوقت (اختياري)</label>' +
              '<input type="datetime-local" id="fxRemTime">' +
            '</div>' +
            '<div class="fx-actions">' +
              '<button type="submit" class="fx-primary">' + svg('plus', 'ic-sm') + ' إضافة التذكير</button>' +
              '<button type="button" class="fx-secondary" data-fx-close>إلغاء</button>' +
            '</div>' +
          '</form>' +
        '</div>' +
      '</div>' +

      '<div class="fx-modal" id="fxFocusModal" role="dialog" aria-modal="true" aria-labelledby="fxFocusTitle">' +
        '<button type="button" class="fx-backdrop" data-fx-close aria-label="إغلاق"></button>' +
        '<div class="fx-dialog">' +
          '<div class="fx-dialog-head">' +
            '<span class="fx-dialog-icon">' + svg('clock', 'ic-lg') + '</span>' +
            '<div><h3 id="fxFocusTitle">وضع التركيز</h3><p>جلسة هادئة لمدة 25 دقيقة بدون مشتتات.</p></div>' +
            '<button type="button" class="fx-close" data-fx-close aria-label="إغلاق">' + svg('x', 'ic-sm') + '</button>' +
          '</div>' +
          '<div class="focus-box" data-focus-box>' +
            '<div class="focus-time" data-focus-time>25:00</div>' +
            '<div class="focus-status" data-focus-status>جاهز للبدء — 25 دقيقة تركيز</div>' +
            '<div class="focus-bar"><span data-focus-bar></span></div>' +
          '</div>' +
          '<div class="fx-actions">' +
            '<button type="button" class="fx-primary" data-focus-start>' + svg('play', 'ic-sm') + ' ابدأ التركيز</button>' +
            '<button type="button" class="fx-secondary" data-focus-reset>' + svg('reset', 'ic-sm') + ' إعادة</button>' +
          '</div>' +
        '</div>' +
      '</div>';

    while (modalWrap.firstChild) body.appendChild(modalWrap.firstChild);
  }

  /* ==========================================================
     MODAL HELPERS
     ========================================================== */
  function openModal(id) {
    $$('.fx-modal.is-open').forEach(function (m) { m.classList.remove('is-open'); });
    var m = doc.getElementById(id);
    if (!m) return;
    m.classList.add('is-open');
    if (id === 'fxReminderModal') {
      var text = doc.getElementById('fxRemText');
      if (text) {
        text.value = '';
        window.setTimeout(function () { text.focus(); }, 30);
      }
      var time = doc.getElementById('fxRemTime');
      if (time && !time.value) time.value = defaultReminderTime();
      clearFieldError(text);
    }
    if (id === 'fxFocusModal') paintFocus();
  }

  function closeModals() {
    $$('.fx-modal.is-open').forEach(function (m) { m.classList.remove('is-open'); });
  }

  function defaultReminderTime() {
    var d = new Date(Date.now() + 60 * 60 * 1000);
    d.setSeconds(0, 0);
    function p(n) { return (n < 10 ? '0' : '') + n; }
    return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate()) + 'T' + p(d.getHours()) + ':' + p(d.getMinutes());
  }

  /* ==========================================================
     FIELD VALIDATION
     ========================================================== */
  var FIELD_MESSAGES = {
    email: 'لو سمحت، أدخل البريد الإلكتروني.',
    password: 'لو سمحت، أدخل كلمة المرور.',
    confirm_password: 'لو سمحت، أكّد كلمة المرور.',
    name: 'لو سمحت، أدخل الاسم.',
    full_name: 'لو سمحت، أدخل الاسم الكامل.',
    title: 'لو سمحت، أدخل العنوان.',
    description: 'لو سمحت، أضف وصفاً مختصراً.',
    content: 'لو سمحت، أدخل المحتوى.',
    price: 'لو سمحت، أدخل السعر.',
    birth_date: 'لو سمحت، اختر تاريخ الميلاد.',
    gender: 'لو سمحت، اختر الجنس.',
    category_id: 'لو سمحت، اختر التصنيف.',
    course_id: 'لو سمحت، اختر الدورة.',
    video_url: 'لو سمحت، أضف رابط الفيديو.',
    image: 'لو سمحت، اختر صورة.',
    old_password: 'لو سمحت، أدخل كلمة المرور الحالية.',
    new_password: 'لو سمحت، أدخل كلمة المرور الجديدة.'
  };

  function clearFieldError(field) {
    if (!field) return;
    field.classList.remove('is-invalid');
    field.removeAttribute('aria-invalid');
    var next = field.nextElementSibling;
    if (next && next.classList && next.classList.contains('field-error')) {
      next.parentNode.removeChild(next);
    }
  }

  function setFieldError(field, message) {
    if (!field) return;
    clearFieldError(field);
    field.classList.add('is-invalid');
    field.setAttribute('aria-invalid', 'true');
    var err = doc.createElement('div');
    err.className = 'field-error';
    err.innerHTML = '<span aria-hidden="true">•</span><span></span>';
    err.lastChild.textContent = message;
    field.insertAdjacentElement('afterend', err);
  }

  function fieldLabel(field) {
    var name = (field.getAttribute('name') || '').toLowerCase();
    if (FIELD_MESSAGES[name]) return FIELD_MESSAGES[name];
    var ph = (field.getAttribute('placeholder') || '').trim();
    if (ph) return 'لو سمحت، أدخل ' + ph.replace(/[:：]\s*$/, '') + '.';
    return 'هذا الحقل مطلوب، لو سمحت املأه.';
  }

  function validateField(field) {
    var type = (field.getAttribute('type') || field.tagName || '').toLowerCase();
    var name = (field.getAttribute('name') || '').toLowerCase();
    var value = (field.value || '').trim();
    var required = field.hasAttribute('required');

    if (required && !value) {
      return fieldLabel(field);
    }

    if (!value) return '';

    if (type === 'email' || name === 'email') {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        return 'البريد الإلكتروني غير صحيح، لو سمحت راجعه.';
      }
    }

    if (name === 'confirm_password') {
      var form = field.form;
      var pass = form ? form.querySelector('[name="password"]') : null;
      if (pass && pass.value && pass.value !== field.value) {
        return 'كلمتا المرور غير متطابقتين، لو سمحت راجعهما.';
      }
    }

    if (type === 'number' && value !== '' && isNaN(Number(value))) {
      return 'لو سمحت، أدخل رقماً صحيحاً.';
    }

    return '';
  }

  function validateForm(form) {
    if (!form || form.hasAttribute('data-native-validation')) return true;
    var invalid = [];
    $$('input, select, textarea', form).forEach(function (field) {
      if (field.type === 'hidden' || field.disabled) return;
      if (!field.hasAttribute('required') && !field.value) return;
      var msg = validateField(field);
      if (msg) {
        setFieldError(field, msg);
        invalid.push(field);
      } else {
        clearFieldError(field);
      }
    });
    if (invalid.length) {
      try { invalid[0].focus({ preventScroll: false }); } catch (e) { invalid[0].focus(); }
      return false;
    }
    return true;
  }

  function setupForms() {
    $$('form').forEach(function (form) {
      if (form.id === 'fxReminderForm') return;
      form.noValidate = true;
      form.addEventListener('submit', function (ev) {
        if (!validateForm(form)) {
          ev.preventDefault();
          ev.stopPropagation();
          toast('راجع الحقول المعلّمة بالأحمر، لو سمحت.', 'err');
        }
      }, true);

      $$('input, select, textarea', form).forEach(function (field) {
        field.addEventListener('input', function () {
          if (field.classList.contains('is-invalid')) {
            var msg = validateField(field);
            if (msg) setFieldError(field, msg);
            else clearFieldError(field);
          }
        });
        field.addEventListener('change', function () {
          if (field.classList.contains('is-invalid')) {
            var msg = validateField(field);
            if (msg) setFieldError(field, msg);
            else clearFieldError(field);
          }
        });
      });
    });
  }

  /* ==========================================================
     MISC ACTIONS
     ========================================================== */
  function copyLink() {
    var url = window.location.href;
    function done() { toast('تم نسخ رابط الصفحة.', 'ok'); }
    function fallback() {
      try {
        var ta = doc.createElement('textarea');
        ta.value = url;
        ta.setAttribute('readonly', 'readonly');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        body.appendChild(ta);
        ta.select();
        doc.execCommand('copy');
        body.removeChild(ta);
        done();
      } catch (e) { toast('تعذّر النسخ، انسخ الرابط من شريط العنوان.', 'warn'); }
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(done, fallback);
    } else {
      fallback();
    }
  }

  function askNotifyPermission() {
    if (!('Notification' in window)) {
      toast('متصفحك لا يدعم إشعارات المتصفح.', 'warn');
      return;
    }
    if (Notification.permission === 'granted') {
      toast('إشعارات المتصفح مفعّلة بالفعل.', 'ok');
      return;
    }
    Notification.requestPermission().then(function (p) {
      if (p === 'granted') toast('تم تفعيل الإشعارات.', 'ok');
      else toast('لم يتم تفعيل الإشعارات، لا مشكلة.', 'warn');
    });
  }

  function toggleSidebar() {
    body.classList.toggle('sidebar-open');
  }

  function closeSidebar() {
    body.classList.remove('sidebar-open');
  }

  function todayLabel() {
    $$('[data-today]').forEach(function (el) {
      var d = new Date();
      try {
        el.textContent = d.toLocaleDateString('ar-IQ', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      } catch (e) {
        el.textContent = d.toLocaleDateString('ar');
      }
    });
  }

  function initBars() {
    $$('[data-bar]').forEach(function (bar) {
      var value = Number(bar.getAttribute('data-bar')) || 0;
      var max = Number(bar.getAttribute('data-max')) || 0;
      var width = 0;
      if (max > 0) width = Math.min(100, Math.max(0, (value / max) * 100));
      else if (value > 0) width = 100;
      bar.style.width = '0%';
      window.requestAnimationFrame(function () {
        bar.style.width = width + '%';
      });
    });
  }

  /* ==========================================================
     EVENTS
     ========================================================== */
  function bind() {
    doc.addEventListener('click', function (ev) {
      var t = ev.target;
      if (!t || !t.closest) return;

      var openRem = t.closest('[data-reminder-open]');
      if (openRem) { ev.preventDefault(); openModal('fxReminderModal'); return; }

      var openFocus = t.closest('[data-focus-open]');
      if (openFocus) { ev.preventDefault(); openModal('fxFocusModal'); return; }

      if (t.closest('[data-copy-link]')) { ev.preventDefault(); copyLink(); return; }
      if (t.closest('[data-scroll-top]')) { ev.preventDefault(); window.scrollTo(0, 0); return; }
      if (t.closest('[data-notify-perm]')) { ev.preventDefault(); askNotifyPermission(); return; }

      if (t.closest('[data-theme-toggle]')) { ev.preventDefault(); toggleTheme(); return; }

      if (t.closest('[data-sidebar-toggle]')) { ev.preventDefault(); toggleSidebar(); return; }

      if (t.closest('[data-fx-close]')) { ev.preventDefault(); closeModals(); return; }

      var tog = t.closest('[data-rem-toggle]');
      if (tog) { ev.preventDefault(); toggleReminder(tog.getAttribute('data-rem-toggle')); return; }

      var del = t.closest('[data-rem-del]');
      if (del) { ev.preventDefault(); removeReminder(del.getAttribute('data-rem-del')); return; }

      if (t.closest('[data-focus-start]')) { ev.preventDefault(); focusStartPause(); return; }
      if (t.closest('[data-focus-reset]')) { ev.preventDefault(); focusReset(); return; }

      // إغلاق القائمة الجانبية عند النقر على الخلفية
      if (t.classList && t.classList.contains('side-backdrop')) { closeSidebar(); }
    });

    doc.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape') {
        closeModals();
        closeSidebar();
      }
    });

    var remForm = doc.getElementById('fxReminderForm');
    if (remForm) {
      remForm.addEventListener('submit', function (ev) {
        ev.preventDefault();
        var text = doc.getElementById('fxRemText');
        var time = doc.getElementById('fxRemTime');
        var title = text ? text.value.trim() : '';
        if (!title) {
          setFieldError(text, 'لو سمحت، اكتب عنوان التذكير.');
          if (text) text.focus();
          return;
        }
        if (title.length < 2) {
          setFieldError(text, 'العنوان قصير جداً، لو سمحت اكتب عنواناً أوضح.');
          if (text) text.focus();
          return;
        }
        clearFieldError(text);
        var when = time && time.value ? new Date(time.value).toISOString() : new Date(Date.now() + 60 * 60 * 1000).toISOString();
        addReminder(title, when);
        toast('تمت إضافة التذكير بنجاح.', 'ok');
        closeModals();
      });
    }

    var remText = doc.getElementById('fxRemText');
    if (remText) {
      remText.addEventListener('input', function () { clearFieldError(remText); });
    }
  }

  /* ==========================================================
     INIT
     ========================================================== */
  function init() {
    ensureTheme();
    buildShell();
    buildChrome();
    paintThemeButtons();
    bind();
    setupForms();
    loadFocus();
    renderReminders();
    paintFocus();
    todayLabel();
    initBars();

    focus.tick = window.setInterval(focusTick, 500);
    window.setInterval(checkDueReminders, 20000);
    window.setTimeout(checkDueReminders, 1200);
  }

  if (doc.readyState === 'loading') {
    doc.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
