<?php
// ── SpidyUniverse Configuration ──────────────────────────────
define('API_BASE_URL', 'https://ccccccddddddsssss.vercel.app');
define('SITE_URL',     '');   // leave empty for relative URLs

// Secret key for token encryption (change this to a long random string)
define('ENCRYPT_KEY',  'Sp!dy@2025#Un1v3rse$S3cur3&K3y^X9z');
define('ENCRYPT_IV',   'Sp!dyIV@2025#16b');   // exactly 16 bytes

// Session settings
define('SESSION_LIFETIME', 86400); // 24 hours

// Security - block devtools
define('DEVTOOLS_PROTECTION', true);

// Cache TTL for API responses (seconds)
define('CACHE_TTL', 300);

// Timezone
date_default_timezone_set('Asia/Kolkata');
