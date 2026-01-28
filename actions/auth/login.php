<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/login.php');
}

$email = (string)($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');

// Temporary debug to surface what the running app sees
$authDebug = [];
$dbRow = $conn->query("SELECT DATABASE() AS db");
$authDebug[] = 'db=' . (($dbRow && $dbRow->num_rows > 0) ? (string)$dbRow->fetch_assoc()['db'] : 'unknown');

$adminStmt = $conn->prepare('SELECT id_admin, email, mot_de_passe FROM admin WHERE email = ? LIMIT 1');
$adminStmt->bind_param('s', $email);
$adminStmt->execute();
$adminRow = $adminStmt->get_result()->fetch_assoc();
$adminStmt->close();

if ($adminRow) {
    $stored = (string)($adminRow['mot_de_passe'] ?? '');
    $authDebug[] = 'admin_found=1';
    $authDebug[] = 'stored_len=' . strlen($stored);
    $authDebug[] = 'match=' . (auth_password_matches($password, $stored) ? '1' : '0');
} else {
    $authDebug[] = 'admin_found=0';
}

$result = auth_attempt_login($conn, $email, $password);

if (!$result['success']) {
    $_SESSION['auth_error'] = ($result['message'] ?? 'Login failed.') . ' | debug: ' . implode(', ', $authDebug);
    $_SESSION['auth_old'] = ['email' => $email];
    redirect('/primelib/login.php');
}

if (auth_role() === 'admin') {
    redirect('/primelib/pages/admin/dashboard.php');
}

redirect('/primelib/pages/reader/dashboard.php');