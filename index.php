<?php
require_once __DIR__ . '/includes/auth.php';
if (auth_role() === 'admin') {
    redirect('/primelib/pages/admin/dashboard.php');
}
if (auth_role() === 'user') {
    redirect('/primelib/pages/reader/dashboard.php');
}
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$publicBooks = [];
$bookResult = $conn->query('SELECT id_livre, titre, auteur, genre, annee_publication, etat_disponibilite, cover_url FROM livre ORDER BY id_livre DESC LIMIT 8');
if ($bookResult) {
    while ($row = $bookResult->fetch_assoc()) {
        $publicBooks[] = $row;
    }
    $bookResult->close();
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib</title>
  <link rel="stylesheet" href="Assets/css/styles.css" />
</head>
<body>
  <main class="main">
    <header class="topbar">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text">
          <div class="brand-name">PRIME LIB</div>
        </div>
      </div>
      <div class="top-actions">
        <a class="pill" href="login.php">Sign in</a>
        <a class="pill" href="signup.php">Get started</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">Library management platform</div>
        <h1>Run your library with clarity, speed, and control.</h1>
        <div class="user-role">Track members, lending, and inventory in one place with real-time insights.</div>
        <div class="top-actions" style="margin-top: 16px;">
          <a class="pill" href="signup.php">Request a demo</a>
          <a class="pill" href="signup.php">Explore features</a>
        </div>
      </div>

      <div class="cards" style="margin-top: 18px;">
        <article class="stat-card">
          <div class="stat-head">
            <div>
              <div class="stat-label">Libraries onboarded</div>
              <div class="stat-value" data-count="120">120</div>
            </div>
            <div class="stat-icon blue">LIB</div>
          </div>
          <div class="stat-foot up">+ 12% growth this quarter</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <div>
              <div class="stat-label">Books managed</div>
              <div class="stat-value" data-count="240000">240,000</div>
            </div>
            <div class="stat-icon amber">BOOK</div>
          </div>
          <div class="stat-foot">Centralized catalog and tracking</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <div>
              <div class="stat-label">Active members</div>
              <div class="stat-value" data-count="78000">78,000</div>
            </div>
            <div class="stat-icon green">MEM</div>
          </div>
          <div class="stat-foot">Smart renewals and alerts</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <div>
              <div class="stat-label">Avg. time saved</div>
              <div class="stat-value" data-count="42">42</div>
            </div>
            <div class="stat-icon peach">TIME</div>
          </div>
          <div class="stat-foot up">+ Hours per week</div>
        </article>
      </div>

      <div style="margin-top: 28px;">
        <h2>Catalogue highlights</h2>
        <div class="stat-label" style="margin-top: 6px;">A sneak peek at what readers can borrow instantly.</div>
        <div class="book-grid" style="margin-top: 16px;">
          <?php if (count($publicBooks) === 0): ?>
            <div class="reader-empty">No books available yet. Add books in the admin panel.</div>
          <?php endif; ?>
          <?php foreach ($publicBooks as $book): ?>
            <?php
              $cover = (string)($book['cover_url'] ?? '');
              $coverSrc = $cover !== '' ? $cover : 'Assets/images/book-placeholder.svg';
              $status = (string)$book['etat_disponibilite'];
            ?>
            <article class="book-card">
              <img class="book-cover" src="<?= e($coverSrc) ?>" alt="<?= e((string)$book['titre']) ?>" onerror="this.src='Assets/images/book-placeholder.svg';" />
              <div class="book-meta">
                <div class="book-title"><?= e((string)$book['titre']) ?></div>
                <div class="book-author">by <?= e((string)$book['auteur']) ?></div>
                <div class="book-tags">
                  <span class="tag"><?= e((string)($book['genre'] ?? 'General')) ?></span>
                  <span class="tag soft"><?= e((string)($book['annee_publication'] ?? '')) ?></span>
                </div>
                <span class="status <?= $status === 'disponible' ? 'available' : ($status === 'perdu' ? 'unavailable' : 'soon') ?>">
                  <?= $status === 'disponible' ? 'Available' : ($status === 'perdu' ? 'Lost' : 'On loan') ?>
                </span>
              </div>
              <div class="book-actions">
                <a class="primary" href="login.php">Sign in to borrow</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="grid" style="margin-top: 18px;">
        <article class="panel">
          <div class="panel-title">Everything your librarians need</div>
          <div class="book-list">
            <div class="book-name">Member profiles &amp; access control</div>
            <div class="book-author">Secure roles, card issuance, and history in one view.</div>
            <div class="book-name" style="margin-top: 12px;">Inventory &amp; circulation</div>
            <div class="book-author">Scan, lend, and return with fast workflows.</div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-title">Decision-ready analytics</div>
          <div class="chart">
            <svg viewBox="0 0 420 220" aria-hidden="true">
              <g class="grid-lines">
                <line x1="40" y1="30" x2="400" y2="30" />
                <line x1="40" y1="80" x2="400" y2="80" />
                <line x1="40" y1="130" x2="400" y2="130" />
                <line x1="40" y1="180" x2="400" y2="180" />
              </g>
              <polyline class="line blue" points="40,160 100,120 160,90 220,110 280,90 340,70 400,40" />
              <polyline class="line green" points="40,190 100,150 160,120 220,150 280,130 340,110 400,80" />
              <g class="axis">
                <text x="40" y="205">Week 1</text>
                <text x="140" y="205">Week 2</text>
                <text x="240" y="205">Week 3</text>
                <text x="340" y="205">Week 4</text>
              </g>
            </svg>
          </div>
          <div class="stat-label">Track circulation trends and category performance.</div>
        </article>
      </div>

      <div style="margin-top: 28px;">
        <h2>Why libraries choose Prime Lib</h2>
        <div class="grid" style="margin-top: 14px;">
          <article class="panel">
            <div class="panel-title">Fast onboarding</div>
            <div class="book-author">Import your catalog, members, and circulation history in days, not weeks.</div>
          </article>
          <article class="panel">
            <div class="panel-title">Automated workflows</div>
            <div class="book-author">Reminders, overdue alerts, and reporting run automatically.</div>
          </article>
          <article class="panel">
            <div class="panel-title">Secure by design</div>
            <div class="book-author">Role-based access keeps staff, patrons, and data protected.</div>
          </article>
          <article class="panel">
            <div class="panel-title">Community ready</div>
            <div class="book-author">Event management, room bookings, and program attendance in one place.</div>
          </article>
        </div>
      </div>

      <div style="margin-top: 28px;">
        <h2>Plans that scale</h2>
        <div class="cards" style="margin-top: 14px;">
          <article class="stat-card">
            <div class="stat-label">Starter</div>
            <div class="stat-value">Free</div>
            <div class="stat-foot">Up to 1,000 books and 300 members.</div>
          </article>
          <article class="stat-card">
            <div class="stat-label">Professional</div>
            <div class="stat-value">$49 / mo</div>
            <div class="stat-foot">Unlimited catalog with analytics and alerts.</div>
          </article>
          <article class="stat-card">
            <div class="stat-label">Enterprise</div>
            <div class="stat-value">Custom</div>
            <div class="stat-foot">Multi-branch support and dedicated success.</div>
          </article>
          <article class="stat-card">
            <div class="stat-label">City Network</div>
            <div class="stat-value">Custom</div>
            <div class="stat-foot">Shared resources across departments.</div>
          </article>
        </div>
      </div>

      <div style="margin-top: 28px;">
        <h2>Stories from librarians</h2>
        <div class="grid" style="margin-top: 14px;">
          <article class="panel">
            <div class="panel-title">"Our circulation jumped 18%."</div>
            <div class="book-author">We reclaimed staff time and improved patron satisfaction.</div>
            <div class="stat-label">Riverside Public Library</div>
          </article>
          <article class="panel">
            <div class="panel-title">"Reporting is finally effortless."</div>
            <div class="book-author">We can track popular genres and plan acquisitions faster.</div>
            <div class="stat-label">Eastwood Learning Center</div>
          </article>
        </div>
      </div>

      <div style="margin-top: 28px;">
        <h2>Frequently asked</h2>
        <div class="panel" style="margin-top: 14px;">
          <div class="book-list">
            <div class="book-name">Can we import our existing catalog?</div>
            <div class="book-author">Yes. Use CSV or MARC import tools with guided mapping.</div>
            <div class="book-name" style="margin-top: 12px;">Does Prime Lib work for multi-branch libraries?</div>
            <div class="book-author">Multi-branch routing and shared inventory are built in.</div>
            <div class="book-name" style="margin-top: 12px;">Is there training for staff?</div>
            <div class="book-author">We provide guided onboarding and a full knowledge base.</div>
          </div>
        </div>
      </div>

      <div class="panel" style="margin-top: 28px;">
        <div class="panel-title">Ready to modernize your library?</div>
        <div class="book-author">Start with a guided demo and a migration plan tailored to your collection.</div>
        <div class="top-actions" style="margin-top: 16px;">
          <a class="pill" href="signup.php">Book a demo</a>
          <a class="pill" href="signup.php">Contact sales</a>
        </div>
      </div>

      <footer class="stat-label" style="margin-top: 28px;">
        Prime Lib. Built for public, academic, and community libraries.
      </footer>
    </section>
  </main>

  <script src="Assets/js/script.js"></script>
</body>
</html>

