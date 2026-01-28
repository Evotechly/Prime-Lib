<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('user');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));
$userId = (int)($authUser['id'] ?? 0);

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$loans = [];
$loanSql = "SELECT e.id_emprunt, e.date_emprunt, e.date_retour_prevue, e.date_retour_effective,
                   l.titre, l.auteur, l.genre, l.annee_publication, l.cover_url, l.id_livre
            FROM emprunt e
            JOIN livre l ON e.id_livre = l.id_livre
            WHERE e.id_utilisateur = ?
            ORDER BY e.date_emprunt DESC, e.id_emprunt DESC";
$stmt = $conn->prepare($loanSql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $loans[] = $row;
  }
}
$stmt->close();

$activeCount = 0;
$historyCount = 0;
$overdueCount = 0;
foreach ($loans as $loan) {
  if (!empty($loan['date_retour_effective'])) {
    $historyCount++;
  } else {
    $activeCount++;
    if (strtotime((string)$loan['date_retour_prevue']) < strtotime(date('Y-m-d'))) {
      $overdueCount++;
    }
  }
}

$flashSuccess = $_SESSION['reader_success'] ?? null;
$flashError = $_SESSION['reader_error'] ?? null;
unset($_SESSION['reader_success'], $_SESSION['reader_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib - My Loans</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body class="reader-body">
  <main class="reader-shell">
    <header class="reader-topbar">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text">
          <div class="brand-name">PRIME LIB</div>
        </div>
      </div>
      <div class="top-actions">
        <a class="pill" href="dashboard.php">Home</a>
        <a class="pill" href="catalogue.php">Catalogue</a>
        <span class="pill" style="cursor:default;"><?= e($authDisplayName !== '' ? $authDisplayName : 'Reader') ?></span>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="reader-hero compact single">
      <div class="reader-hero-content">
        <p class="reader-hero-tag">YOUR HISTORY</p>
        <h1>Track every loan.</h1>
        <p>Return books on time and keep your reading journey organized.</p>
      </div>
    </section>

    <?php if ($flashSuccess): ?>
      <div class="reader-flash success"><?= e((string)$flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="reader-flash error"><?= e((string)$flashError) ?></div>
    <?php endif; ?>

    <section class="reader-section">
      <div class="reader-stats wide">
        <div>
          <div class="stat-value"><?= e((string)$activeCount) ?></div>
          <div class="stat-label">Active loans</div>
        </div>
        <div>
          <div class="stat-value"><?= e((string)$overdueCount) ?></div>
          <div class="stat-label">Overdue</div>
        </div>
        <div>
          <div class="stat-value"><?= e((string)$historyCount) ?></div>
          <div class="stat-label">Completed</div>
        </div>
      </div>

      <div class="book-grid">
        <?php if (count($loans) === 0): ?>
          <div class="reader-empty">You have no loans yet. Browse the catalogue to get started.</div>
        <?php endif; ?>
        <?php foreach ($loans as $loan): ?>
          <?php
            $returned = !empty($loan['date_retour_effective']);
            $due = (string)$loan['date_retour_prevue'];
            $status = 'On time';
            $statusClass = 'available';
            if ($returned) {
              $status = 'Returned';
              $statusClass = 'available';
            } elseif (strtotime($due) < strtotime(date('Y-m-d'))) {
              $status = 'Overdue';
              $statusClass = 'unavailable';
            } elseif (strtotime($due) <= strtotime('+3 days')) {
              $status = 'Due soon';
              $statusClass = 'soon';
            }
            $cover = (string)($loan['cover_url'] ?? '');
            $coverSrc = $cover !== '' ? $cover : '../../Assets/images/book-placeholder.svg';
          ?>
          <article class="book-card">
            <img class="book-cover" src="<?= e($coverSrc) ?>" alt="<?= e((string)$loan['titre']) ?>" onerror="this.src='../../Assets/images/book-placeholder.svg';" />
            <div class="book-meta">
              <div class="book-title"><?= e((string)$loan['titre']) ?></div>
              <div class="book-author">by <?= e((string)$loan['auteur']) ?></div>
              <div class="book-tags">
                <span class="tag"><?= e((string)($loan['genre'] ?? 'General')) ?></span>
                <span class="tag soft"><?= e((string)($loan['annee_publication'] ?? '')) ?></span>
              </div>
              <div class="loan-dates">
                <span>Borrowed: <?= e((string)$loan['date_emprunt']) ?></span>
                <span>Due: <?= e($due) ?></span>
              </div>
              <span class="status <?= e($statusClass) ?>"><?= e($status) ?></span>
            </div>
            <div class="book-actions">
              <?php if (!$returned): ?>
                <form method="post" action="../../actions/reader/return.php">
                  <input type="hidden" name="loan_id" value="<?= e((string)$loan['id_emprunt']) ?>" />
                  <button class="primary" type="submit">Return</button>
                </form>
              <?php else: ?>
                <button class="pill" type="button" disabled>Completed</button>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
