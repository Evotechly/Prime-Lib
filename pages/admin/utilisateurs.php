<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

$flashSuccess = $_SESSION['admin_action_success'] ?? $_SESSION['user_action_success'] ?? null;
$flashError = $_SESSION['admin_action_error'] ?? $_SESSION['user_action_error'] ?? null;
unset(
  $_SESSION['admin_action_success'],
  $_SESSION['admin_action_error'],
  $_SESSION['user_action_success'],
  $_SESSION['user_action_error']
);

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

$adminCount = fetch_count($conn, 'SELECT COUNT(*) AS total FROM admin');
$userCount = fetch_count($conn, 'SELECT COUNT(*) AS total FROM utilisateur');
$total = $adminCount + $userCount;
$staff = $adminCount;
$active = $total;

$users = [];
$userSql = "SELECT id_admin AS id, CONCAT(prenom, ' ', nom) AS name, email, 'Admin' AS role, 'staff' AS role_key, 'admin' AS type, 1 AS role_order
            FROM admin
            UNION ALL
            SELECT id_utilisateur AS id, CONCAT(prenom, ' ', nom) AS name, email, 'Member' AS role, 'member' AS role_key, 'user' AS type, 2 AS role_order
            FROM utilisateur
            ORDER BY role_order, name";
$userResult = $conn->query($userSql);
if ($userResult) {
  while ($row = $userResult->fetch_assoc()) {
    $users[] = [
      'id' => (int)$row['id'],
      'name' => (string)$row['name'],
      'email' => (string)$row['email'],
      'role' => (string)$row['role'],
      'role_key' => (string)$row['role_key'],
      'type' => (string)$row['type'],
      'status_label' => 'Active',
      'status_class' => 'available',
    ];
  }
  $userResult->close();
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
          <a class="nav-item" href="analytics.php">
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
            <span class="icon"><img class="p11" src="../../Assets/icons/search.png"></span>
            <input type="text" id="user-search" placeholder="Search users" />
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
            <div class="action-menu">
              <button class="primary action-menu-button" type="button" aria-haspopup="true" aria-expanded="false">
                + Create
              </button>
              <div class="action-menu-list" role="menu">
                <a role="menuitem" href="add_user.php">New user</a>
                <a role="menuitem" href="add_admin.php">New admin</a>
              </div>
            </div>
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
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="users-table">
                <?php foreach ($users as $u): ?>
                  <tr data-role="<?= e($u['role_key']) ?>">
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['role']) ?></td>
                    <td>
                      <span class="status <?= e($u['status_class']) ?>">
                        <?= e($u['status_label']) ?>
                      </span>
                    </td>
                    <td>
                      <a class="table-action" href="edit_user.php?type=<?= e($u['type']) ?>&id=<?= e((string)$u['id']) ?>">Edit</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/utilisateurs.js"></script>
  </body>
</html>
