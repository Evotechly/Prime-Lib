<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('user');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/reader/mes_emprunts.php');
}

$authUser = auth_user();
$userId = (int)($authUser['id'] ?? 0);
$loanId = (int)($_POST['loan_id'] ?? 0);

if ($userId <= 0 || $loanId <= 0) {
    $_SESSION['reader_error'] = 'Invalid request.';
    redirect('/primelib/pages/reader/mes_emprunts.php');
}

$stmt = $conn->prepare('SELECT id_livre FROM emprunt WHERE id_emprunt = ? AND id_utilisateur = ? AND date_retour_effective IS NULL LIMIT 1');
$stmt->bind_param('ii', $loanId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$row) {
    $_SESSION['reader_error'] = 'Loan not found or already returned.';
    redirect('/primelib/pages/reader/mes_emprunts.php');
}

$bookId = (int)$row['id_livre'];
$today = date('Y-m-d');

$stmt = $conn->prepare('UPDATE emprunt SET date_retour_effective = ? WHERE id_emprunt = ?');
$stmt->bind_param('si', $today, $loanId);
if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['reader_error'] = 'Failed to return the book.';
    redirect('/primelib/pages/reader/mes_emprunts.php');
}
$stmt->close();

$check = $conn->prepare('SELECT COUNT(*) AS total FROM emprunt WHERE id_livre = ? AND date_retour_effective IS NULL');
$check->bind_param('i', $bookId);
$check->execute();
$countRow = $check->get_result()->fetch_assoc();
$check->close();

$status = ((int)($countRow['total'] ?? 0) > 0) ? 'emprunte' : 'disponible';
$update = $conn->prepare('UPDATE livre SET etat_disponibilite = ? WHERE id_livre = ?');
$update->bind_param('si', $status, $bookId);
$update->execute();
$update->close();

$_SESSION['reader_success'] = 'Book returned successfully.';
redirect('/primelib/pages/reader/mes_emprunts.php');
