<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
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

$totalActivities = fetch_count($conn, 'SELECT COUNT(*) AS total FROM emprunt');
$dueSoonCount = fetch_count(
  $conn,
  "SELECT COUNT(*) AS total FROM emprunt
   WHERE date_retour_effective IS NULL
     AND date_retour_prevue >= CURDATE()
     AND date_retour_prevue <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)"
);
$overdueCount = fetch_count(
  $conn,
  "SELECT COUNT(*) AS total FROM emprunt
   WHERE date_retour_effective IS NULL
     AND date_retour_prevue < CURDATE()"
);

$activities = [];
$activitySql = "SELECT e.date_emprunt, e.date_retour_prevue, e.date_retour_effective,
                       e.id_emprunt, u.prenom, u.nom, l.titre
                FROM emprunt e
                JOIN utilisateur u ON e.id_utilisateur = u.id_utilisateur
                JOIN livre l ON e.id_livre = l.id_livre
                ORDER BY e.date_emprunt DESC, e.id_emprunt DESC
                LIMIT 50";
$activityResult = $conn->query($activitySql);
if ($activityResult) {
  while ($row = $activityResult->fetch_assoc()) {
    $due = (string)$row['date_retour_prevue'];
    $returned = $row['date_retour_effective'];
    $statusLabel = 'On time';
    $statusClass = 'available';
    $type = 'Borrowed';

    if (!empty($returned)) {
      $statusLabel = 'Returned';
      $statusClass = 'available';
      $type = 'Returned';
    } else {
      $dueTs = strtotime($due);
      $nowTs = strtotime(date('Y-m-d'));
      $soonTs = strtotime('+3 days', $nowTs);
      if ($dueTs < $nowTs) {
        $statusLabel = 'Overdue';
        $statusClass = 'unavailable';
        $type = 'Overdue';
      } elseif ($dueTs <= $soonTs) {
        $statusLabel = 'Due soon';
        $statusClass = 'soon';
        $type = 'Borrowed';
      }
    }

    $activities[] = [
      'id' => (int)$row['id_emprunt'],
      'member' => trim((string)$row['prenom'] . ' ' . (string)$row['nom']),
      'book' => (string)$row['titre'],
      'type' => $type,
      'due' => $due,
      'status_label' => $statusLabel,
      'status_class' => $statusClass,
    ];
  }
  $activityResult->close();
}

$shownCount = count($activities);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Lib - Activities</title>
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
          <a class="nav-item" href="livres.php">
            <img class="nav-icon" src="../../Assets/icons/book.svg" alt="" />
            Books
          </a>
          <a class="nav-item" href="analytics.php">
            <img class="nav-icon" src="../../Assets/icons/chart-svgrepo-com.svg" alt="" />
            Analytics
          </a>
          <a class="nav-item active" href="emprunts.php">
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
            <input type="text" id="activity-search" placeholder="Search member or book" />
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
              <h1>Activities</h1>
              <p class="subtitle">Track borrowing, returns, and overdue activity.</p>
            </div>
            <button class="primary">+ New activity</button>
          </div>

          <div class="stat-grid">
            <article class="stat-card">
              <div>
                <p>Total Activities</p>
                <h2><?= e((string)$totalActivities) ?></h2>
              </div>
              <div class="stat-icon blue">LOG</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Due Soon</p>
                <h2><?= e((string)$dueSoonCount) ?></h2>
              </div>
              <div class="stat-icon amber">SOON</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Overdue</p>
                <h2><?= e((string)$overdueCount) ?></h2>
              </div>
              <div class="stat-icon peach">LATE</div>
            </article>
          </div>

          <div class="filters">
            <div class="filter-pill">Status</div>
            <button class="filter-button" data-filter="all">All</button>
            <button class="filter-button" data-filter="soon">Due soon</button>
            <button class="filter-button" data-filter="available">On time</button>
            <button class="filter-button" data-filter="unavailable">Overdue</button>
            <button class="reset-button" type="button">Reset filters</button>
          </div>

          <div class="table-card">
            <table>
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Book</th>
                  <th>Type</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="activity-table">
                <?php if ($shownCount === 0): ?>
                  <tr>
                    <td colspan="6" style="text-align:center;padding:16px;">No activity yet.</td>
                  </tr>
                <?php endif; ?>
                <?php foreach ($activities as $a): ?>
                  <tr data-status="<?= e($a['status_class']) ?>">
                    <td><?= e($a['member']) ?></td>
                    <td><?= e($a['book']) ?></td>
                    <td><?= e($a['type']) ?></td>
                    <td><?= e($a['due']) ?></td>
                    <td>
                      <span class="status <?= e($a['status_class']) ?>">
                        <?= e($a['status_label']) ?>
                      </span>
                    </td>
                    <td>
                      <a class="table-action" href="edit_emprunt.php?id=<?= e((string)$a['id']) ?>">Edit</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="table-footer">
              <span>
                Showing <?= e((string)$shownCount) ?> of <?= e((string)$totalActivities) ?> activities
              </span>
              <div class="pager">
                <button class="page-btn" type="button">1</button>
                <button class="page-btn" type="button">2</button>
                <button class="page-btn" type="button">3</button>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/script.js"></script>
    <script src="../../Assets/js/emprunts.js"></script>
  </body>
</html>
