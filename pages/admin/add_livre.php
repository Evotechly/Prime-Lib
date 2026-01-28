<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$authUser = auth_user();
$authDisplayName = trim((string)($authUser['prenom'] ?? '') . ' ' . (string)($authUser['nom'] ?? ''));

$err = $_SESSION['book_action_error'] ?? null;
$old = $_SESSION['book_action_old'] ?? [];
unset($_SESSION['book_action_error'], $_SESSION['book_action_old']);

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$oldTitre = e((string)($old['titre'] ?? ''));
$oldAuteur = e((string)($old['auteur'] ?? ''));
$oldGenre = e((string)($old['genre'] ?? ''));
$oldAnnee = e((string)($old['annee'] ?? ''));
$oldEtat = (string)($old['etat'] ?? 'disponible');
$oldCover = e((string)($old['cover_url'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prime Lib - Add Book</title>
  <link rel="stylesheet" href="../../Assets/css/styles.css" />
</head>
<body>
  <main class="main" style="padding:24px;max-width:820px;margin:0 auto;">
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
        <h1>Add Book</h1>
        <div class="user-role">Add a new title to the library catalog.</div>
      </div>

      <?php if ($err): ?>
        <div role="alert" style="margin:12px 0;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
          <?= e((string)$err) ?>
        </div>
      <?php endif; ?>

      <div class="table-card" style="margin-top:12px;">
        <form class="form" method="post" action="../../actions/admin/add_livre.php">
          <div>
            <label for="titre">Title</label>
            <input id="titre" name="titre" type="text" required value="<?= $oldTitre ?>" />
          </div>
          <div>
            <label for="auteur">Author</label>
            <input id="auteur" name="auteur" type="text" required value="<?= $oldAuteur ?>" />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
              <label for="genre">Genre</label>
              <input id="genre" name="genre" type="text" value="<?= $oldGenre ?>" />
            </div>
            <div>
              <label for="annee_publication">Publication Year</label>
              <input id="annee_publication" name="annee_publication" type="number" min="0" step="1" value="<?= $oldAnnee ?>" />
            </div>
          </div>
          <div>
            <label for="cover_url">Cover image URL</label>
            <input id="cover_url" name="cover_url" type="url" placeholder="https://..." value="<?= $oldCover ?>" />
          </div>
          <div>
            <label for="etat_disponibilite">Availability</label>
            <select id="etat_disponibilite" name="etat_disponibilite">
              <option value="disponible" <?= $oldEtat === 'disponible' ? 'selected' : '' ?>>Disponible</option>
              <option value="emprunte" <?= $oldEtat === 'emprunte' ? 'selected' : '' ?>>Emprunte</option>
              <option value="perdu" <?= $oldEtat === 'perdu' ? 'selected' : '' ?>>Perdu</option>
            </select>
          </div>
          <div class="auth-actions" style="margin-top:8px;">
            <button class="auth-button" type="submit">Add book</button>
            <a class="pill" href="livres.php">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
