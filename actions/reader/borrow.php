<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('user');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/reader/catalogue.php');
}

$authUser = auth_user();
$userId = (int)($authUser['id'] ?? 0);
$bookId = (int)($_POST['book_id'] ?? 0);

if ($userId <= 0 || $bookId <= 0) {
    $_SESSION['reader_error'] = 'Invalid request.';
    redirect('/primelib/pages/reader/catalogue.php');
}

$stmt = $conn->prepare('SELECT id_livre, etat_disponibilite FROM livre WHERE id_livre = ? LIMIT 1');
$stmt->bind_param('i', $bookId);
$stmt->execute();
$result = $stmt->get_result();
$book = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$book || (string)$book['etat_disponibilite'] !== 'disponible') {
    $_SESSION['reader_error'] = 'This book is not available right now.';
    redirect('/primelib/pages/reader/catalogue.php');
}

$stmt = $conn->prepare('SELECT id_emprunt FROM emprunt WHERE id_utilisateur = ? AND id_livre = ? AND date_retour_effective IS NULL LIMIT 1');
$stmt->bind_param('ii', $userId, $bookId);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    $_SESSION['reader_error'] = 'You already have this book on loan.';
    redirect('/primelib/pages/reader/catalogue.php');
}

$loanDays = 14;
$settingsResult = $conn->query('SELECT loan_days FROM settings ORDER BY id DESC LIMIT 1');
if ($settingsResult && $settingsResult->num_rows > 0) {
    $row = $settingsResult->fetch_assoc();
    $loanDays = max(1, (int)($row['loan_days'] ?? 14));
    $settingsResult->close();
}

$adminId = null;
$adminStmt = $conn->prepare('SELECT id_admin FROM utilisateur WHERE id_utilisateur = ? LIMIT 1');
$adminStmt->bind_param('i', $userId);
$adminStmt->execute();
$adminRow = $adminStmt->get_result()->fetch_assoc();
$adminStmt->close();
if ($adminRow && $adminRow['id_admin'] !== null) {
    $adminId = (int)$adminRow['id_admin'];
}

$dateEmprunt = date('Y-m-d');
$datePrevue = date('Y-m-d', strtotime('+' . $loanDays . ' days'));

if ($adminId === null) {
    $stmt = $conn->prepare('INSERT INTO emprunt (date_emprunt, date_retour_prevue, date_retour_effective, id_utilisateur, id_livre, id_admin) VALUES (?, ?, NULL, ?, ?, NULL)');
    $stmt->bind_param('ssii', $dateEmprunt, $datePrevue, $userId, $bookId);
} else {
    $stmt = $conn->prepare('INSERT INTO emprunt (date_emprunt, date_retour_prevue, date_retour_effective, id_utilisateur, id_livre, id_admin) VALUES (?, ?, NULL, ?, ?, ?)');
    $stmt->bind_param('ssiii', $dateEmprunt, $datePrevue, $userId, $bookId, $adminId);
}
if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['reader_error'] = 'Failed to create the loan. Please try again.';
    redirect('/primelib/pages/reader/catalogue.php');
}
$stmt->close();

$update = $conn->prepare("UPDATE livre SET etat_disponibilite = 'emprunte' WHERE id_livre = ?");
$update->bind_param('i', $bookId);
$update->execute();
$update->close();

$_SESSION['reader_success'] = 'Book borrowed successfully. Due date: ' . $datePrevue . '.';
redirect('/primelib/pages/reader/mes_emprunts.php');
