# 🕷️ SpidyUniverse Website

Professional defence exam prep website with secure PHP backend.

---

## 🚀 Deploy on Shared Hosting / cPanel

1. Upload ALL files to `public_html/` (or your subdomain folder)
2. Make sure PHP 7.4+ is enabled (cPanel → PHP Version)
3. Enable `mod_rewrite` for .htaccess to work
4. Done! Visit your domain

---

## 🔧 Required PHP Extensions
- `openssl` (for encryption) — enabled by default on most hosts
- `curl` (for API proxy calls) — enabled by default

---

## ⚙️ Configuration

Edit `php/config.php`:
```php
define('API_BASE_URL', 'https://ccccccddddddsssss.vercel.app');  // Your API URL
define('ENCRYPT_KEY',  'your-secret-key-change-this');            // Change this!
```

**Important:** Change `ENCRYPT_KEY` to a long random string for security.

---

## 📁 File Structure

```
public_html/
├── index.php          ← Homepage (batch list)
├── batch.php          ← Subjects page
├── lectures.php       ← Lecture list for a subject
├── player.php         ← Secure video player
├── .htaccess          ← Security rules
├── php/
│   ├── config.php     ← Configuration (API URL, keys)
│   ├── api.php        ← Server-side API proxy (hidden from browser)
│   └── security.php   ← Encryption class (AES-256-CBC)
└── assets/
    ├── css/style.css  ← Full stylesheet
    └── js/
        ├── security.js ← DevTools protection
        └── app.js      ← Batch filter/search
```

---

## 🔒 Security Features

| Feature | Details |
|---------|---------|
| API Hidden | All API calls via PHP cURL — never in browser |
| URLs Encrypted | AES-256-CBC encrypted tokens in all links |
| Video Links Hidden | Never in HTML source or network tab |
| DevTools Block | F12, Ctrl+Shift+I, right-click all blocked |
| Console Cleared | All console methods overridden to noop |
| Source Protection | Ctrl+U (view source) blocked |
| Headers | X-Frame, XSS protection, no-referrer set |
| PHP Cache | API responses cached 5 min server-side |

---

## 🌐 URL Structure

All URLs use encrypted tokens — no IDs or video links ever exposed:

```
/index.php                                   ← All batches
/batch.php?t=<encrypted_batch_token>         ← Subjects
/lectures.php?t=<token>&s=<subject_token>    ← Lecture list
/player.php?v=<encrypted_video_token>        ← Video player
```

---

*SpidyUniverse — Preparing India's Defence Officers 🕷️*
