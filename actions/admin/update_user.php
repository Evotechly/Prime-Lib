<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/primelib/pages/admin/utilisateurs.php');
}

$id = (int)($_POST['id'] ?? 0);
$type = (string)($_POST['type'] ?? '');
$nom = trim((string)($_POST['nom'] ?? ''));
$prenom = trim((string)($_POST['prenom'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

if ($id <= 0 || !in_array($type, ['admin', 'user'], true)) {
    redirect('/primelib/pages/admin/utilisateurs.php');
}

if ($nom === '' || $prenom === '' || $email === '') {
    $_SESSION['user_action_error'] = 'All fields are required.';
    redirect('/primelib/pages/admin/edit_user.php?type=' . $type . '&id=' . $id);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['user_action_error'] = 'Please enter a valid email address.';
    redirect('/primelib/pages/admin/edit_user.php?type=' . $type . '&id=' . $id);
}

if ($password !== '' || $confirm !== '') {
    if ($password !== $confirm) {
        $_SESSION['user_action_error'] = 'Passwords do not match.';
        redirect('/primelib/pages/admin/edit_user.php?type=' . $type . '&id=' . $id);
    }
}

$checkAdmin = $conn->prepare('SELECT id_admin FROM admin WHERE email = ? LIMIT 1');
$checkAdmin->bind_param('s', $email);
$checkAdmin->execute();
$adminRow = $checkAdmin->get_result()->fetch_assoc();
$checkAdmin->close();

$checkUser = $conn->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = ? LIMIT 1');
$checkUser->bind_param('s', $email);
$checkUser->execute();
$userRow = $checkUser->get_result()->fetch_assoc();
$checkUser->close();

if ($type === 'admin') {
    if ($adminRow && (int)$adminRow['id_admin'] !== $id) {
        $_SESSION['admin_action_error'] = 'Another admin already uses this email.';
        redirect('/primelib/pages/admin/edit_user.php?type=admin&id=' . $id);
    }
    if ($userRow) {
        $_SESSION['admin_action_error'] = 'This email is already used by a user.';
        redirect('/primelib/pages/admin/edit_user.php?type=admin&id=' . $id);
    }
} else {
    if ($userRow && (int)$userRow['id_utilisateur'] !== $id) {
        $_SESSION['user_action_error'] = 'Another user already uses this email.';
        redirect('/primelib/pages/admin/edit_user.php?type=user&id=' . $id);
    }
    if ($adminRow) {
        $_SESSION['user_action_error'] = 'This email is already used by an admin.';
        redirect('/primelib/pages/admin/edit_user.php?type=user&id=' . $id);
    }
}

if ($type === 'admin') {
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            $_SESSION['admin_action_error'] = 'Failed to secure the password.';
            redirect('/primelib/pages/admin/edit_user.php?type=admin&id=' . $id);
        }
        $stmt = $conn->prepare('UPDATE admin SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id_admin = ?');
        $stmt->bind_param('ssssi', $nom, $prenom, $email, $hash, $id);
    } else {
        $stmt = $conn->prepare('UPDATE admin SET nom = ?, prenom = ?, email = ? WHERE id_admin = ?');
        $stmt->bind_param('sssi', $nom, $prenom, $email, $id);
    }
} else {
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            $_SESSION['user_action_error'] = 'Failed to secure the password.';
            redirect('/primelib/pages/admin/edit_user.php?type=user&id=' . $id);
        }
        $stmt = $conn->prepare('UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id_utilisateur = ?');
        $stmt->bind_param('ssssi', $nom, $prenom, $email, $hash, $id);
    } else {
        $stmt = $conn->prepare('UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?');
        $stmt->bind_param('sssi', $nom, $prenom, $email, $id);
    }
}

if (!$stmt) {
    $_SESSION['user_action_error'] = 'Failed to prepare update.';
    redirect('/primelib/pages/admin/edit_user.php?type=' . $type . '&id=' . $id);
}

if (!$stmt->execute()) {
    $stmt->close();
    $_SESSION['user_action_error'] = 'Failed to update account.';
    redirect('/primelib/pages/admin/edit_user.php?type=' . $type . '&id=' . $id);
}

$stmt->close();
$_SESSION['user_action_success'] = ($type === 'admin') ? 'Admin updated successfully.' : 'User updated successfully.';
redirect('/primelib/pages/admin/utilisateurs.php');
