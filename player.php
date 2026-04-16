<?php
require_once 'php/config.php';
require_once 'php/api.php';
require_once 'php/security.php';

Security::init();

$vToken   = $_GET['v']  ?? '';
$tBatch   = $_GET['t']  ?? '';
$tSubject = $_GET['s']  ?? '';

$videoUrl = Security::decryptVideo($vToken);
$batchId  = Security::decryptId($tBatch);
$subject  = Security::decryptSubject($tSubject);

if (!$videoUrl || !$batchId) {
    header('Location: index.php');
    exit;
}

// Determine embed URL
function buildEmbedUrl(string $url): string {
    // Zoom recording
    if (str_contains($url, 'zoom.us/rec/share')) {
        // Zoom links open directly in iframe
        return $url;
    }
    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
        $vid = $m[1];
        $h = '';
        if (preg_match('/[?&]h=([a-f0-9]+)/', $url, $hm)) $h = '?h='.$hm[1];
        return "https://player.vimeo.com/video/{$vid}{$h}&autoplay=1&title=0&byline=0";
    }
    // YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
        return "https://www.youtube.com/embed/{$m[1]}?autoplay=1";
    }
    return $url;
}

$embedUrl = buildEmbedUrl($videoUrl);

// Get title from referer data
$data    = Api::getBatchDetail($batchId);
$bName   = $data['data']['batch_name'] ?? 'Batch';
$bySubj  = $data['data']['videos_by_subject'] ?? [];
$videos  = $bySubj[$subject] ?? [];
$title   = 'Lecture';
foreach ($videos as $v) {
    if ($v['url'] === $videoUrl) { $title = $v['title']; break; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta name="robots" content="noindex,nofollow"/>
<title><?= htmlspecialchars($title) ?> — SpidyUniverse</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body class="player-body">

<div class="noise-overlay"></div>

<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="brand">
      <span class="brand-icon">🕷</span>
      <span class="brand-text">Spidy<em>Universe</em></span>
    </a>
    <nav class="header-nav">
      <a href="lectures.php?t=<?= urlencode($tBatch) ?>&s=<?= urlencode($tSubject) ?>" class="nav-item">
        ← Back to Lectures
      </a>
    </nav>
  </div>
</header>

<div class="player-wrap">

  <div class="player-breadcrumb">
    <a href="index.php">Batches</a> <span>›</span>
    <a href="batch.php?t=<?= urlencode($tBatch) ?>"><?= htmlspecialchars($bName) ?></a> <span>›</span>
    <a href="lectures.php?t=<?= urlencode($tBatch) ?>&s=<?= urlencode($tSubject) ?>"><?= htmlspecialchars($subject) ?></a> <span>›</span>
    <span><?= htmlspecialchars($title) ?></span>
  </div>

  <h2 class="player-title"><?= htmlspecialchars($title) ?></h2>

  <!-- SECURE IFRAME PLAYER -->
  <div class="player-container" id="playerContainer">
    <div class="player-loading" id="playerLoading">
      <div class="load-spinner"></div>
      <p>Loading lecture…</p>
    </div>
    <iframe
      id="secureFrame"
      src="<?= htmlspecialchars($embedUrl) ?>"
      allowfullscreen
      allow="autoplay; fullscreen; picture-in-picture"
      referrerpolicy="no-referrer"
      sandbox="allow-scripts allow-same-origin allow-forms allow-presentation"
      onload="document.getElementById('playerLoading').style.display='none'"
    ></iframe>
  </div>

  <!-- NEXT / PREV navigation -->
  <?php
    $prevToken = null; $nextToken = null;
    foreach ($videos as $i => $v) {
      if ($v['url'] === $videoUrl) {
        if ($i > 0) $prevToken = Security::encryptVideo($videos[$i-1]['url'], $batchId);
        if ($i < count($videos)-1) $nextToken = Security::encryptVideo($videos[$i+1]['url'], $batchId);
        break;
      }
    }
  ?>
  <div class="player-nav">
    <?php if ($prevToken): ?>
      <a href="player.php?v=<?= urlencode($prevToken) ?>&t=<?= urlencode($tBatch) ?>&s=<?= urlencode($tSubject) ?>" class="pnav-btn">
        <i class="fas fa-chevron-left"></i> Previous
      </a>
    <?php else: ?>
      <span class="pnav-btn disabled"><i class="fas fa-chevron-left"></i> Previous</span>
    <?php endif; ?>

    <span class="pnav-label"><?= htmlspecialchars($subject) ?></span>

    <?php if ($nextToken): ?>
      <a href="player.php?v=<?= urlencode($nextToken) ?>&t=<?= urlencode($tBatch) ?>&s=<?= urlencode($tSubject) ?>" class="pnav-btn">
        Next <i class="fas fa-chevron-right"></i>
      </a>
    <?php else: ?>
      <span class="pnav-btn disabled">Next <i class="fas fa-chevron-right"></i></span>
    <?php endif; ?>
  </div>

</div><!-- /player-wrap -->

<footer class="site-footer">
  <div class="footer-inner">
    <span class="brand-text-sm">🕷 SpidyUniverse</span>
    <p>Preparing India's Defence Officers</p>
  </div>
</footer>

<script src="assets/js/security.js"></script>
</body>
</html>
