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

    <form class="form" method="post" action="pages/admin/dashboard.php">
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
