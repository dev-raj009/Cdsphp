<?php
require_once 'php/config.php';
require_once 'php/api.php';
require_once 'php/security.php';

Security::init();

$batches = Api::getBatches();
$stats   = Api::getStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta name="robots" content="noindex,nofollow"/>
<title>SpidyUniverse — Defence Exam Prep</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

<div class="noise-overlay"></div>

<!-- HEADER -->
<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="brand">
      <span class="brand-icon">🕷</span>
      <span class="brand-text">Spidy<em>Universe</em></span>
    </a>
    <nav class="header-nav">
      <a href="index.php" class="nav-item active">Batches</a>
      <a href="#" class="nav-item" onclick="scrollToSearch()">Search</a>
    </nav>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-text">SPIDY</div>
  <div class="hero-content">
    <div class="hero-badge"><span class="pulse-dot"></span> Live Batches Available</div>
    <h1 class="hero-title">Your Gateway to<br/><span class="gradient-text">Defence Exam Success</span></h1>
    <p class="hero-sub">CDS · NDA · AFCAT · CAPF — All lectures in one place</p>
    <div class="hero-stats">
      <div class="stat-pill"><strong><?= $stats['total_batches'] ?? 12 ?></strong> Batches</div>
      <div class="stat-pill"><strong><?= number_format($stats['total_videos'] ?? 1025) ?></strong> Lectures</div>
      <div class="stat-pill"><strong>Free</strong> Access</div>
    </div>
  </div>
</section>

<!-- SEARCH -->
<div class="search-wrap" id="searchSection">
  <div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="batchSearch" placeholder="Search batches — CDS, NDA, AFCAT, CAPF…" autocomplete="off"/>
    <span class="search-clear" onclick="clearSearch()">✕</span>
  </div>
</div>

<!-- FILTER TABS -->
<div class="filter-bar">
  <button class="filter-btn active" data-filter="all">All Batches</button>
  <button class="filter-btn" data-filter="spidyuniverse">🕷 SpidyUniverse</button>
  <button class="filter-btn" data-filter="cdsjourney">📚 CDS Journey</button>
</div>

<!-- BATCHES GRID -->
<main class="batches-wrap">
  <div class="batches-grid" id="batchesGrid">
    <?php if (!empty($batches['batches'])): ?>
      <?php foreach ($batches['batches'] as $batch): ?>
        <?php
          $tid   = Security::encryptId($batch['id']);
          $src   = $batch['source'] ?? 'cdsjourney';
          $label = strtolower($src);
          $srcLabel = $src === 'spidyuniverse' ? '🕷 SpidyUniverse' : '📚 CDS Journey';
          $exam  = '';
          $n = strtoupper($batch['name']);
          if (str_contains($n,'NDA')) $exam='NDA';
          elseif (str_contains($n,'AFCAT')) $exam='AFCAT';
          elseif (str_contains($n,'CAPF')) $exam='CAPF';
          elseif (str_contains($n,'CDS')) $exam='CDS';
          elseif (str_contains($n,'MATHS') || str_contains($n,'MATH')) $exam='MATHS';
        ?>
        <div class="batch-card" data-source="<?= htmlspecialchars($label) ?>" data-name="<?= htmlspecialchars(strtolower($batch['name'])) ?>">
          <div class="card-glow"></div>
          <div class="card-top">
            <?php if ($exam): ?><span class="exam-tag exam-<?= strtolower($exam) ?>"><?= $exam ?></span><?php endif; ?>
            <span class="src-tag"><?= $srcLabel ?></span>
          </div>
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($batch['name']) ?></h3>
            <div class="card-meta">
              <span class="meta-free"><i class="fas fa-unlock-alt"></i> Free Access</span>
            </div>
          </div>
          <a href="batch.php?t=<?= urlencode($tid) ?>" class="card-btn">
            View Subjects <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state"><i class="fas fa-spider"></i><p>No batches found</p></div>
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
<script src="assets/js/app.js"></script>
</body>
</html>
