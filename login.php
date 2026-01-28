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
        <div class=""><img class="primelib" src="../../Assets/img/logo.png">
    </div>
      <div class="brand-text">
      </div>
    </div>
      <br>
    <div class="auth-header">
      <h1 class="auth-title">Welcome back</h1>
    </div>

    <?php if ($authError): ?>
      <div class="auth-error" role="alert" style="margin-bottom:12px;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
        <?= htmlspecialchars((string) $authError, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="actions/auth/login.php">
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password"required />
      </div>
      <div class="auth-actions">
        <label>
          Remember me
        <input type="checkbox" name="remember" />
        </label>
        <a href="signup.php">Forgot password?</a>
      </div>
      <button class="auth-button" type="submit">Sign in</button>
    </form>

    <div class="auth-footer">
      Don't have an account? <a href="signup.php"><ins>Create one</ins></a>
    </div>
  </div>
</body>
</html>
