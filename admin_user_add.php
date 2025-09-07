<?php
// admin_user_add.php — Add User (any role)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_users';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'user';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

// CSRF + messages
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();
$errors  = $_SESSION['form_errors']  ?? [];
$success = $_SESSION['form_success'] ?? '';
$old     = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_success'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add User | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body class="site">

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="dashboard-layout">
    <aside class="side-panel">
      <?php
        $sidebarPath = __DIR__ . '/partials/sidebar.php';
        if (!is_file($sidebarPath)) { $sidebarPath = __DIR__ . '/sidebar.php'; }
        if (is_file($sidebarPath)) { include $sidebarPath; }
        else { echo '<nav><ul class="side-menu"><li><a class="side-link" href="#">Sidebar missing</a></li></ul></nav>'; }
      ?>
    </aside>

    <section class="dashboard-content" aria-label="Add User">
      <section class="dash3-card" aria-labelledby="add-user-title">
        <header class="dash3-card__head"><h2 id="add-user-title">Add User</h2></header>

        <!-- Add User Form -->
        <form method="post" action="backend/user_add.php" class="add-user-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">

          <section class="form-row">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text"
                   value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES) ?>" required />
          </section>

          <section class="form-row">
            <label for="email">Email</label>
            <input id="email" name="email" type="email"
                   value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>" required />
          </section>

          <section class="form-row">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="6" />
          </section>

          <section class="form-row">
            <label for="confirm">Confirm Password</label>
            <input id="confirm" name="confirm" type="password" required minlength="6" />
          </section>

          <section class="form-row">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="">-- Select Role --</option>
              <option value="admin"         <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
              <option value="user"          <?= ($old['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
              <option value="human_resource"<?= ($old['role'] ?? '') === 'human_resource' ? 'selected' : '' ?>>Human Resource</option>
              <option value="stakeholder"   <?= ($old['role'] ?? '') === 'stakeholder' ? 'selected' : '' ?>>Stakeholder</option>
            </select>
          </section>

          <!-- Buttons -->
          <section class="form-actions">
            <button type="submit" class="btn">Create User</button>
            <a href="admin_users.php" class="btn-ghost">Cancel</a>
          </section>

          <!-- ✅ Messages now BELOW buttons -->
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
