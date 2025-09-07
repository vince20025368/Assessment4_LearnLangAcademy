<?php
// admin_course_add.php — Add Course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_courses';

// Auth guard (only admin can add courses)
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'guest';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

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
  <title>Add Course | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
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

    <section class="dashboard-content" aria-label="Add Course">
      <section class="dash3-card" aria-labelledby="add-course-title">
        <header class="dash3-card__head"><h2 id="add-course-title">Add Course</h2></header>

        <form method="post" action="backend/course_add.php" class="add-user-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">

          <section class="form-row">
            <label for="course_code">Course Code</label>
            <input id="course_code" name="course_code" type="text"
                   value="<?= htmlspecialchars($old['course_code'] ?? '', ENT_QUOTES) ?>" required />
          </section>

          <section class="form-row">
            <label for="title">Course Title</label>
            <input id="title" name="title" type="text"
                   value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES) ?>" required />
          </section>

          <section class="form-row">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES) ?></textarea>
          </section>

          <section class="form-actions">
            <button type="submit" class="btn">Create Course</button>
            <a href="admin_courses.php" class="btn-ghost">Cancel</a>
          </section>

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
