<?php
// login.php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf   = $_SESSION['csrf'];
$active = 'login';

// Validation results (set in backend/login.php)
$errors = $_SESSION['login_errors'] ?? [];
$old    = $_SESSION['old_login'] ?? [];

// Clear after reading
unset($_SESSION['login_errors'], $_SESSION['old_login']);
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
      <p class="muted">Access your account and continue learning.</p>
    </section>

    <section class="auth-wrap" aria-labelledby="login-form-title">
      <form class="auth-card" action="backend/login.php" method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>" />
        <input type="text" name="website" class="hp" autocomplete="off" aria-hidden="true" />

        <?php if ($errors): ?>
          <section class="form-error" role="alert" aria-live="polite">
            <ul>
              <?php foreach ($errors as $msg): ?>
                <li><?= htmlspecialchars($msg) ?></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>

        <fieldset>
          <legend id="login-form-title" class="visually-hidden">Sign in to your account</legend>

          <label for="email">Email Address</label>
          <input
            id="email"
            name="email"
            type="email"
            required
            autocomplete="username"
            inputmode="email"
            placeholder="you@example.com"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
          />

          <label for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            minlength="8"
            autocomplete="current-password"
            placeholder="Your password"
          />

          <button type="submit" class="btn-auth">Login</button>
          <p>New here? <a href="register.php">Create an account</a></p>
        </fieldset>
      </form>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
  <script src="js/script.js"></script>
</body>
</html>
