<?php
require_once 'php/config.php';
require_once 'php/api.php';
require_once 'php/security.php';

Security::init();

$tBatch   = $_GET['t'] ?? '';
$tSubject = $_GET['s'] ?? '';

$batchId = Security::decryptId($tBatch);
$subject = Security::decryptSubject($tSubject);

if (!$batchId || $subject === false) {
    header('Location: index.php');
    exit;
}

$data = Api::getBatchDetail($batchId);
if (!$data || !($data['success'] ?? false)) {
    header('Location: index.php');
    exit;
}

$bName   = htmlspecialchars($data['data']['batch_name']);
$bySubj  = $data['data']['videos_by_subject'] ?? [];
$videos  = $bySubj[$subject] ?? [];
$subjectE = htmlspecialchars($subject);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta name="robots" content="noindex,nofollow"/>
<title><?= $subjectE ?> — <?= $bName ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

<div class="noise-overlay"></div>

<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="brand">
      <span class="brand-icon">🕷</span>
      <span class="brand-text">Spidy<em>Universe</em></span>
    </a>
    <nav class="header-nav">
      <a href="batch.php?t=<?= urlencode($tBatch) ?>" class="nav-item">← Subjects</a>
    </nav>
  </div>
</header>

<div class="page-hero mini">
  <div class="breadcrumb">
    <a href="index.php">Batches</a> <span>›</span>
    <a href="batch.php?t=<?= urlencode($tBatch) ?>"><?= $bName ?></a> <span>›</span>
    <span><?= $subjectE ?></span>
  </div>
  <h1 class="page-title"><?= $subjectE ?></h1>
  <div class="hero-stats">
    <div class="stat-pill"><strong><?= count($videos) ?></strong> Lectures</div>
  </div>
</div>

<main class="lectures-wrap">
  <div class="lectures-list" id="lecturesList">
    <?php foreach ($videos as $idx => $video): ?>
      <?php
        $vToken = Security::encryptVideo($video['url'], $batchId);
        $title  = htmlspecialchars($video['title']);
        $num    = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
      ?>
      <a href="player.php?v=<?= urlencode($vToken) ?>&t=<?= urlencode($tBatch) ?>&s=<?= urlencode($tSubject) ?>"
         class="lecture-row">
        <div class="lec-num"><?= $num ?></div>
        <div class="lec-body">
          <div class="lec-title"><?= $title ?></div>
          <div class="lec-subject"><?= $subjectE ?></div>
        </div>
        <div class="lec-play">
          <i class="fas fa-play-circle"></i>
        </div>
      </a>
    <?php endforeach; ?>

    <?php if (empty($videos)): ?>
      <div class="empty-state">
        <i class="fas fa-video-slash"></i>
        <p>No lectures available yet.</p>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer class="site-footer">
  <div class="footer-inner">
    <span class="brand-text-sm">🕷 SpidyUniverse</span>
    <p>Preparing India's Defence Officers</p>
  </div>
</footer>

<script src="assets/js/security.js"></script>
</body>
</html>
