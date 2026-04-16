/**
 * SpidyUniverse — Client Security Layer
 * Blocks DevTools, inspect element, source viewing, right-click
 * All video/API data is server-side only — nothing sensitive in JS
 */
(function () {
  'use strict';

  /* ── 1. Right-click disable ─────────────────── */
  document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
    return false;
  });

  /* ── 2. Keyboard shortcut block ─────────────── */
  document.addEventListener('keydown', function (e) {
    const k = e.key;
    const ctrl = e.ctrlKey || e.metaKey;

    // F12
    if (k === 'F12') { e.preventDefault(); triggerBlock(); return false; }

    // Ctrl+Shift+I / Ctrl+Shift+J / Ctrl+Shift+C / Ctrl+Shift+K
    if (ctrl && e.shiftKey && ['i','I','j','J','c','C','k','K'].includes(k)) {
      e.preventDefault(); triggerBlock(); return false;
    }

    // Ctrl+U (view source)
    if (ctrl && (k === 'u' || k === 'U')) {
      e.preventDefault(); return false;
    }

    // Ctrl+S (save page)
    if (ctrl && (k === 's' || k === 'S')) {
      e.preventDefault(); return false;
    }

    // Ctrl+P (print / source)
    if (ctrl && (k === 'p' || k === 'P')) {
      e.preventDefault(); return false;
    }
  });

  /* ── 3. DevTools size detection ─────────────── */
  var _devOpen = false;
  var _threshold = 160;

  function checkDevTools() {
    var w = window.outerWidth - window.innerWidth;
    var h = window.outerHeight - window.innerHeight;
    if (w > _threshold || h > _threshold) {
      if (!_devOpen) {
        _devOpen = true;
        triggerBlock();
      }
    } else {
      _devOpen = false;
    }
  }

  /* ── 4. console.log detection trick ─────────── */
  var _consoleOpen = false;
  var _devToolsCheck = /./;
  _devToolsCheck.toString = function () {
    _consoleOpen = true;
    triggerBlock();
    return '';
  };

  function detectConsole() {
    _consoleOpen = false;
    console.log('%c', _devToolsCheck);
    if (_consoleOpen) triggerBlock();
  }

  /* ── 5. debugger loop ────────────────────────── */
  function antiDebug() {
    try {
      var start = new Date();
      // eslint-disable-next-line no-debugger
      debugger;
      var end = new Date();
      if (end - start > 100) {
        triggerBlock();
      }
    } catch (e) {}
  }

  /* ── 6. Trigger block ────────────────────────── */
  function triggerBlock() {
    try {
      // Clear the page body
      document.body.innerHTML =
        '<div style="' +
        'display:flex;align-items:center;justify-content:center;' +
        'height:100vh;background:#080a0f;color:#6b7394;' +
        'font-family:sans-serif;text-align:center;flex-direction:column;gap:16px' +
        '">' +
        '<span style="font-size:3rem">🕷</span>' +
        '<strong style="color:#e8eaf0;font-size:1.2rem">Access Restricted</strong>' +
        '<p style="font-size:.85rem;max-width:320px">Developer tools are not permitted on this platform.</p>' +
        '<a href="/" style="color:#e63946;font-size:.8rem;margin-top:8px">← Go Back</a>' +
        '</div>';
    } catch (e) {}
    // Also navigate away
    setTimeout(function () {
      try { window.location.href = '/'; } catch (e) {}
    }, 2500);
  }

  /* ── 7. Override console methods ─────────────── */
  (function () {
    var noop = function () {};
    var methods = ['log','warn','error','info','debug','table','dir','trace'];
    if (window.console) {
      methods.forEach(function (m) {
        try { window.console[m] = noop; } catch (e) {}
      });
    }
  })();

  /* ── 8. Disable text selection on sensitive areas */
  document.addEventListener('selectstart', function (e) {
    if (e.target.closest('.player-container, .batch-card, .lecture-row')) {
      e.preventDefault();
    }
  });

  /* ── 9. Prevent drag ─────────────────────────── */
  document.addEventListener('dragstart', function (e) {
    e.preventDefault();
  });

  /* ── Run checks on interval ─────────────────── */
  setInterval(checkDevTools, 1000);
  setInterval(detectConsole, 3000);
  setInterval(antiDebug, 5000);
  checkDevTools();

})();
