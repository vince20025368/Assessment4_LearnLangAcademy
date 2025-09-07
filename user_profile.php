<?php
// user_profile.php — User profile settings
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'user_profile';

// Auth guard: only users
if (empty($_SESSION['account_id']) || ($_SESSION['account_type'] ?? '') !== 'user') {
  header('Location: login.php');
  exit;
}

require __DIR__ . '/backend/user_profile.php'; // gives $user
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();

$errors  = $_SESSION['form_errors']  ?? [];
$success = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_errors'], $_SESSION['form_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Profile | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body class="site">

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="dashboard-layout">
  <!-- Sidebar -->
  <aside class="side-panel">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
  </aside>

  <!-- Content -->
  <section class="dashboard-content" aria-label="My Profile">
    <section class="dash3-card" aria-labelledby="profile-title">
      <header class="dash3-card__head">
        <h2 id="profile-title">My Profile</h2>
      </header>

      <form method="post" action="backend/user_profile_save.php" class="profile-form">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">

        <label for="name" class="form-label">Full Name</label>
        <input type="text" id="name" name="name"
               value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>

        <label for="email" class="form-label">Email</label>
        <input type="email" id="email"
               value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>

        <label for="password" class="form-label">New Password</label>
        <input type="password" id="password" name="password" placeholder="Leave blank to keep current">

        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">

        <button type="submit" class="btn btn-primary">Save Changes</button>

        <!-- ✅ Messages below the button -->
        <?php if ($success): ?>
          <p class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <section class="alert alert-danger" role="alert">
            <ul>
              <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      </form>
    </section>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
