<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/utilisateurs.php');
}

$nom = trim((string)($_POST['nom'] ?? ''));
$prenom = trim((string)($_POST['prenom'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
    $_SESSION['admin_action_error'] = 'All fields are required.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['admin_action_error'] = 'Please enter a valid email address.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

if ($password !== $confirm) {
    $_SESSION['admin_action_error'] = 'Passwords do not match.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

// Email must be unique across both admin and utilisateur
$adminCheck = $conn->prepare('SELECT id_admin FROM admin WHERE email = ? LIMIT 1');
$adminCheck->bind_param('s', $email);
$adminCheck->execute();
$adminExists = $adminCheck->get_result()->fetch_assoc();
$adminCheck->close();

if ($adminExists) {
    $_SESSION['admin_action_error'] = 'An admin with this email already exists.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

$userCheck = $conn->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = ? LIMIT 1');
$userCheck->bind_param('s', $email);
$userCheck->execute();
$userExists = $userCheck->get_result()->fetch_assoc();
$userCheck->close();

if ($userExists) {
    $_SESSION['admin_action_error'] = 'This email is already used by a user.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    $_SESSION['admin_action_error'] = 'Failed to secure the password.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

$insert = $conn->prepare('INSERT INTO admin (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)');
$insert->bind_param('ssss', $nom, $prenom, $email, $hash);

if (!$insert->execute()) {
    $insert->close();
    $_SESSION['admin_action_error'] = 'Failed to create admin. Please try again.';
    $_SESSION['admin_action_old'] = compact('nom', 'prenom', 'email');
    redirect('/primelib/pages/admin/add_admin.php');
}

$insert->close();

$_SESSION['admin_action_success'] = 'Admin created successfully.';
redirect('/primelib/pages/admin/utilisateurs.php');
