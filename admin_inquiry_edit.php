<?php
// admin_inquiry_edit.php — Edit Inquiry (Action Taken only)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_inquiries';

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

// Get inquiry id
$id = $_GET['id'] ?? '';
if (!$id) { header('Location: admin_inquiries.php'); exit; }

// Fetch inquiry
$stmt = $conn->prepare("
  SELECT id, account_id, ref_code, name, email, subject, message, action_taken, created_at
  FROM contact_inquiries
  WHERE id = ?
  LIMIT 1
");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$inquiry = $result->fetch_assoc();
$stmt->close();

if (!$inquiry) {
  $_SESSION['form_errors'] = ['Inquiry not found.'];
  header('Location: admin_inquiries.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Inquiry | LearnLang</title>
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

    <section class="dashboard-content" aria-label="Edit Inquiry">
      <section class="dash3-card" aria-labelledby="edit-inquiry-title">
        <header class="dash3-card__head"><h2 id="edit-inquiry-title">Take Action</h2></header>

        <form method="post" action="backend/inquiry_edit.php" class="add-user-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($inquiry['id'], ENT_QUOTES) ?>">

          <!-- Ref Code + Created -->
          <section class="form-row two-cols">
            <label>Ref Code
              <input type="text" value="<?= htmlspecialchars($inquiry['ref_code'], ENT_QUOTES) ?>" readonly />
            </label>
            <label>Created
              <input type="text" value="<?= htmlspecialchars(date('M j, Y g:i A', strtotime($inquiry['created_at'])), ENT_QUOTES) ?>" readonly />
            </label>
          </section>

          <!-- Name + Email -->
          <section class="form-row two-cols">
            <label>Name
              <input type="text" value="<?= htmlspecialchars($inquiry['name'], ENT_QUOTES) ?>" readonly />
            </label>
            <label>Email
              <input type="text" value="<?= htmlspecialchars($inquiry['email'], ENT_QUOTES) ?>" readonly />
            </label>
          </section>

          <!-- Subject + Inquiry Type -->
          <section class="form-row two-cols">
            <label>Subject
              <input type="text" value="<?= htmlspecialchars($inquiry['subject'], ENT_QUOTES) ?>" readonly />
            </label>
            <label>Inquiry Type
              <input type="text" value="<?= empty($inquiry['account_id']) ? 'Non-user Inquiry' : 'Registered User Inquiry' ?>" readonly />
            </label>
          </section>

          <!-- Full-width: Message -->
          <section class="form-row">
            <label>Message</label>
            <textarea rows="4" readonly><?= htmlspecialchars($inquiry['message'], ENT_QUOTES) ?></textarea>
          </section>

          <!-- Full-width: Action Taken -->
          <section class="form-row">
            <label for="action_taken">Action Taken</label>
            <textarea id="action_taken" name="action_taken" rows="3" placeholder="Enter action taken..."><?= htmlspecialchars($inquiry['action_taken'] ?? '', ENT_QUOTES) ?></textarea>
          </section>

          <section class="form-actions">
            <button type="submit" class="btn">Update Inquiry</button>
            <a href="admin_inquiries.php" class="btn-ghost">Cancel</a>
          </section>

          <!-- Messages -->
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
