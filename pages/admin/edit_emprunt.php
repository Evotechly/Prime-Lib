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
if ($id <= 0) {
  redirect('/primelib/pages/admin/emprunts.php');
}

$loan = null;
$stmt = $conn->prepare('SELECT id_emprunt, date_emprunt, date_retour_prevue, date_retour_effective, id_utilisateur, id_livre, id_admin FROM emprunt WHERE id_emprunt = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
  $loan = $result->fetch_assoc();
}
$stmt->close();

if (!$loan) {
  redirect('/primelib/pages/admin/emprunts.php');
}

$users = [];
$userResult = $conn->query('SELECT id_utilisateur, prenom, nom FROM utilisateur ORDER BY prenom, nom');
if ($userResult) {
  while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
  }
  $userResult->close();
}

$books = [];
$bookResult = $conn->query('SELECT id_livre, titre, auteur FROM livre ORDER BY titre');
if ($bookResult) {
  while ($row = $bookResult->fetch_assoc()) {
    $books[] = $row;
  }
  $bookResult->close();
}

$admins = [];
$adminResult = $conn->query('SELECT id_admin, prenom, nom FROM admin ORDER BY prenom, nom');
if ($adminResult) {
  while ($row = $adminResult->fetch_assoc()) {
    $admins[] = $row;
  }
  $adminResult->close();
}

$flashError = $_SESSION['loan_action_error'] ?? null;
unset($_SESSION['loan_action_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prime Lib - Edit Activity</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body>
  <main class="main" style="padding:24px;max-width:860px;margin:0 auto;">
    <header class="topbar" style="margin-bottom:16px;">
      <div class="brand">
        <div class="brand-icon">P</div>
        <div class="brand-text"><div class="brand-name">PRIME LIB</div></div>
      </div>
      <div class="top-actions">
        <span class="pill" style="cursor:default;"><?= e($authDisplayName !== '' ? $authDisplayName : 'Admin') ?></span>
        <a class="pill" href="emprunts.php">Back</a>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">Activity Action</div>
        <h1>Edit Activity</h1>
        <div class="user-role">Update loan details and return tracking.</div>
      </div>

      <?php if ($flashError): ?>
        <div role="alert" style="margin:12px 0;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
          <?= e((string)$flashError) ?>
        </div>
      <?php endif; ?>

      <div class="table-card" style="margin-top:12px;">
        <form class="form" method="post" action="../../actions/admin/update_emprunt.php">
          <input type="hidden" name="id" value="<?= e((string)$loan['id_emprunt']) ?>" />
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="id_utilisateur">Member</label>
              <select id="id_utilisateur" name="id_utilisateur" required>
                <?php foreach ($users as $user): ?>
                  <option value="<?= e((string)$user['id_utilisateur']) ?>" <?= (int)$loan['id_utilisateur'] === (int)$user['id_utilisateur'] ? 'selected' : '' ?>>
                    <?= e(trim((string)$user['prenom'] . ' ' . (string)$user['nom'])) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="id_livre">Book</label>
              <select id="id_livre" name="id_livre" required>
                <?php foreach ($books as $book): ?>
                  <option value="<?= e((string)$book['id_livre']) ?>" <?= (int)$loan['id_livre'] === (int)$book['id_livre'] ? 'selected' : '' ?>>
                    <?= e((string)$book['titre']) ?> · <?= e((string)$book['auteur']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="date_emprunt">Loan date</label>
              <input id="date_emprunt" name="date_emprunt" type="date" required value="<?= e((string)$loan['date_emprunt']) ?>" />
            </div>
            <div>
              <label for="date_retour_prevue">Due date</label>
              <input id="date_retour_prevue" name="date_retour_prevue" type="date" required value="<?= e((string)$loan['date_retour_prevue']) ?>" />
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="date_retour_effective">Return date (optional)</label>
              <input id="date_retour_effective" name="date_retour_effective" type="date" value="<?= e((string)($loan['date_retour_effective'] ?? '')) ?>" />
            </div>
            <div>
              <label for="id_admin">Admin</label>
              <select id="id_admin" name="id_admin">
                <option value="">Unassigned</option>
                <?php foreach ($admins as $admin): ?>
                  <option value="<?= e((string)$admin['id_admin']) ?>" <?= (int)($loan['id_admin'] ?? 0) === (int)$admin['id_admin'] ? 'selected' : '' ?>>
                    <?= e(trim((string)$admin['prenom'] . ' ' . (string)$admin['nom'])) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="auth-actions" style="margin-top:8px;">
            <button class="auth-button" type="submit">Save changes</button>
            <a class="pill" href="emprunts.php">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
