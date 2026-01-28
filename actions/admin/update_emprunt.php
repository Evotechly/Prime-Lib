<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/emprunts.php');
}

$id = (int)($_POST['id'] ?? 0);
$idUtilisateur = (int)($_POST['id_utilisateur'] ?? 0);
$idLivre = (int)($_POST['id_livre'] ?? 0);
$idAdmin = trim((string)($_POST['id_admin'] ?? ''));
$dateEmprunt = trim((string)($_POST['date_emprunt'] ?? ''));
$datePrevue = trim((string)($_POST['date_retour_prevue'] ?? ''));
$dateRetour = trim((string)($_POST['date_retour_effective'] ?? ''));

if ($id <= 0 || $idUtilisateur <= 0 || $idLivre <= 0 || $dateEmprunt === '' || $datePrevue === '') {
    $_SESSION['loan_action_error'] = 'All required fields must be filled.';
    redirect('/primelib/pages/admin/edit_emprunt.php?id=' . $id);
}

$prevBookId = null;
$stmt = $conn->prepare('SELECT id_livre FROM emprunt WHERE id_emprunt = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $prevBookId = (int)$result->fetch_assoc()['id_livre'];
}
$stmt->close();

$adminValue = null;
if ($idAdmin !== '') {
    $adminValue = (int)$idAdmin;
}
$returnValue = null;
if ($dateRetour !== '') {
    $returnValue = $dateRetour;
}

$sql = 'UPDATE emprunt SET date_emprunt = ?, date_retour_prevue = ?, date_retour_effective = ?, id_utilisateur = ?, id_livre = ?, id_admin = ? WHERE id_emprunt = ?';
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['loan_action_error'] = 'Failed to prepare update.';
    redirect('/primelib/pages/admin/edit_emprunt.php?id=' . $id);
}

$stmt->bind_param('sssiiii', $dateEmprunt, $datePrevue, $returnValue, $idUtilisateur, $idLivre, $adminValue, $id);
if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['loan_action_error'] = 'Failed to update activity.';
    redirect('/primelib/pages/admin/edit_emprunt.php?id=' . $id);
}
$stmt->close();

function update_book_status(mysqli $conn, int $bookId): void {
    $status = 'disponible';
    $check = $conn->prepare('SELECT COUNT(*) AS total FROM emprunt WHERE id_livre = ? AND date_retour_effective IS NULL');
    if ($check) {
        $check->bind_param('i', $bookId);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();
        $check->close();
        if ($row && (int)$row['total'] > 0) {
            $status = 'emprunte';
        }
    }
    $update = $conn->prepare('UPDATE livre SET etat_disponibilite = ? WHERE id_livre = ?');
    if ($update) {
        $update->bind_param('si', $status, $bookId);
        $update->execute();
        $update->close();
    }
}

if ($prevBookId) {
    update_book_status($conn, $prevBookId);
}
if ($idLivre && $idLivre !== $prevBookId) {
    update_book_status($conn, $idLivre);
}

$_SESSION['loan_action_success'] = 'Activity updated successfully.';
redirect('/primelib/pages/admin/emprunts.php');
