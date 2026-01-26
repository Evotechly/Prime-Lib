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
      <div class="brand-icon">P</div>
      <div class="brand-text">
        <div class="brand-name">PRIME LIB</div>
      </div>
    </div>

    <div class="auth-header">
      <h1 class="auth-title">Create your account</h1>
      <p class="auth-subtitle">Start managing your library in minutes.</p>
    </div>

    <form class="form" method="post" action="pages/admin/dashboard.php">
      <div>
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" placeholder="Full name" required />
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="you@library.org" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required />
      </div>
      <div>
        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" name="confirm_password" type="password" placeholder="••••••••" required />
      </div>
      <button class="auth-button" type="submit">Create account</button>
    </form>

    <div class="auth-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </div>
  </div>
</body>
</html>
