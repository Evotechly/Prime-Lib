<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function fetch_count(mysqli $conn, string $sql): int {
  $result = $conn->query($sql);
  if (!$result) {
    return 0;
  }
  $row = $result->fetch_assoc();
  $result->close();
  return (int)($row['total'] ?? 0);
}

$totalMembers = fetch_count($conn, 'SELECT COUNT(*) AS total FROM utilisateur');
$totalBooks = fetch_count($conn, 'SELECT COUNT(*) AS total FROM livre');
$borrowedBooks = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_retour_effective IS NULL");
$overdueReturns = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_retour_effective IS NULL AND date_retour_prevue < CURDATE()");
$borrowedThisMonth = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_emprunt >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");

$activeMembers = fetch_count(
  $conn,
  "SELECT COUNT(DISTINCT id_utilisateur) AS total FROM emprunt WHERE date_emprunt >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
);
$inactiveMembers = max(0, $totalMembers - $activeMembers);

$ringRadius = 44;
$ringCircumference = 2 * pi() * $ringRadius;
$activeLength = $totalMembers > 0 ? ($activeMembers / $totalMembers) * $ringCircumference : 0;

$monthKeys = [];
$monthLabels = [];
$start = new DateTime('first day of this month');
for ($i = 4; $i >= 0; $i--) {
  $dt = (clone $start)->modify("-{$i} months");
  $monthKeys[] = $dt->format('Y-m');
  $monthLabels[] = $dt->format('M');
}

$borrowSeries = array_fill_keys($monthKeys, 0);
$returnSeries = array_fill_keys($monthKeys, 0);

$borrowSql = "SELECT DATE_FORMAT(date_emprunt, '%Y-%m') AS ym, COUNT(*) AS total
              FROM emprunt
              WHERE date_emprunt >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), '%Y-%m-01')
              GROUP BY ym
              ORDER BY ym";
$borrowResult = $conn->query($borrowSql);
if ($borrowResult) {
  while ($row = $borrowResult->fetch_assoc()) {
    $key = (string)$row['ym'];
    if (array_key_exists($key, $borrowSeries)) {
      $borrowSeries[$key] = (int)$row['total'];
    }
  }
  $borrowResult->close();
}

$returnSql = "SELECT DATE_FORMAT(date_retour_effective, '%Y-%m') AS ym, COUNT(*) AS total
              FROM emprunt
              WHERE date_retour_effective IS NOT NULL
                AND date_retour_effective >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), '%Y-%m-01')
              GROUP BY ym
              ORDER BY ym";
$returnResult = $conn->query($returnSql);
if ($returnResult) {
  while ($row = $returnResult->fetch_assoc()) {
    $key = (string)$row['ym'];
    if (array_key_exists($key, $returnSeries)) {
      $returnSeries[$key] = (int)$row['total'];
    }
  }
  $returnResult->close();
}

$borrowValues = array_values($borrowSeries);
$returnValues = array_values($returnSeries);
$maxSeriesValue = max(array_merge($borrowValues, $returnValues, [1]));

function build_line_points(array $values, int $x0, int $x1, int $y0, int $y1, int $maxValue): string {
  $count = count($values);
  if ($count <= 1) {
    return '';
  }
  $step = ($x1 - $x0) / ($count - 1);
  $points = [];
  foreach ($values as $index => $value) {
    $x = $x0 + ($step * $index);
    $ratio = $maxValue > 0 ? ($value / $maxValue) : 0;
    $y = $y1 - (($y1 - $y0) * $ratio);
    $points[] = sprintf('%.1f,%.1f', $x, $y);
  }
  return implode(' ', $points);
}

$lineBorrow = build_line_points($borrowValues, 40, 400, 30, 180, $maxSeriesValue);
$lineReturn = build_line_points($returnValues, 40, 400, 30, 180, $maxSeriesValue);

$genres = [];
$genreSql = "SELECT COALESCE(NULLIF(TRIM(genre), ''), 'Uncategorized') AS label, COUNT(*) AS total
             FROM livre
             GROUP BY label
             ORDER BY total DESC
             LIMIT 3";
$genreResult = $conn->query($genreSql);
if ($genreResult) {
  while ($row = $genreResult->fetch_assoc()) {
    $genres[] = [
      'label' => (string)$row['label'],
      'total' => (int)$row['total'],
    ];
  }
  $genreResult->close();
}
while (count($genres) < 3) {
  $genres[] = ['label' => 'Other', 'total' => 0];
}

$catRadius = 42;
$catCircumference = 2 * pi() * $catRadius;
$catTotal = array_sum(array_column($genres, 'total'));
$catTotal = $catTotal > 0 ? $catTotal : 1;
$catOffsets = [];
$offset = 0.0;
foreach ($genres as $genre) {
  $length = ($genre['total'] / $catTotal) * $catCircumference;
  $catOffsets[] = ['length' => $length, 'offset' => $offset];
  $offset += $length;
}

$topBooks = [];
$topSql = "SELECT livre.titre, livre.auteur, COUNT(*) AS total
           FROM emprunt
           JOIN livre ON emprunt.id_livre = livre.id_livre
           GROUP BY emprunt.id_livre
           ORDER BY total DESC
           LIMIT 3";
$topResult = $conn->query($topSql);
if ($topResult) {
  while ($row = $topResult->fetch_assoc()) {
    $topBooks[] = [
      'title' => (string)$row['titre'],
      'author' => (string)$row['auteur'],
      'total' => (int)$row['total'],
    ];
  }
  $topResult->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib Dashboard</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text">
          <div class="brand-name">PRIME LIB</div>
        </div>
      </div>

      <nav class="nav">
        <a class="nav-item active" href="dashboard.php">
          <img class="nav-icon" src="../../Assets/icons/home.svg" alt="" />
          Dashboard
        </a>
        <a class="nav-item" href="livres.php">
          <img class="nav-icon" src="../../Assets/icons/book.svg" alt="" />
          Books
        </a>
        <a class="nav-item" href="analytics.php">
          <img class="nav-icon" src="../../Assets/icons/chart-svgrepo-com.svg" alt="" />
          Analytics
        </a>
        <a class="nav-item" href="emprunts.php">
          <img class="nav-icon" src="../../Assets/icons/case-round-svgrepo-com.svg" alt="" />
          Activities
        </a>
        <a class="nav-item" href="utilisateurs.php">
          <img class="nav-icon" src="../../Assets/icons/user.svg" alt="" />
          Members
        </a>
      </nav>

      <div class="sidebar-footer">
        <a class="nav-item ghost" href="settings.php">
          <img class="nav-icon" src="../../Assets/icons/layers.svg" alt="" />
          Settings
        </a>
        <a class="nav-item ghost" href="../../logout.php">
          <img class="nav-icon" src="../../Assets/icons/logout.svg" alt="" />
          Logout
        </a>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="search">
          <span class="search-icon"><img class="p11" src="../../Assets/icons/search.png"></span>
          <input type="text" placeholder="Search" />
        </div>
        <div class="top-actions">
          <button class="pill">
            <span class="flag"><img class="p11" src="../../Assets/icons/language.png"></span>
            English
          </button>
          <button class="user-pill">
            <span class="avatar">U</span>
            <div class="user-meta">
              <div class="user-name"><?= e($authDisplayName !== '' ? $authDisplayName : 'Admin') ?></div>
              <div class="user-role">Admin</div>
            </div>
          </button>
        </div>
      </header>

      <section class="content">
        <h1>Dashboard</h1>

        <div class="cards">
          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Total Members</div>
                <div class="stat-value" data-count="<?= e((string)$totalMembers) ?>"><?= e((string)$totalMembers) ?></div>
              </div>
              <div class="stat-icon blue"><img src="../../Assets/icons/user.svg " alt=""></div>
            </div>
            <div class="stat-foot">Active (30 days): <?= e((string)$activeMembers) ?></div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Total Books</div>
                <div class="stat-value" data-count="348">348</div>
              </div>
              <div class="stat-icon amber"><img src="../../Assets/icons/book.png" alt=""></div>
            </div>
            <div class="stat-foot">On loan now: <?= e((string)$borrowedBooks) ?></div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Overdue Returns</div>
                <div class="stat-value" data-count="<?= e((string)$overdueReturns) ?>"><?= e((string)$overdueReturns) ?></div>
              </div>
              <div class="stat-icon green"><img src="../../Assets/icons/overdue.png" alt=""></div>
            </div>
            <div class="stat-foot">Past due loans</div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Borrowed This Month</div>
                <div class="stat-value" data-count="<?= e((string)$borrowedThisMonth) ?>"><?= e((string)$borrowedThisMonth) ?></div>
              </div>
              <div class="stat-icon peach"><img src="../../Assets/icons/borrowed.png"></div>
            </div>
            <div class="stat-foot">New loans this month</div>
          </article>
        </div>

        <div class="grid">
          <article class="panel">
            <div class="panel-title">Members</div>
            <br><br>
            <div class="members-panel">
              <div class="donut">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                  <circle cx="60" cy="60" r="44" class="ring" />
                  <circle cx="60" cy="60" r="44" class="ring-progress" />
                </svg>
                <br>
              </div>
              <div class="member-stats">
                <div>
                  <div class="member-value"><?= e((string)$activeMembers) ?></div>
                  <div class="member-label"><span class="bullet blue"></span>Active (30 days)</div>
                </div>
                <div>
                  <div class="member-value"><?= e((string)$inactiveMembers) ?></div>
                  <div class="member-label"><span class="bullet light"></span>Inactive</div>
                </div>
              </div>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Analytics</div>
            <div class="chart">
              <svg viewBox="0 0 420 220" aria-hidden="true">
                <g class="grid-lines">
                  <line x1="40" y1="30" x2="400" y2="30" />
                  <line x1="40" y1="80" x2="400" y2="80" />
                  <line x1="40" y1="130" x2="400" y2="130" />
                  <line x1="40" y1="180" x2="400" y2="180" />
                </g>
                <polyline class="line blue" points="<?= e($lineBorrow) ?>" />
                <polyline class="line green" points="<?= e($lineReturn) ?>" />
                <g class="axis">
                  <?php foreach ($monthLabels as $idx => $label): ?>
                    <?php $x = 40 + (($idx) * (360 / (count($monthLabels) - 1))); ?>
                    <text x="<?= e(sprintf('%.1f', $x)) ?>" y="205"><?= e($label) ?></text>
                  <?php endforeach; ?>
                </g>
              </svg>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Best Category</div>
            <div class="category-panel">
              <ul class="legend">
                <li><span class="dot gold"></span><?= e($genres[0]['label']) ?></li>
                <li><span class="dot blue"></span><?= e($genres[1]['label']) ?></li>
                <li><span class="dot teal"></span><?= e($genres[2]['label']) ?></li>
              </ul>
              <div class="donut small">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                  <circle cx="60" cy="60" r="42" class="ring" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-gold" style="stroke-dasharray: <?= e(sprintf('%.2f %.2f', $catOffsets[0]['length'], $catCircumference - $catOffsets[0]['length'])) ?>; stroke-dashoffset: <?= e(sprintf('%.2f', -$catOffsets[0]['offset'])) ?>;" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-blue" style="stroke-dasharray: <?= e(sprintf('%.2f %.2f', $catOffsets[1]['length'], $catCircumference - $catOffsets[1]['length'])) ?>; stroke-dashoffset: <?= e(sprintf('%.2f', -$catOffsets[1]['offset'])) ?>;" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-teal" style="stroke-dasharray: <?= e(sprintf('%.2f %.2f', $catOffsets[2]['length'], $catCircumference - $catOffsets[2]['length'])) ?>; stroke-dashoffset: <?= e(sprintf('%.2f', -$catOffsets[2]['offset'])) ?>;" />
                </svg>
              </div>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Best Books</div>
            <ol class="book-list">
              <li>
                <span class="rank gold"><img class="rank gold" src="../../Assets/icons/1st.png" alt="" /></span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
              <li>
                <span class="rank silver"><img class="rank silver" src="../../Assets/icons/2nd.png" alt="" /></span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
              <li>
                <span class="rank bronze"><img class="rank bronze" src="../../Assets/icons/3rd.png" alt="" /></span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
            </ol>
          </article>
        </div>
      </section>
    </main>
  </div>

  <script src="../../Assets/js/script.js"></script>
</body>
</html>
