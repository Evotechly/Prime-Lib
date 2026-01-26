<?php
require_once __DIR__ . '/../../includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/signup.php');
}

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ((string) $password !== (string) $confirm) {
    $_SESSION['auth_error'] = 'Passwords do not match.';
    $_SESSION['auth_old'] = [
        'nom' => (string) $nom,
        'prenom' => (string) $prenom,
        'email' => (string) $email,
    ];
    redirect('/primelib/signup.php');
}

$result = auth_register_user(
    $conn,
    (string) $nom,
    (string) $prenom,
    (string) $email,
    (string) $password
);

if (!$result['success']) {
    $_SESSION['auth_error'] = $result['message'] ?? 'Registration failed.';
    $_SESSION['auth_old'] = [
        'nom' => (string) $nom,
        'prenom' => (string) $prenom,
        'email' => (string) $email,
    ];
    redirect('/primelib/signup.php');
}

redirect('/primelib/pages/reader/dashboard.php');
