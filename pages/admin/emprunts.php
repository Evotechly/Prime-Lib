<?php
$activities = [
  [
    "member" => "Amina Benali",
    "book" => "The Silent Signal",
    "type" => "Borrowed",
    "due" => "2026-02-05",
    "status_label" => "Due soon",
    "status_class" => "soon",
  ],
  [
    "member" => "Omar Khaled",
    "book" => "Modern Algorithms",
    "type" => "Returned",
    "due" => "2026-01-20",
    "status_label" => "Returned",
    "status_class" => "available",
  ],
  [
    "member" => "Sara Idris",
    "book" => "Ocean Stories",
    "type" => "Overdue",
    "due" => "2026-01-12",
    "status_label" => "Overdue",
    "status_class" => "unavailable",
  ],
  [
    "member" => "Youssef Ali",
    "book" => "Math for Creators",
    "type" => "Borrowed",
    "due" => "2026-02-18",
    "status_label" => "On time",
    "status_class" => "available",
  ],
  [
    "member" => "Lina Noor",
    "book" => "Community Design",
    "type" => "Borrowed",
    "due" => "2026-02-02",
    "status_label" => "Due soon",
    "status_class" => "soon",
  ],
  [
    "member" => "Hassan Rahim",
    "book" => "The Quantum Path",
    "type" => "Overdue",
    "due" => "2026-01-09",
    "status_label" => "Overdue",
    "status_class" => "unavailable",
  ],
];

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$total = count($activities);
$dueSoon = 0;
$overdue = 0;
$returned = 0;

foreach ($activities as $a) {
  if ($a["status_label"] === "Overdue") $overdue++;
  else if ($a["status_label"] === "Returned") $returned++;
  else $dueSoon++;
}
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
          <a class="nav-item" href="dashboard.php">
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
                <span class="name">User</span>
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
                <h2><?= e((string)$total) ?></h2>
              </div>
              <div class="stat-icon blue">LOG</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Due Soon</p>
                <h2><?= e((string)$dueSoon) ?></h2>
              </div>
              <div class="stat-icon amber">SOON</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Overdue</p>
                <h2><?= e((string)$overdue) ?></h2>
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
                </tr>
              </thead>
              <tbody id="activity-table">
                <?php foreach ($activities as $a): ?>
                  <tr data-status="<?= e($a["status_class"]) ?>">
                    <td><?= e($a["member"]) ?></td>
                    <td><?= e($a["book"]) ?></td>
                    <td><?= e($a["type"]) ?></td>
                    <td><?= e($a["due"]) ?></td>
                    <td>
                      <span class="status <?= e($a["status_class"]) ?>">
                        <?= e($a["status_label"]) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="table-footer">
              <span>
                Showing <?= e((string)count($activities)) ?> of <?= e((string)count($activities)) ?> activities
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


