<?php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf   = $_SESSION['csrf'];
$active = 'login';
$err    = isset($_GET['err']) ? htmlspecialchars($_GET['err'], ENT_QUOTES) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | LearnLang Academy</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body class="site">
  <a class="skip-link" href="#main">Skip to content</a>

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main id="main" class="site-main">
    <section class="auth-hero" aria-labelledby="login-title">
      <h1 id="login-title">Login</h1>
      <?php if ($err): ?>
        <p class="form-error" role="alert"><?php echo $err; ?></p>
      <?php endif; ?>
    </section>

    <section class="auth-wrap">
      <article class="auth-card">
        <form action="backend/login_submit.php" method="post" novalidate>
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES); ?>" />
          <!-- honeypot -->
          <input id="hp" name="hp" type="text" tabindex="-1" autocomplete="off"
                 aria-hidden="true" class="hp" />

          <label for="email">Email</label>
          <input id="email" name="email" type="email" required maxlength="200"
                 autocomplete="username" inputmode="email" placeholder="you@example.com" />

          <label for="password">Password</label>
          <input id="password" name="password" type="password" required minlength="8"
                 autocomplete="current-password" placeholder="Your password" />

          <button type="submit" class="btn-auth">Login</button>

          <p class="muted tiny center" style="margin-top:.9rem">
            Not a member yet? <a href="register.php">Create an account</a>
          </p>
        </form>
      </article>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
