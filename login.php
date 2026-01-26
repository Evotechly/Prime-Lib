<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authError = $_SESSION['auth_error'] ?? null;
$old = $_SESSION['auth_old'] ?? [];

unset($_SESSION['auth_error'], $_SESSION['auth_old']);

$emailValue = htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib - Login</title>
  <link rel="stylesheet" href="Assets/css/styles.css" />
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="brand">
      <div class="brand-icon">P</div>
      <div class="brand-text">
        <div class="brand-name">PRIME LIB</div>
      </div>
    </div>

    <div class="auth-header">
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-subtitle">Sign in to manage your library dashboard.</p>
    </div>

    <?php if ($authError): ?>
      <div class="auth-error" role="alert" style="margin-bottom:12px;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
        <?= htmlspecialchars((string) $authError, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="actions/auth/login.php">
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="you@library.org" value="<?= $emailValue ?>" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required />
      </div>
      <div class="auth-actions">
        <label>
          <input type="checkbox" name="remember" />
          Remember me
        </label>
        <a href="signup.php">Forgot password?</a>
      </div>
      <button class="auth-button" type="submit">Sign in</button>
    </form>

    <div class="auth-footer">
      Don’t have an account? <a href="signup.php">Create one</a>
    </div>
  </div>
</body>
</html>
