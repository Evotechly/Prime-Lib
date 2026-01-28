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

function fetch_count(mysqli $conn, string $sql, int $userId = 0): int {
  if ($userId > 0) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    return (int)($row['total'] ?? 0);
  }
  $result = $conn->query($sql);
  if (!$result) {
    return 0;
  }
  $row = $result->fetch_assoc();
  $result->close();
  return (int)($row['total'] ?? 0);
}

$totalBooks = fetch_count($conn, 'SELECT COUNT(*) AS total FROM livre');
$availableBooks = fetch_count($conn, "SELECT COUNT(*) AS total FROM livre WHERE etat_disponibilite = 'disponible'");
$activeLoans = fetch_count($conn, 'SELECT COUNT(*) AS total FROM emprunt WHERE id_utilisateur = ? AND date_retour_effective IS NULL', $userId);

$activeLoanMap = [];
$stmt = $conn->prepare('SELECT id_emprunt, id_livre FROM emprunt WHERE id_utilisateur = ? AND date_retour_effective IS NULL');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $activeLoanMap[(int)$row['id_livre']] = (int)$row['id_emprunt'];
  }
}
$stmt->close();

$books = [];
$bookSql = 'SELECT id_livre, titre, auteur, genre, annee_publication, etat_disponibilite, cover_url FROM livre ORDER BY id_livre DESC LIMIT 6';
$bookResult = $conn->query($bookSql);
if ($bookResult) {
  while ($row = $bookResult->fetch_assoc()) {
    $books[] = $row;
  }
  $bookResult->close();
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
  <title>Prime Lib - Reader Home</title>
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
        <a class="pill" href="catalogue.php">Catalogue</a>
        <a class="pill" href="mes_emprunts.php">History</a>
        <span class="pill" style="cursor:default;"><?= e($authDisplayName !== '' ? $authDisplayName : 'Reader') ?></span>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="reader-hero">
      <div class="reader-hero-content">
        <p class="reader-hero-tag">PRIME LIB PICKS</p>
        <h1>Read more, wait less.</h1>
        <p>Discover fresh arrivals, borrow instantly, and keep track of every loan in one place.</p>
        <div class="reader-hero-actions">
          <a class="primary" href="catalogue.php">Browse catalogue</a>
          <a class="pill" href="mes_emprunts.php">View history</a>
        </div>
        <div class="reader-stats">
          <div>
            <div class="stat-value"><?= e((string)$totalBooks) ?></div>
            <div class="stat-label">Total books</div>
          </div>
          <div>
            <div class="stat-value"><?= e((string)$availableBooks) ?></div>
            <div class="stat-label">Available now</div>
          </div>
          <div>
            <div class="stat-value"><?= e((string)$activeLoans) ?></div>
            <div class="stat-label">Your active loans</div>
          </div>
        </div>
      </div>
      <div class="reader-hero-card">
        <div class="hero-card-title">Today’s marketing spotlight</div>
        <div class="hero-card-body">
          <p>Borrow 2 books this week and unlock a 7-day bonus extension.</p>
          <p class="hero-card-sub">Ask your librarian to activate the bonus once you finish reading.</p>
        </div>
      </div>
    </section>

    <?php if ($flashSuccess): ?>
      <div class="reader-flash success"><?= e((string)$flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="reader-flash error"><?= e((string)$flashError) ?></div>
    <?php endif; ?>

    <section class="reader-section">
      <div class="reader-section-head">
        <div>
          <h2>Latest in the catalogue</h2>
          <p>Grab a title before it’s gone.</p>
        </div>
        <a class="pill" href="catalogue.php">See all</a>
      </div>
      <div class="book-grid">
        <?php foreach ($books as $book): ?>
          <?php
            $bookId = (int)$book['id_livre'];
            $status = (string)$book['etat_disponibilite'];
            $cover = (string)($book['cover_url'] ?? '');
            $coverSrc = $cover !== '' ? $cover : '../../Assets/images/book-placeholder.svg';
            $activeLoanId = $activeLoanMap[$bookId] ?? null;
          ?>
          <article class="book-card">
            <img class="book-cover" src="<?= e($coverSrc) ?>" alt="<?= e((string)$book['titre']) ?>" onerror="this.src='../../Assets/images/book-placeholder.svg';" />
            <div class="book-meta">
              <div class="book-title"><?= e((string)$book['titre']) ?></div>
              <div class="book-author">by <?= e((string)$book['auteur']) ?></div>
              <div class="book-tags">
                <span class="tag"><?= e((string)($book['genre'] ?? 'General')) ?></span>
                <span class="tag soft"><?= e((string)($book['annee_publication'] ?? '')) ?></span>
              </div>
            </div>
            <div class="book-actions">
              <?php if ($activeLoanId): ?>
                <form method="post" action="../../actions/reader/return.php">
                  <input type="hidden" name="loan_id" value="<?= e((string)$activeLoanId) ?>" />
                  <button class="primary" type="submit">Return</button>
                </form>
              <?php elseif ($status === 'disponible'): ?>
                <form method="post" action="../../actions/reader/borrow.php">
                  <input type="hidden" name="book_id" value="<?= e((string)$bookId) ?>" />
                  <button class="primary" type="submit">Borrow</button>
                </form>
              <?php else: ?>
                <button class="pill" type="button" disabled>On loan</button>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
