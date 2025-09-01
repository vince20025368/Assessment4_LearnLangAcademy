<?php
// register.php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf   = $_SESSION['csrf'];
$active = 'login';

// Grab validation errors + old values from session (set in backend/register.php)
$errors  = $_SESSION['reg_errors'] ?? [];
$old     = $_SESSION['old'] ?? [];
$success = $_SESSION['reg_success'] ?? null;

// Clear them after displaying
unset($_SESSION['reg_errors'], $_SESSION['old'], $_SESSION['reg_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Account | LearnLang Academy</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body class="site">
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main id="main" class="site-main">
    <section class="auth-hero">
      <h1>Create Account</h1>
      <p class="muted">Join LearnLang Academy and start learning today.</p>
    </section>

    <section class="auth-wrap">
      <form class="auth-card" action="backend/register.php" method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>" />
        <input type="text" name="website" class="hp" autocomplete="off" />

        <!-- Global messages -->
        <?php if ($errors): ?>
          <div class="form-error" role="alert">
            <ul>
              <?php foreach ($errors as $msg): ?>
                <li><?= htmlspecialchars($msg) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php elseif ($success): ?>
          <div class="form-success" role="status">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <!-- Full Name -->
        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" 
               value="<?= htmlspecialchars($old['name'] ?? '') ?>" required />

        <!-- Email -->
        <label for="email">Email Address</label>
        <input id="email" name="email" type="email" 
               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required />

        <!-- Password -->
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required minlength="6" />

        <!-- Confirm Password -->
        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" name="confirm_password" type="password" required minlength="6" />

        <button type="submit" class="btn-auth">Create Account</button>
        <p>Already a member? <a href="login.php">Login</a></p>
      </form>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
