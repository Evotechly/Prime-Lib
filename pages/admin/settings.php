<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$settings = [
  'library_name' => 'Not set',
  'contact_email' => 'Not set',
  'contact_phone' => 'Not set',
  'timezone' => 'Not set',
  'language' => 'Not set',
  'loan_days' => '0',
  'late_fee' => '0.00',
  'due_reminders' => 'Not set',
  'overdue_alerts' => 'Not set',
  'two_factor' => 'Not set',
  'session_timeout' => 'Not set',
];

$settingsSql = 'SELECT library_name, contact_email, contact_phone, timezone, language, loan_days, late_fee, due_reminders, overdue_alerts, two_factor, session_timeout FROM settings ORDER BY id DESC LIMIT 1';
$settingsResult = $conn->query($settingsSql);
if ($settingsResult && $settingsResult->num_rows > 0) {
  $row = $settingsResult->fetch_assoc();
  if ($row) {
    $settings = array_merge($settings, $row);
  }
  $settingsResult->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Lib - Settings</title>
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
          <a class="nav-item" href="utilisateurs.php">
            <img class="nav-icon" src="../../Assets/icons/user.svg" alt="" />
            Members
          </a>
        </nav>
        <div class="sidebar-footer">
          <a class="nav-item ghost active" href="settings.php">
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
            <input type="text" placeholder="Search settings" />
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
              <h1>Settings</h1>
              <p class="subtitle">Manage your library profile, policies, and preferences.</p>
            </div>
            <button class="primary">Save changes</button>
          </div>

          <div class="grid">
            <article class="panel">
              <div class="panel-title">Library profile</div>
              <div class="book-list">
                <div class="book-name">Library name</div>
                <div class="book-author"><?= e((string)$settings['library_name']) ?></div>
                <div class="book-name" style="margin-top: 12px;">Contact email</div>
                <div class="book-author"><?= e((string)$settings['contact_email']) ?></div>
                <div class="book-name" style="margin-top: 12px;">Phone</div>
                <div class="book-author"><?= e((string)$settings['contact_phone']) ?></div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Regional preferences</div>
              <div class="book-list">
                <div class="book-name">Timezone</div>
                <div class="book-author"><?= e((string)$settings['timezone']) ?></div>
                <div class="book-name" style="margin-top: 12px;">Language</div>
                <div class="book-author"><?= e((string)$settings['language']) ?></div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Loan policy</div>
              <div class="book-list">
                <div class="book-name">Loan period</div>
                <div class="book-author"><?= e((string)$settings['loan_days']) ?> days</div>
                <div class="book-name" style="margin-top: 12px;">Late fee</div>
                <div class="book-author">$<?= e(number_format((float)$settings['late_fee'], 2)) ?> per day</div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Notifications</div>
              <div class="book-list">
                <div class="book-name">Due reminders</div>
                <div class="book-author"><?= e((string)$settings['due_reminders']) ?></div>
                <div class="book-name" style="margin-top: 12px;">Overdue alerts</div>
                <div class="book-author"><?= e((string)$settings['overdue_alerts']) ?></div>
              </div>
            </article>
          </div>

          <div class="panel" style="margin-top: 18px;">
            <div class="panel-title">Security</div>
            <div class="book-list">
              <div class="book-name">Two-factor authentication</div>
              <div class="book-author"><?= e((string)$settings['two_factor']) ?></div>
              <div class="book-name" style="margin-top: 12px;">Session timeout</div>
              <div class="book-author"><?= e((string)$settings['session_timeout']) ?></div>
            </div>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/script.js"></script>
  </body>
</html>
