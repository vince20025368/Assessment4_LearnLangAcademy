<?php
// admin_user_edit.php — Edit User
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_users';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'user';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

require __DIR__ . '/backend/db.php';
require __DIR__ . '/backend/csrf.php';

$csrf = csrf_token();
$errors  = $_SESSION['form_errors']  ?? [];
$success = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_errors'], $_SESSION['form_success']);

// Get user id
$id = $_GET['id'] ?? '';
if (!$id) { header('Location: admin_users.php'); exit; }

// Fetch user
$stmt = $conn->prepare("SELECT id, name, email, account_type FROM accounts WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
  $_SESSION['form_errors'] = ['User not found.'];
  header('Location: admin_users.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit User | LearnLang</title>
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

    <section class="dashboard-content" aria-label="Edit User">
      <section class="dash3-card" aria-labelledby="edit-user-title">
        <header class="dash3-card__head"><h2 id="edit-user-title">Edit User</h2></header>

        <form method="post" action="backend/user_edit.php" class="add-user-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES) ?>">

          <section class="form-row">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text"
                   value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>" required />
          </section>

<section class="form-row">
  <label for="email">Email</label>
  <input id="email" name="email" type="email"
         value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>"
         readonly />
</section>

          <section class="form-row">
            <label for="password">New Password (leave blank to keep current)</label>
            <input id="password" name="password" type="password" minlength="6" />
          </section>

          <section class="form-row">
            <label for="confirm">Confirm New Password</label>
            <input id="confirm" name="confirm" type="password" minlength="6" />
          </section>

          <section class="form-row">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="admin"         <?= ($user['account_type'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
              <option value="user"          <?= ($user['account_type'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
              
            </select>
          </section>

          <section class="form-actions">
            <button type="submit" class="btn">Update User</button>
            <a href="admin_users.php" class="btn-ghost">Cancel</a>
          </section>

          <!-- ✅ Messages below buttons -->
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
