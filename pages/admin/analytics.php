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
$activeLoans = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_retour_effective IS NULL");
$overdueLoans = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_retour_effective IS NULL AND date_retour_prevue < CURDATE()");
$borrowedThisMonth = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_emprunt >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");
$returnedThisMonth = fetch_count($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE date_retour_effective >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");

$monthKeys = [];
$monthLabels = [];
$start = new DateTime('first day of this month');
for ($i = 11; $i >= 0; $i--) {
  $dt = (clone $start)->modify("-{$i} months");
  $monthKeys[] = $dt->format('Y-m');
  $monthLabels[] = $dt->format('M');
}

$borrowSeries = array_fill_keys($monthKeys, 0);
$returnSeries = array_fill_keys($monthKeys, 0);

$borrowSql = "SELECT DATE_FORMAT(date_emprunt, '%Y-%m') AS ym, COUNT(*) AS total
              FROM emprunt
              WHERE date_emprunt >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
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
                AND date_retour_effective >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
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

$lineBorrow = build_line_points($borrowValues, 40, 500, 30, 200, $maxSeriesValue);
$lineReturn = build_line_points($returnValues, 40, 500, 30, 200, $maxSeriesValue);

$genres = [];
$genreSql = "SELECT COALESCE(NULLIF(TRIM(genre), ''), 'Uncategorized') AS label, COUNT(*) AS total
             FROM livre
             GROUP BY label
             ORDER BY total DESC
             LIMIT 6";
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
$maxGenre = 0;
foreach ($genres as $genre) {
  $maxGenre = max($maxGenre, $genre['total']);
}

$statusCounts = [
  'disponible' => fetch_count($conn, "SELECT COUNT(*) AS total FROM livre WHERE etat_disponibilite = 'disponible'"),
  'emprunte' => fetch_count($conn, "SELECT COUNT(*) AS total FROM livre WHERE etat_disponibilite = 'emprunte'"),
  'perdu' => fetch_count($conn, "SELECT COUNT(*) AS total FROM livre WHERE etat_disponibilite = 'perdu'"),
];
$statusTotal = array_sum($statusCounts);
$statusTotal = $statusTotal > 0 ? $statusTotal : 1;

$topBooks = [];
$topSql = "SELECT livre.titre, livre.auteur, COUNT(*) AS total
           FROM emprunt
           JOIN livre ON emprunt.id_livre = livre.id_livre
           GROUP BY emprunt.id_livre
           ORDER BY total DESC
           LIMIT 5";
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

$radius = 54;
$circumference = 2 * pi() * $radius;
$offset = 0.0;
$statusSegments = [
  ['label' => 'Available', 'value' => $statusCounts['disponible'], 'class' => 'seg-available'],
  ['label' => 'On loan', 'value' => $statusCounts['emprunte'], 'class' => 'seg-onloan'],
  ['label' => 'Lost', 'value' => $statusCounts['perdu'], 'class' => 'seg-lost'],
];
foreach ($statusSegments as $index => $segment) {
  $length = ($segment['value'] / $statusTotal) * $circumference;
  $statusSegments[$index]['length'] = $length;
  $statusSegments[$index]['offset'] = $offset;
  $offset += $length;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Prime Lib - Analytics</title>
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
          <a class="nav-item" href="dashboard.php">
            <img class="nav-icon" src="../../Assets/icons/home.svg" alt="" />
            Dashboard
          </a>
          <a class="nav-item" href="livres.php">
            <img class="nav-icon" src="../../Assets/icons/book.svg" alt="" />
            Books
          </a>
          <a class="nav-item active" href="analytics.php">
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
            <span class="search-icon">Search</span>
            <input type="text" placeholder="Search analytics" />
          </div>
          <div class="top-actions">
            <button class="pill">
              <span class="flag">EN</span>
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
          <h1>Analytics</h1>

          <div class="cards">
            <article class="stat-card">
              <div class="stat-head">
                <div>
                  <div class="stat-label">Total Members</div>
                  <div class="stat-value" data-count="<?= e((string)$totalMembers) ?>"><?= e((string)$totalMembers) ?></div>
                </div>
                <div class="stat-icon blue">MEM</div>
              </div>
              <div class="stat-foot">Borrowed this month: <?= e((string)$borrowedThisMonth) ?></div>
            </article>

            <article class="stat-card">
              <div class="stat-head">
                <div>
                  <div class="stat-label">Total Books</div>
                  <div class="stat-value" data-count="<?= e((string)$totalBooks) ?>"><?= e((string)$totalBooks) ?></div>
                </div>
                <div class="stat-icon amber">BOOK</div>
              </div>
              <div class="stat-foot">Returned this month: <?= e((string)$returnedThisMonth) ?></div>
            </article>

            <article class="stat-card">
              <div class="stat-head">
                <div>
                  <div class="stat-label">Active Loans</div>
                  <div class="stat-value" data-count="<?= e((string)$activeLoans) ?>"><?= e((string)$activeLoans) ?></div>
                </div>
                <div class="stat-icon green">TIME</div>
              </div>
              <div class="stat-foot">Overdue: <?= e((string)$overdueLoans) ?></div>
            </article>

            <article class="stat-card">
              <div class="stat-head">
                <div>
                  <div class="stat-label">On-Time Returns</div>
                  <div class="stat-value"><?= $activeLoans > 0 ? e((string)max(0, $activeLoans - $overdueLoans)) : '0' ?></div>
                </div>
                <div class="stat-icon peach">SAFE</div>
              </div>
              <div class="stat-foot">Tracking last 12 months</div>
            </article>
          </div>

          <div class="grid">
            <article class="panel">
              <div class="panel-title">Borrow vs Return (Last 12 months)</div>
              <div class="chart-legend">
                <span><span class="dot blue"></span>Borrowed</span>
                <span><span class="dot green"></span>Returned</span>
              </div>
              <div class="chart">
                <svg viewBox="0 0 520 230" aria-hidden="true">
                  <g class="grid-lines">
                    <line x1="40" y1="30" x2="500" y2="30" />
                    <line x1="40" y1="80" x2="500" y2="80" />
                    <line x1="40" y1="130" x2="500" y2="130" />
                    <line x1="40" y1="180" x2="500" y2="180" />
                  </g>
                  <polyline class="line blue" points="<?= e($lineBorrow) ?>" />
                  <polyline class="line green" points="<?= e($lineReturn) ?>" />
                  <g class="axis">
                    <?php foreach ($monthLabels as $idx => $label): ?>
                      <?php $x = 40 + (($idx) * (460 / (count($monthLabels) - 1))); ?>
                      <text x="<?= e(sprintf('%.1f', $x)) ?>" y="210"><?= e($label) ?></text>
                    <?php endforeach; ?>
                  </g>
                </svg>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Book Status Mix</div>
              <div class="category-panel">
                <ul class="legend">
                  <li><span class="dot gold"></span>Available: <?= e((string)$statusCounts['disponible']) ?></li>
                  <li><span class="dot blue"></span>On loan: <?= e((string)$statusCounts['emprunte']) ?></li>
                  <li><span class="dot teal"></span>Lost: <?= e((string)$statusCounts['perdu']) ?></li>
                </ul>
                <div class="donut small">
                  <svg viewBox="0 0 140 140" aria-hidden="true">
                    <circle cx="70" cy="70" r="<?= e((string)$radius) ?>" class="ring" />
                    <?php foreach ($statusSegments as $segment): ?>
                      <circle
                        cx="70"
                        cy="70"
                        r="<?= e((string)$radius) ?>"
                        class="ring-seg <?= e($segment['class']) ?>"
                        style="stroke-dasharray: <?= e(sprintf('%.2f %.2f', $segment['length'], $circumference - $segment['length'])) ?>; stroke-dashoffset: <?= e(sprintf('%.2f', -$segment['offset'])) ?>;"
                      />
                    <?php endforeach; ?>
                  </svg>
                </div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Genres With Most Titles</div>
              <div class="bar-chart">
                <?php if (count($genres) === 0): ?>
                  <div class="chart-empty">No genres yet. Add books to see the distribution.</div>
                <?php endif; ?>
                <?php foreach ($genres as $index => $genre): ?>
                  <?php $percent = $maxGenre > 0 ? ($genre['total'] / $maxGenre) * 100 : 0; ?>
                  <div class="bar-row">
                    <div class="bar-label"><?= e($genre['label']) ?></div>
                    <div class="bar">
                      <span class="bar-fill<?= $index === 1 ? ' alt' : '' ?>" style="width: <?= e(sprintf('%.1f', $percent)) ?>%"></span>
                    </div>
                    <div class="bar-value"><?= e((string)$genre['total']) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Most Borrowed Books</div>
              <ol class="book-list">
                <?php if (count($topBooks) === 0): ?>
                  <li>No loan data yet.</li>
                <?php endif; ?>
                <?php foreach ($topBooks as $index => $book): ?>
                  <li>
                    <span class="rank <?= $index === 0 ? 'gold' : ($index === 1 ? 'silver' : 'bronze') ?>"><?= e((string)($index + 1)) ?></span>
                    <div>
                      <div class="book-name"><?= e($book['title']) ?></div>
                      <div class="book-author">by <?= e($book['author']) ?> · <?= e((string)$book['total']) ?> loans</div>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ol>
            </article>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/script.js"></script>
  </body>
</html>
