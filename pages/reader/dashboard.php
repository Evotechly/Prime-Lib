<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('user');
$authUser = auth_user();
$authDisplayName = trim(($authUser['prenom'] ?? '') . ' ' . ($authUser['nom'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib - Reader Dashboard</title>
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
        <span class="pill" style="cursor:default;">
          <?= htmlspecialchars($authDisplayName !== '' ? $authDisplayName : 'Reader', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">Reader area</div>
        <h1>Welcome to your dashboard</h1>
        <div class="user-role">Use the links below to browse and manage your loans.</div>
        <div class="top-actions" style="margin-top: 16px;">
          <a class="pill" href="catalogue.php">Catalogue</a>
          <a class="pill" href="mes_emprunts.php">My loans</a>
          <a class="pill" href="emprunter.php">Borrow</a>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
