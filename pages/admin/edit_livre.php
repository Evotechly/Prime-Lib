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
  redirect('/primelib/pages/admin/livres.php');
}

$book = null;
$stmt = $conn->prepare('SELECT id_livre, titre, auteur, genre, annee_publication, etat_disponibilite, cover_url FROM livre WHERE id_livre = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
  $book = $result->fetch_assoc();
}
$stmt->close();

if (!$book) {
  redirect('/primelib/pages/admin/livres.php');
}

$flashError = $_SESSION['book_action_error'] ?? null;
unset($_SESSION['book_action_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prime Lib - Edit Book</title>
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
        <a class="pill" href="livres.php">Back</a>
        <a class="pill" href="../../logout.php">Logout</a>
      </div>
    </header>

    <section class="content">
      <div class="panel">
        <div class="stat-label">Book Action</div>
        <h1>Edit Book</h1>
        <div class="user-role">Update book information and availability.</div>
      </div>

      <?php if ($flashError): ?>
        <div role="alert" style="margin:12px 0;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
          <?= e((string)$flashError) ?>
        </div>
      <?php endif; ?>

      <div class="table-card" style="margin-top:12px;">
        <form class="form" method="post" action="../../actions/admin/update_livre.php">
          <input type="hidden" name="id" value="<?= e((string)$book['id_livre']) ?>" />
          <div>
            <label for="titre">Title</label>
            <input id="titre" name="titre" type="text" required value="<?= e((string)$book['titre']) ?>" />
          </div>
          <div>
            <label for="auteur">Author</label>
            <input id="auteur" name="auteur" type="text" required value="<?= e((string)$book['auteur']) ?>" />
          </div>
          <div>
            <label for="genre">Genre</label>
            <input id="genre" name="genre" type="text" value="<?= e((string)($book['genre'] ?? '')) ?>" />
          </div>
          <div>
            <label for="annee_publication">Publication year</label>
            <input id="annee_publication" name="annee_publication" type="number" value="<?= e((string)($book['annee_publication'] ?? '')) ?>" />
          </div>
          <div>
            <label for="etat_disponibilite">Availability</label>
            <select id="etat_disponibilite" name="etat_disponibilite" required>
              <?php
                $status = (string)($book['etat_disponibilite'] ?? 'disponible');
                $options = ['disponible' => 'Available', 'emprunte' => 'On loan', 'perdu' => 'Lost'];
                foreach ($options as $value => $label):
              ?>
                <option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="cover_url">Cover image URL</label>
            <input id="cover_url" name="cover_url" type="url" placeholder="https://..." value="<?= e((string)($book['cover_url'] ?? '')) ?>" />
          </div>
          <div class="auth-actions" style="margin-top:8px;">
            <button class="auth-button" type="submit">Save changes</button>
            <a class="pill" href="livres.php">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
