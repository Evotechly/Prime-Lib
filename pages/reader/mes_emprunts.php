<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
require_role('user');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib - My Loans</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body>
  <main class="main" style="padding:24px;max-width:960px;margin:0 auto;">
    <header class="topbar" style="margin-bottom:16px;">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text">
          <div class="brand-name">PRIME LIB</div>
        </div>
      </div>
      <div class="top-actions">
        <a class="pill" href="dashboard.php">Dashboard</a>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">Loans</div>
        <h1>My loans</h1>
        <div class="user-role">Your current and past loans will appear here.</div>
      </div>
    </section>
  </main>
</body>
</html>
