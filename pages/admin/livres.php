<?php
// livres.php

// Later you can replace this array with a DB query (MySQL/PDO)
$books = [
  [
    "title" => "The Silent Signal",
    "author" => "Nora Fields",
    "category" => "Science",
    "isbn" => "978-0-562-11235-9",
    "status" => "available",
    "status_label" => "available",
  ],
  [
    "title" => "Modern Algorithms",
    "author" => "Devon Lee",
    "category" => "Computer Science",
    "isbn" => "978-1-402-19876-4",
    "status" => "soon",
    "status_label" => "returning soon",
  ],
  [
    "title" => "Ocean Stories",
    "author" => "Liam Carter",
    "category" => "Literature",
    "isbn" => "978-0-889-10811-2",
    "status" => "available",
    "status_label" => "available",
  ],
  [
    "title" => "The Quantum Path",
    "author" => "Dr. Hana Patel",
    "category" => "Physics",
    "isbn" => "978-1-946-55321-7",
    "status" => "unavailable",
    "status_label" => "unavailable",
  ],
  [
    "title" => "Math for Creators",
    "author" => "Ana Rivera",
    "category" => "Mathematics",
    "isbn" => "978-1-229-77123-4",
    "status" => "available",
    "status_label" => "available",
  ],
  [
    "title" => "Community Design",
    "author" => "Leo Grant",
    "category" => "Architecture",
    "isbn" => "978-0-778-55409-3",
    "status" => "soon",
    "status_label" => "returning soon",
  ],
];

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$totalTitles = count($books);
$availableNow = 0;
$onLoan = 0;

foreach ($books as $b) {
  if ($b["status"] === "available") $availableNow++;
  else $onLoan++; // "soon" + "unavailable" considered on loan/out
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prime Lib - Livres</title>
    <link rel="stylesheet" href="../../Assets/css/styles.css" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
    />
  </head>
  <body>
    <div class="app">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon"><img src="../../Assets/css/logo.png" alt=""></div>
        <div class="brand-text">
          <div class="brand-name"><img src="../../Assets/icons/logo.png" alt=""></div>
        </div>
      </div>
        <nav class="nav">
          <a class="nav-item" href="dashboard.php" data-page="dashboard">
            <img class="nav-icon" src="../../Assets/icons/homee.svg" alt="" />
            Dashboard
          </a>
          <a class="nav-item active" href="livres.php" data-page="livres">
            <img class="nav-icon" src="../../Assets/icons/books.svg" alt="" />
            Books
          </a>
          <a class="nav-item" href="dashboard.php" data-page="analytics">
            <img class="nav-icon" src="../../Assets/icons/analytic.svg" alt="" />
            Analytics
          </a>
          <a class="nav-item" href="emprunts.php" data-page="activities">
            <img class="nav-icon" src="../../Assets/icons/activity.svg" alt="" />
            Activities
          </a>
          <a class="nav-item" href="utilisateurs.php" data-page="members">
            <img class="nav-icon" src="../../Assets/icons/member.svg" alt="" />
            Members
          </a>
        </nav>
        <div class="sidebar-footer">
          <a class="nav-item ghost" href="dashboard.php">
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
            <span class="icon">ICON</span>
            <input
              type="text"
              id="book-search"
              placeholder="Search books or authors"
            />
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
              <h1>Livres</h1>
              <p class="subtitle">Manage the library catalog and inventory.</p>
            </div>
            <button class="primary">+ Add new book</button>
          </div>

          <div class="stat-grid">
            <article class="stat-card">
              <div class="p12">
                <img src="../../Assets/icons/total titles.svg" alt="" class="user00">
                <span class="p11">Total Titles</span>

              
              </div>

               <h2><?= e((string)$totalTitles) ?></h2>

              <!--<div class="stat-icon purple">TITLES</div>-->
            </article>
            <article class="stat-card">
              <div class="p12">
                <img src="../../Assets/icons/Available.svg" alt="" class="user01">
                <span class="p11">Available Now</span>
              </div>
              <h2><?= e((string)$availableNow) ?></h2>
              <!-- <div class="stat-icon mint">OK</div> -->
            </article>
            <article class="stat-card">
              <div class="p12">
                 <img src="../../Assets/icons/lloo.svg" alt="" class="user01">
                <span class="p11">On Loan</span>
               
              </div>
              <h2><?= e((string)$onLoan) ?></h2>
              <!-- <div class="stat-icon orange">LOANS</div> -->
            </article>
          </div>

          <div class="filters">
            <div class="filter-pill">Status</div>
            <button class="filter-button" data-filter="all">All</button>
            <button class="filter-button" data-filter="available">Available</button>
            <button class="filter-button" data-filter="soon">Returning Soon</button>
            <button class="filter-button" data-filter="unavailable">Unavailable</button>
            <button class="reset-button" type="button">Reset filters</button>
          </div>

          <div class="table-card">
            <table>
              <thead>
                <tr>
                  <th>Book</th>
                  <th>Author</th>
                  <th>Category</th>
                  <th>ISBN</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody id="books-table">
                <?php foreach ($books as $b): ?>
                  <tr
                    data-title="<?= e($b["title"]) ?>"
                    data-author="<?= e($b["author"]) ?>"
                    data-status="<?= e($b["status"]) ?>"
                  >
                    <td><?= e($b["title"]) ?></td>
                    <td><?= e($b["author"]) ?></td>
                    <td><?= e($b["category"]) ?></td>
                    <td><?= e($b["isbn"]) ?></td>
                    <td>
                      <span class="status <?= e($b["status"]) ?>">
                        <?= e($b["status_label"]) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="table-footer">
              <span id="table-count">
                Showing <?= e((string)count($books)) ?> of <?= e((string)count($books)) ?> books
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
    <script src="../../Assets/js/livres.js"></script>
  </body>
</html>




