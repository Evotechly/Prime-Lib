<?php
$users = [
  [
    "name" => "Amina Benali",
    "email" => "amina.b@primelib.com",
    "role" => "Librarian",
    "status_label" => "Active",
    "status_class" => "available",
  ],
  [
    "name" => "Omar Khaled",
    "email" => "omar.k@primelib.com",
    "role" => "Assistant",
    "status_label" => "Active",
    "status_class" => "available",
  ],
  [
    "name" => "Sara Idris",
    "email" => "sara.i@primelib.com",
    "role" => "Member",
    "status_label" => "Inactive",
    "status_class" => "unavailable",
  ],
  [
    "name" => "Youssef Ali",
    "email" => "youssef.a@primelib.com",
    "role" => "Member",
    "status_label" => "Active",
    "status_class" => "available",
  ],
  [
    "name" => "Lina Noor",
    "email" => "lina.n@primelib.com",
    "role" => "Manager",
    "status_label" => "Active",
    "status_class" => "available",
  ],
  [
    "name" => "Hassan Rahim",
    "email" => "hassan.r@primelib.com",
    "role" => "Member",
    "status_label" => "Suspended",
    "status_class" => "soon",
  ],
];

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$total = count($users);
$active = 0;
$inactive = 0;
$staff = 0;

foreach ($users as $u) {
  if ($u["status_label"] === "Active") $active++;
  else $inactive++;
  if ($u["role"] !== "Member") $staff++;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Lib - Users</title>
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
          <a class="nav-item" href="emprunts.php">
            <img class="nav-icon" src="../../Assets/icons/case-round-svgrepo-com.svg" alt="" />
            Activities
          </a>
          <a class="nav-item active" href="utilisateurs.php">
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
            <span class="icon">Search</span>
            <input type="text" id="user-search" placeholder="Search users" />
          </div>
          <div class="topbar-right">
            <div class="language">
              <span class="flag">EN</span>
              English
              <span class="caret">v</span>
            </div>
            <div class="user">
              <div class="avatar">U</div>
              <div class="user-meta">
                <span class="name">User</span>
                <span class="role">Admin</span>
              </div>
              <span class="caret">v</span>
            </div>
          </div>
        </header>

        <section class="content">
          <div class="page-header">
            <div>
              <h1>Members</h1>
              <p class="subtitle">Manage staff accounts and patron access.</p>
            </div>
            <button class="primary">+ Add new user</button>
          </div>

          <div class="stat-grid">
            <article class="stat-card">
              <div>
                <p>Total Users</p>
                <h2><?= e((string)$total) ?></h2>
              </div>
              <div class="stat-icon blue">USERS</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Active</p>
                <h2><?= e((string)$active) ?></h2>
              </div>
              <div class="stat-icon green">OK</div>
            </article>
            <article class="stat-card">
              <div>
                <p>Staff</p>
                <h2><?= e((string)$staff) ?></h2>
              </div>
              <div class="stat-icon amber">TEAM</div>
            </article>
          </div>

          <div class="filters">
            <div class="filter-pill">Role</div>
            <button class="filter-button" data-filter="all">All</button>
            <button class="filter-button" data-filter="staff">Staff</button>
            <button class="filter-button" data-filter="member">Members</button>
            <button class="reset-button" type="button">Reset filters</button>
          </div>

          <div class="table-card">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="users-table">
                <?php foreach ($users as $u): ?>
                  <tr data-role="<?= e(strtolower($u["role"])) ?>">
                    <td><?= e($u["name"]) ?></td>
                    <td><?= e($u["email"]) ?></td>
                    <td><?= e($u["role"]) ?></td>
                    <td>
                      <span class="status <?= e($u["status_class"]) ?>">
                        <?= e($u["status_label"]) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="table-footer">
              <span>
                Showing <?= e((string)count($users)) ?> of <?= e((string)count($users)) ?> users
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
    <script src="../../Assets/js/utilisateurs.js"></script>
  </body>
</html>


