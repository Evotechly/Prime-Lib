<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
function auth_set_user(array $user): void
{
    $_SESSION['auth_user'] = $user;
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_role(): ?string
{
    $user = auth_user();
    return $user['role'] ?? null;
}

function auth_password_matches(string $password, string $stored): bool
{
    if ($password === '' || $stored === '') {
        return false;
    }

    // Legacy seed uses SHA-256 hex; fall back to that if needed.
    if (auth_password_is_legacy_sha256($stored)) {
        return hash('sha256', $password) === strtolower($stored);
    }

    return password_verify($password, $stored);
}

function auth_password_is_legacy_sha256(string $stored): bool
{
    if (strlen($stored) !== 64) {
        return false;
    }

    return ctype_xdigit($stored);
}
function require_auth(): void
{
    if (!auth_check()) {
        $_SESSION['auth_error'] = 'Please sign in first.';
        redirect('/primelib/login.php');
    }
}

function require_role(string $role): void
{
    require_auth();

    if (auth_role() !== $role) {
        // Send users to the correct dashboard if they are signed in
        if (auth_role() === 'admin') {
            redirect('/primelib/pages/admin/dashboard.php');
        }
        if (auth_role() === 'user') {
            redirect('/primelib/pages/reader/dashboard.php');
        }

        // Fallback: clear session and force re-login
        auth_logout();
        $_SESSION['auth_error'] = 'Your session was invalid. Please sign in again.';
        redirect('/primelib/login.php');
    }
}

/**
 * Log out the current user safely.
 */
function auth_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Attempt to authenticate either an admin or a user by email/password.
 *
 * @return array{success: bool, message?: string}
 */
function auth_attempt_login(mysqli $conn, string $email, string $password): array
{
    $email = trim($email);

    if ($email == '' || $password == '') {
        return [
            'success' => false,
            'message' => 'Email and password are required.',
        ];
    }

    // 1) Try admin first
    $adminSql = 'SELECT id_admin, nom, prenom, email, mot_de_passe FROM admin WHERE email = ? LIMIT 1';
    if ($stmt = $conn->prepare($adminSql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($admin && !empty($admin['mot_de_passe']) && auth_password_matches($password, (string) $admin['mot_de_passe'])) {
            if (auth_password_is_legacy_sha256((string) $admin['mot_de_passe'])) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                if ($newHash !== false) {
                    $rehash = $conn->prepare('UPDATE admin SET mot_de_passe = ? WHERE id_admin = ?');
                    if ($rehash) {
                        $adminId = (int) $admin['id_admin'];
                        $rehash->bind_param('si', $newHash, $adminId);
                        $rehash->execute();
                        $rehash->close();
                    }
                }
            }
            auth_set_user([
                'role' => 'admin',
                'id' => (int) $admin['id_admin'],
                'nom' => (string) $admin['nom'],
                'prenom' => (string) $admin['prenom'],
                'email' => (string) $admin['email'],
            ]);

            return ['success' => true];
        }
    }

    // 2) Try normal user
    $userSql = 'SELECT id_utilisateur, nom, prenom, email, mot_de_passe FROM utilisateur WHERE email = ? LIMIT 1';
    if ($stmt = $conn->prepare($userSql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($user && !empty($user['mot_de_passe']) && auth_password_matches($password, (string) $user['mot_de_passe'])) {
            if (auth_password_is_legacy_sha256((string) $user['mot_de_passe'])) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                if ($newHash !== false) {
                    $rehash = $conn->prepare('UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?');
                    if ($rehash) {
                        $userId = (int) $user['id_utilisateur'];
                        $rehash->bind_param('si', $newHash, $userId);
                        $rehash->execute();
                        $rehash->close();
                    }
                }
            }
            auth_set_user([
                'role' => 'user',
                'id' => (int) $user['id_utilisateur'],
                'nom' => (string) $user['nom'],
                'prenom' => (string) $user['prenom'],
                'email' => (string) $user['email'],
            ]);

            return ['success' => true];
        }
    }

    return [
        'success' => false,
        'message' => 'Invalid email or password.',
    ];
}
function auth_register_user(mysqli $conn, string $nom, string $prenom, string $email, string $password): array
{
    $nom = trim($nom);
    $prenom = trim($prenom);
    $email = trim($email);

    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        return [
            'success' => false,
            'message' => 'All fields are required.',
        ];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Please enter a valid email address.',
        ];
    }

    // Ensure email is not already used by admin
    $adminCheck = 'SELECT id_admin FROM admin WHERE email = ? LIMIT 1';
    if ($stmt = $conn->prepare($adminCheck)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $existsAdmin = $stmt->get_result()?->fetch_assoc();
        $stmt->close();

        if ($existsAdmin) {
            return [
                'success' => false,
                'message' => 'This email is reserved for an administrator.',
            ];
        }
    }

    // Ensure email is not already used by a user
    $userCheck = 'SELECT id_utilisateur FROM utilisateur WHERE email = ? LIMIT 1';
    if ($stmt = $conn->prepare($userCheck)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $existsUser = $stmt->get_result()?->fetch_assoc();
        $stmt->close();

        if ($existsUser) {
            return [
                'success' => false,
                'message' => 'An account with this email already exists.',
            ];
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        return [
            'success' => false,
            'message' => 'Failed to secure the password.',
        ];
    }

    $insertSql = 'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)';
    if (!($stmt = $conn->prepare($insertSql))) {
        return [
            'success' => false,
            'message' => 'Failed to prepare registration statement.',
        ];
    }

    $stmt->bind_param('ssss', $nom, $prenom, $email, $hash);

    if (!$stmt->execute()) {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Registration failed. Please try again.',
        ];
    }

    $newId = (int) $stmt->insert_id;
    $stmt->close();

    auth_set_user([
        'role' => 'user',
        'id' => $newId,
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
    ]);

    return ['success' => true];
}
?>
