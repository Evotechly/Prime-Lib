<?php
$settings = [
  "library_name" => "Prime Lib Central",
  "email" => "admin@primelib.com",
  "phone" => "+1 (555) 248-9021",
  "timezone" => "UTC-05:00",
  "language" => "English",
  "loan_days" => "14",
  "late_fee" => "$1.00",
];

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
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
            <img class="nav-icon" src="../../Assets/icons/homee.svg" alt="" />
            Dashboard
          </a>
          <a class="nav-item" href="livres.php">
            <img class="nav-icon" src="../../Assets/icons/books.svg" alt="" />
            Books
          </a>
          <a class="nav-item" href="dashboard.php">
            <img class="nav-icon" src="../../Assets/icons/analytic.svg" alt="" />
            Analytics
          </a>
          <a class="nav-item" href="emprunts.php">
            <img class="nav-icon" src="../../Assets/icons/activity.svg" alt="" />
            Activities
          </a>
          <a class="nav-item" href="utilisateurs.php">
            <img class="nav-icon" src="../../Assets/icons/member.svg" alt="" />
            Members
          </a>
        </nav>
        <div class="sidebar-footer">
          <a class="nav-item ghost active" href="settings.php">
            <img class="nav-icon" src="../../Assets/icons/cog-svgrepo-com.svg" alt="" />
            Settings
          </a>
          <a class="nav-item ghost" href="../../logout.php">
            <img class="nav-icon" src="../../Assets/icons/logoutt.svg" alt="" />
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
                <div class="book-author"><?= e($settings["library_name"]) ?></div>
                <div class="book-name" style="margin-top: 12px;">Contact email</div>
                <div class="book-author"><?= e($settings["email"]) ?></div>
                <div class="book-name" style="margin-top: 12px;">Phone</div>
                <div class="book-author"><?= e($settings["phone"]) ?></div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Regional preferences</div>
              <div class="book-list">
                <div class="book-name">Timezone</div>
                <div class="book-author"><?= e($settings["timezone"]) ?></div>
                <div class="book-name" style="margin-top: 12px;">Language</div>
                <div class="book-author"><?= e($settings["language"]) ?></div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Loan policy</div>
              <div class="book-list">
                <div class="book-name">Loan period</div>
                <div class="book-author"><?= e($settings["loan_days"]) ?> days</div>
                <div class="book-name" style="margin-top: 12px;">Late fee</div>
                <div class="book-author"><?= e($settings["late_fee"]) ?> per day</div>
              </div>
            </article>

            <article class="panel">
              <div class="panel-title">Notifications</div>
              <div class="book-list">
                <div class="book-name">Due reminders</div>
                <div class="book-author">Email + SMS enabled</div>
                <div class="book-name" style="margin-top: 12px;">Overdue alerts</div>
                <div class="book-author">Escalation after 3 days</div>
              </div>
            </article>
          </div>

          <div class="panel" style="margin-top: 18px;">
            <div class="panel-title">Security</div>
            <div class="book-list">
              <div class="book-name">Two-factor authentication</div>
              <div class="book-author">Required for staff accounts</div>
              <div class="book-name" style="margin-top: 12px;">Session timeout</div>
              <div class="book-author">30 minutes of inactivity</div>
            </div>
          </div>
        </section>
      </main>
    </div>

    <script src="../../Assets/js/script.js"></script>
  </body>
</html>

