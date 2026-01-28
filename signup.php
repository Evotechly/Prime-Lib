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

    <form class="form" method="post" action="pages/admin/dashboard.php">
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
