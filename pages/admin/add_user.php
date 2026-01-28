<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

$err = $_SESSION['user_action_error'] ?? null;
$old = $_SESSION['user_action_old'] ?? [];
unset($_SESSION['user_action_error'], $_SESSION['user_action_old']);

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$oldNom = e((string)($old['nom'] ?? ''));
$oldPrenom = e((string)($old['prenom'] ?? ''));
$oldEmail = e((string)($old['email'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prime Lib - Add User</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body>
  <main class="main" style="padding:24px;max-width:760px;margin:0 auto;">
    <header class="topbar" style="margin-bottom:16px;">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text"><div class="brand-name">PRIME LIB</div></div>
      </div>
      <div class="top-actions">
        <span class="pill" style="cursor:default;"><?= e($authDisplayName !== '' ? $authDisplayName : 'Admin') ?></span>
        <a class="pill" href="utilisateurs.php">Back</a>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">User Action</div>
        <h1>Create User</h1>
        <div class="user-role">Add a new member account.</div>
      </div>

      <?php if ($err): ?>
        <div role="alert" style="margin:12px 0;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
          <?= e((string)$err) ?>
        </div>
      <?php endif; ?>

      <div class="table-card" style="margin-top:12px;">
        <form class="form" method="post" action="../../actions/admin/add_user.php">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="nom">Last name</label>
              <input id="nom" name="nom" type="text" required value="<?= $oldNom ?>" />
            </div>
            <div>
              <label for="prenom">First name</label>
              <input id="prenom" name="prenom" type="text" required value="<?= $oldPrenom ?>" />
            </div>
          </div>
          <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="<?= $oldEmail ?>" />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="password">Password</label>
              <input id="password" name="password" type="password" required />
            </div>
            <div>
              <label for="confirm_password">Confirm password</label>
              <input id="confirm_password" name="confirm_password" type="password" required />
            </div>
          </div>
          <div class="auth-actions" style="margin-top:8px;">
            <button class="auth-button" type="submit">Create user</button>
            <a class="pill" href="utilisateurs.php">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
