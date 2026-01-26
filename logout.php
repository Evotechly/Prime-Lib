<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

auth_logout();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['auth_error'] = 'You have been signed out.';

header('Location: /primelib/login.php');
exit;