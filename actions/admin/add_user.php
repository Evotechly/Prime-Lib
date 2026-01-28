<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/utilisateurs.php');
}

$authUser = auth_user();
$adminId = (int)($authUser['id'] ?? 0);

$nom = trim((string)($_POST['nom'] ?? ''));
$prenom = trim((string)($_POST['prenom'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
    $_SESSION['user_action_error'] = 'All fields are required.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['user_action_error'] = 'Please enter a valid email address.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

if ($password !== $confirm) {
    $_SESSION['user_action_error'] = 'Passwords do not match.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

$adminCheck = $conn->prepare('SELECT id_admin FROM admin WHERE email = ? LIMIT 1');
$adminCheck->bind_param('s', $email);
$adminCheck->execute();
$adminExists = $adminCheck->get_result()->fetch_assoc();
$adminCheck->close();

if ($adminExists) {
    $_SESSION['user_action_error'] = 'This email is already used by an admin.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

$userCheck = $conn->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = ? LIMIT 1');
$userCheck->bind_param('s', $email);
$userCheck->execute();
$userExists = $userCheck->get_result()->fetch_assoc();
$userCheck->close();

if ($userExists) {
    $_SESSION['user_action_error'] = 'An account with this email already exists.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    $_SESSION['user_action_error'] = 'Failed to secure the password.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

$insert = $conn->prepare('INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, id_admin) VALUES (?, ?, ?, ?, ?)');
$insert->bind_param('ssssi', $nom, $prenom, $email, $hash, $adminId);

if (!$insert->execute()) {
    $insert->close();
    $_SESSION['user_action_error'] = 'Failed to create user. Please try again.';
    $_SESSION['user_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_user.php');
}

$insert->close();

$_SESSION['user_action_success'] = 'User created successfully.';
redirect('/primelib/pages/admin/utilisateurs.php');
