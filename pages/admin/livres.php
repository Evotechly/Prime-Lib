<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));
$idAdmin = (int)($authUser['id'] ?? 0);

$flashSuccess = $_SESSION['book_action_success'] ?? null;
$flashError = $_SESSION['book_action_error'] ?? null;
unset($_SESSION['book_action_success'], $_SESSION['book_action_error']);

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$books = [];

$sql = 'SELECT id_livre, titre, auteur, genre, annee_publication, etat_disponibilite FROM livre ORDER BY id_livre DESC';
$result = $conn->query($sql);
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $etat = (string)($row['etat_disponibilite'] ?? 'disponible');
    $statusClass = match ($etat) {
      'disponible' => 'available',
      'emprunte' => 'soon',
      'perdu' => 'unavailable',
      default => 'available',
    };
    $statusLabel = match ($etat) {
      'disponible' => 'available',
      'emprunte' => 'on loan',
      'perdu' => 'lost',
      default => 'available',
    };

    $books[] = [
      'id' => (int)$row['id_livre'],
      'title' => (string)$row['titre'],
      'author' => (string)$row['auteur'],
      'category' => (string)($row['genre'] ?? ''),
      'year' => (string)($row['annee_publication'] ?? ''),
      'status' => $statusClass,
      'status_label' => $statusLabel,
    ];
  }
  $result->close();
}

$totalTitles = count($books);
$availableNow = 0;
$onLoan = 0;

foreach ($books as $b) {
  if ($b['status'] === 'available') {
    $availableNow++;
  } else {
    $onLoan++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Lib - Livres</title>
    <link rel="stylesheet" href="../../Assets/css/styles.css" />
  </head>
  <body>
    <div class="app">
      <aside class="sidebar">
        <div class="brand">
          <div class="brand-icon">P</div>
          <span>Prime Lib</span>
        </div>
        <nav class="nav">
          <a class="nav-item" href="dashboard.php">
            <img class="nav-icon" src="../../Assets/icons/home.svg" alt="" />
            Dashboard
          </a>
          <a class="nav-item active" href="livres.php">
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
          <a class="nav-item ghost" href="dashboard.php">
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
            <span class="icon"><img class="p11" src="../../Assets/icons/search.png"></span>
            <input
              type="text"
              id="book-search"
              placeholder="Search books or authors"
            />
          </div>
          <div class="topbar-right">
            <div class="language">
              <span class="flag"><img class="p11" src="../../Assets/icons/language.png"></span>
              English
              <span class="caret"></span>
            </div>
            <div class="user">
              <div class="avatar">U</div>
              <div class="user-meta">
                <span class="name"><?= e($authDisplayName !== '' ? $authDisplayName : 'Admin') ?></span>
                <span class="role">Admin</span>
              </div>
              <span class="caret"></span>
            </div>
          </div>
        </header>

        <section class="content">
          <div class="page-header">
            <div>
              <h1>Livres</h1>
              <p class="subtitle">Manage the library catalog and inventory.</p>
            </div>
            <a class="primary" href="add_livre.php" style="text-decoration:none;display:inline-block;">+ Add new book</a>
          </div>

          <?php if ($flashSuccess): ?>
            <div role="status" style="margin:8px 0 12px;color:#027a48;background:#ecfdf3;padding:10px 12px;border-radius:8px;">
              <?= e((string)$flashSuccess) ?>
            </div>
          <?php endif; ?>
          <?php if ($flashError): ?>
            <div role="alert" style="margin:8px 0 12px;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
              <?= e((string)$flashError) ?>
            </div>
          <?php endif; ?>

          <div class="stat-grid">
            <article class="stat-card">
              <div>
                <p>Total Titles</p>
                <h2><?= e((string)$totalTitles) ?></h2>
              </div>
              <div class="stat-icon purple">TITLES</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Available Now</p>
                <h2><?= e((string)$availableNow) ?></h2>
              </div>
              <div class="stat-icon mint">OK</div>
            </article>
            <article class="stat-card">
              <div>
                <p>On Loan / Out</p>
                <h2><?= e((string)$onLoan) ?></h2>
              </div>
              <div class="stat-icon orange">LOANS</div>
            </article>
          </div>

          <div class="filters">
            <div class="filter-pill">Status</div>
            <button class="filter-button" data-filter="all">All</button>
            <button class="filter-button" data-filter="available">Available</button>
            <button class="filter-button" data-filter="soon">On Loan</button>
            <button class="filter-button" data-filter="unavailable">Lost</button>
            <button class="reset-button" type="button">Reset filters</button>
          </div>

          <div class="table-card">
            <table>
              <thead>
                <tr>
                  <th>Book</th>
                  <th>Author</th>
                  <th>Genre</th>
                  <th>Year</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="books-table">
                <?php if (count($books) === 0): ?>
                  <tr>
                    <td colspan="6" style="text-align:center;padding:16px;">No books yet. Add your first book.</td>
                  </tr>
                <?php endif; ?>
                <?php foreach ($books as $b): ?>
                  <tr data-status="<?= e($b['status']) ?>">
                    <td><?= e($b['title']) ?></td>
                    <td><?= e($b['author']) ?></td>
                    <td><?= e($b['category']) ?></td>
                    <td><?= e($b['year']) ?></td>
                    <td>
                      <span class="status <?= e($b['status']) ?>">
                        <?= e($b['status_label']) ?>
                      </span>
                    </td>
                    <td>
                      <a class="table-action" href="edit_livre.php?id=<?= e((string)$b['id']) ?>">Edit</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/livres.js"></script>
  </body>
</html>
