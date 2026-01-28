<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);
$type = (string)($_GET['type'] ?? '');
if ($id <= 0 || !in_array($type, ['admin', 'user'], true)) {
  redirect('/primelib/pages/admin/utilisateurs.php');
}

$record = null;
if ($type === 'admin') {
  $stmt = $conn->prepare('SELECT id_admin AS id, nom, prenom, email FROM admin WHERE id_admin = ? LIMIT 1');
} else {
  $stmt = $conn->prepare('SELECT id_utilisateur AS id, nom, prenom, email FROM utilisateur WHERE id_utilisateur = ? LIMIT 1');
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
  $record = $result->fetch_assoc();
}
$stmt->close();

if (!$record) {
  redirect('/primelib/pages/admin/utilisateurs.php');
}

$flashError = $_SESSION['user_action_error'] ?? $_SESSION['admin_action_error'] ?? null;
unset($_SESSION['user_action_error'], $_SESSION['admin_action_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prime Lib - Edit <?= $type === 'admin' ? 'Admin' : 'User' ?></title>
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
        <div class="stat-label">Account Action</div>
        <h1>Edit <?= $type === 'admin' ? 'Admin' : 'User' ?></h1>
        <div class="user-role">Update name, email, or reset the password.</div>
      </div>

      <?php if ($flashError): ?>
        <div role="alert" style="margin:12px 0;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
          <?= e((string)$flashError) ?>
        </div>
      <?php endif; ?>

      <div class="table-card" style="margin-top:12px;">
        <form class="form" method="post" action="../../actions/admin/update_user.php">
          <input type="hidden" name="id" value="<?= e((string)$record['id']) ?>" />
          <input type="hidden" name="type" value="<?= e($type) ?>" />
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="nom">Last name</label>
              <input id="nom" name="nom" type="text" required value="<?= e((string)$record['nom']) ?>" />
            </div>
            <div>
              <label for="prenom">First name</label>
              <input id="prenom" name="prenom" type="text" required value="<?= e((string)$record['prenom']) ?>" />
            </div>
          </div>
          <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="<?= e((string)$record['email']) ?>" />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="password">New password</label>
              <input id="password" name="password" type="password" placeholder="Leave blank to keep current" />
            </div>
            <div>
              <label for="confirm_password">Confirm password</label>
              <input id="confirm_password" name="confirm_password" type="password" placeholder="Leave blank to keep current" />
            </div>
          </div>
          <div class="auth-actions" style="margin-top:8px;">
            <button class="auth-button" type="submit">Save changes</button>
            <a class="pill" href="utilisateurs.php">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
