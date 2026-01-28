<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authError = $_SESSION['auth_error'] ?? null;
$old = $_SESSION['auth_old'] ?? [];

unset($_SESSION['auth_error'], $_SESSION['auth_old']);

$nomValue = htmlspecialchars((string) ($old['nom'] ?? ''), ENT_QUOTES, 'UTF-8');
$prenomValue = htmlspecialchars((string) ($old['prenom'] ?? ''), ENT_QUOTES, 'UTF-8');
$emailValue = htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prime Lib - Sign Up</title>
  <link rel="stylesheet" href="Assets/css/styles.css" />
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="brand">
 
      <div class="brand-text">
        <div class="brand-name"><img src="../../Assets/img/logo.png"></div>
      </div>
    </div>
     <br>
    <div class="auth-header">
      <h1 class="auth-title">Create your account</h1>
    </div>

    <?php if ($authError): ?>
      <div class="auth-error" role="alert" style="margin-bottom:12px;color:#b42318;background:#fee4e2;padding:10px 12px;border-radius:8px;">
        <?= htmlspecialchars((string) $authError, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="actions/auth/register.php">
      <div>
        <label for="nom">Last name</label>
        <input id="nom" name="nom" type="text" placeholder="Nom" value="<?= $nomValue ?>" required />
      </div>
      <div>
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" required />
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
      </div>
      <div>
        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" name="confirm_password" type="password"  required />
      </div>
      <button class="auth-button" type="submit">Create account</button>
    </form>

    <div class="auth-footer">
      Already have an account? <a href="login.php"><ins>Sign in</ins></a>
    </div>
  </div>
</body>
</html>
