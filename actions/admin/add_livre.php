<?php 
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/livres.php');
}

$authUser = auth_user();
$idAdmin = (int)($authUser['id'] ?? 0);

$titre = trim((string)($_POST['titre'] ?? ''));
$auteur = trim((string)($_POST['auteur'] ?? ''));
$genre = trim((string)($_POST['genre'] ?? ''));
$annee = trim((string)($_POST['annee_publication'] ?? ''));
$etat = trim((string)($_POST['etat_disponibilite'] ?? 'disponible'));

$old = compact('titre', 'auteur', 'genre', 'annee', 'etat');

if ($titre === '' || $auteur === '') {
    $_SESSION['book_action_error'] = 'Title and author are required.';
    $_SESSION['book_action_old'] = $old;
    redirect('/primelib/pages/admin/add_livre.php');
}

$anneeInt = null;
if ($annee !== '') {
    if (!ctype_digit($annee)) {
        $_SESSION['book_action_error'] = 'Publication year must be a number.';
        $_SESSION['book_action_old'] = $old;
        redirect('/primelib/pages/admin/add_livre.php');
    }
    $anneeInt = (int)$annee;
}

$allowedEtat = ['disponible', 'emprunte', 'perdu'];
if (!in_array($etat, $allowedEtat, true)) {
    $etat = 'disponible';
}

if ($anneeInt === null) {
    $sql = 'INSERT INTO livre (titre, auteur, genre, annee_publication, etat_disponibilite, id_admin) VALUES (?, ?, ?, NULL, ?, ?)';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['book_action_error'] = 'Failed to prepare book insert.';
        $_SESSION['book_action_old'] = $old;
        redirect('/primelib/pages/admin/add_livre.php');
    }
    $stmt->bind_param('ssssi', $titre, $auteur, $genre, $etat, $idAdmin);
} else {
    $sql = 'INSERT INTO livre (titre, auteur, genre, annee_publication, etat_disponibilite, id_admin) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['book_action_error'] = 'Failed to prepare book insert.';
        $_SESSION['book_action_old'] = $old;
        redirect('/primelib/pages/admin/add_livre.php');
    }
    $stmt->bind_param('sssisi', $titre, $auteur, $genre, $anneeInt, $etat, $idAdmin);
}

if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['book_action_error'] = 'Failed to add the book. Please try again.';
    $_SESSION['book_action_old'] = $old;
    redirect('/primelib/pages/admin/add_livre.php');
}

$stmt->close();

$_SESSION['book_action_success'] = 'Book added successfully.';
redirect('/primelib/pages/admin/livres.php');