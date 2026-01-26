<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');
$authUser = auth_user();
$authDisplayName = trim(($authUser['prenom'] ?? '') . ' ' . ($authUser['nom'] ?? ''));
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib Dashboard</title>
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
        <a class="nav-item active" href="dashboard.php">
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
          <input type="text" placeholder="Search" />
        </div>
        <div class="top-actions">
          <button class="pill">
            <span class="flag">EN</span>
            English
          </button>
          <button class="user-pill">
            <span class="avatar">U</span>
            <div class="user-meta">
              <div class="user-name"><?= htmlspecialchars($authDisplayName !== "" ? $authDisplayName : "Admin", ENT_QUOTES, "UTF-8") ?></div>
              <div class="user-role">Admin</div>
            </div>
          </button>
        </div>
      </header>

      <section class="content">
        <h1>Dashboard</h1>

        <div class="cards">
          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Total Members</div>
                <div class="stat-value" data-count="176">176</div>
              </div>
              <div class="stat-icon blue">MEM</div>
            </div>
            <div class="stat-foot up">+ 8.5% Up from yesterday</div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Total Books</div>
                <div class="stat-value" data-count="10293">10,293</div>
              </div>
              <div class="stat-icon amber">BOOK</div>
            </div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Overdue Returns</div>
                <div class="stat-value" data-count="90">90</div>
              </div>
              <div class="stat-icon green">LATE</div>
            </div>
            <div class="stat-foot down">- 4.3% Down from yesterday</div>
          </article>

          <article class="stat-card">
            <div class="stat-head">
              <div>
                <div class="stat-label">Borrowed Books</div>
                <div class="stat-value" data-count="105">105</div>
              </div>
              <div class="stat-icon peach">TIME</div>
            </div>
            <div class="stat-foot up">+ 1.8% Up from yesterday</div>
          </article>
        </div>

        <div class="grid">
          <article class="panel">
            <div class="panel-title">Members</div>
            <div class="members-panel">
              <div class="donut">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                  <circle cx="60" cy="60" r="44" class="ring" />
                  <circle cx="60" cy="60" r="44" class="ring-progress" />
                  <circle cx="60" cy="16" r="5" class="dot" />
                  <circle cx="104" cy="60" r="5" class="dot" />
                  <circle cx="60" cy="104" r="5" class="dot" />
                  <circle cx="16" cy="60" r="5" class="dot" />
                </svg>
              </div>
              <div class="member-stats">
                <div>
                  <div class="member-value">26</div>
                  <div class="member-label"><span class="bullet blue"></span>New Members</div>
                </div>
                <div>
                  <div class="member-value">150</div>
                  <div class="member-label"><span class="bullet light"></span>Old Members</div>
                </div>
              </div>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Analytics</div>
            <div class="chart">
              <svg viewBox="0 0 420 220" aria-hidden="true">
                <g class="grid-lines">
                  <line x1="40" y1="30" x2="400" y2="30" />
                  <line x1="40" y1="80" x2="400" y2="80" />
                  <line x1="40" y1="130" x2="400" y2="130" />
                  <line x1="40" y1="180" x2="400" y2="180" />
                </g>
                <polyline class="line blue" points="40,160 100,90 160,70 220,110 280,120 340,90 400,40" />
                <polyline class="line green" points="40,190 100,120 160,100 220,140 280,150 340,120 400,70" />
                <g class="axis">
                  <text x="40" y="205">2015</text>
                  <text x="120" y="205">2016</text>
                  <text x="200" y="205">2017</text>
                  <text x="280" y="205">2018</text>
                  <text x="360" y="205">2019</text>
                </g>
              </svg>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Best Category</div>
            <div class="category-panel">
              <ul class="legend">
                <li><span class="dot gold"></span>Science</li>
                <li><span class="dot blue"></span>Computer Science &amp; IT</li>
                <li><span class="dot teal"></span>Mathematics</li>
              </ul>
              <div class="donut small">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                  <circle cx="60" cy="60" r="42" class="ring" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-gold" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-blue" />
                  <circle cx="60" cy="60" r="42" class="ring-seg seg-teal" />
                </svg>
              </div>
            </div>
          </article>

          <article class="panel">
            <div class="panel-title">Best Books</div>
            <ol class="book-list">
              <li>
                <span class="rank gold">1st</span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
              <li>
                <span class="rank silver">2nd</span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
              <li>
                <span class="rank bronze">3rd</span>
                <div>
                  <div class="book-name">Book Name</div>
                  <div class="book-author">by Author</div>
                </div>
              </li>
            </ol>
          </article>
        </div>
      </section>
    </main>
  </div>

  <script src="../../Assets/js/script.js"></script>
</body>
</html>



