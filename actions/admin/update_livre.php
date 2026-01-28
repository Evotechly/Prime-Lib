<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/livres.php');
}

$id = (int)($_POST['id'] ?? 0);
$titre = trim((string)($_POST['titre'] ?? ''));
$auteur = trim((string)($_POST['auteur'] ?? ''));
$genre = trim((string)($_POST['genre'] ?? ''));
$annee = trim((string)($_POST['annee_publication'] ?? ''));
$etat = trim((string)($_POST['etat_disponibilite'] ?? ''));
$coverUrl = trim((string)($_POST['cover_url'] ?? ''));

if ($id <= 0 || $titre === '' || $auteur === '' || $etat === '') {
    $_SESSION['book_action_error'] = 'All required fields must be filled.';
    redirect('/primelib/pages/admin/edit_livre.php?id=' . $id);
}

$yearValue = null;
if ($annee !== '') {
    $yearInt = (int)$annee;
    $yearValue = $yearInt !== 0 ? $yearInt : null;
}

$validStatus = ['disponible', 'emprunte', 'perdu'];
if (!in_array($etat, $validStatus, true)) {
    $_SESSION['book_action_error'] = 'Invalid availability status.';
    redirect('/primelib/pages/admin/edit_livre.php?id=' . $id);
}

$coverValue = $coverUrl !== '' ? $coverUrl : null;

$sql = 'UPDATE livre SET titre = ?, auteur = ?, genre = ?, annee_publication = ?, etat_disponibilite = ?, cover_url = ? WHERE id_livre = ?';
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['book_action_error'] = 'Failed to prepare update.';
    redirect('/primelib/pages/admin/edit_livre.php?id=' . $id);
}

$stmt->bind_param('sssissi', $titre, $auteur, $genre, $yearValue, $etat, $coverValue, $id);
if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['book_action_error'] = 'Failed to update book.';
    redirect('/primelib/pages/admin/edit_livre.php?id=' . $id);
}
$stmt->close();

$_SESSION['book_action_success'] = 'Book updated successfully.';
redirect('/primelib/pages/admin/livres.php');
