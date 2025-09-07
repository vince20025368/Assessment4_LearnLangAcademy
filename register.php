<?php
// registration.php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf   = $_SESSION['csrf'];
$active = 'login'; // or 'register' if your header supports it

// Validation results (set in backend/register.php)
$errors  = $_SESSION['reg_errors'] ?? [];
$old     = $_SESSION['old'] ?? [];
$success = $_SESSION['reg_success'] ?? null;

// Clear after reading
unset($_SESSION['reg_errors'], $_SESSION['old'], $_SESSION['reg_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Account | LearnLang Academy</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body class="site">
  <a class="skip-link" href="#main">Skip to content</a>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main id="main" class="site-main">
    <section class="auth-hero" aria-labelledby="reg-title">
      <h1 id="reg-title">Create Account</h1>
      <p class="muted">Join LearnLang Academy and start learning today.</p>
    </section>

    <section class="auth-wrap" aria-labelledby="form-title">
      <form class="auth-card" action="backend/register.php" method="post" novalidate>
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
        <?php elseif ($success): ?>
          <p class="form-success" role="status" aria-live="polite">
            <?= htmlspecialchars($success) ?>
          </p>
        <?php endif; ?>

        <fieldset>
          <legend id="form-title" class="visually-hidden">Create your account</legend>

          <label for="name">Full Name</label>
          <input id="name" name="name" type="text"
                 value="<?= htmlspecialchars($old['name'] ?? '') ?>" required autocomplete="name" />

          <label for="email">Email Address</label>
          <input id="email" name="email" type="email"
                 value="<?= htmlspecialchars($old['email'] ?? '') ?>" required
                 autocomplete="email" inputmode="email" placeholder="you@example.com" />

          <label for="password">Password</label>
          <input id="password" name="password" type="password" required minlength="8"
                 autocomplete="new-password" placeholder="Min. 8 characters" />

          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" required minlength="8"
                 autocomplete="new-password" placeholder="Re-enter your password" />

          <button type="submit" class="btn-auth">Create Account</button>
          <p>Already a member? <a href="login.php">Login</a></p>
        </fieldset>
      </form>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
