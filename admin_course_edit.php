<?php
// admin_course_edit.php — Edit Course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_courses';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'guest';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

require __DIR__ . '/backend/db.php';
require __DIR__ . '/backend/csrf.php';

$csrf = csrf_token();
$errors  = $_SESSION['form_errors']  ?? [];
$success = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_errors'], $_SESSION['form_success']);

// Get course id
$id = $_GET['id'] ?? '';
if (!$id) { header('Location: courses.php'); exit; }

// Fetch course
$stmt = $conn->prepare("SELECT id, course_code, title, description FROM courses WHERE id = ? LIMIT 1");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
  $_SESSION['form_errors'] = ['Course not found.'];
  header('Location: courses.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Course | LearnLang</title>
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

    <section class="dashboard-content" aria-label="Edit Course">
      <section class="dash3-card" aria-labelledby="edit-course-title">
        <header class="dash3-card__head"><h2 id="edit-course-title">Edit Course</h2></header>

        <form method="post" action="backend/course_edit.php" class="add-user-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($course['id'], ENT_QUOTES) ?>">

          <section class="form-row">
            <label for="course_code">Course Code</label>
            <input id="course_code" name="course_code" type="text"
                   value="<?= htmlspecialchars($course['course_code'] ?? '', ENT_QUOTES) ?>"
                   readonly />
          </section>

          <section class="form-row">
            <label for="title">Course Title</label>
            <input id="title" name="title" type="text"
                   value="<?= htmlspecialchars($course['title'] ?? '', ENT_QUOTES) ?>" required />
          </section>

          <section class="form-row">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($course['description'] ?? '', ENT_QUOTES) ?></textarea>
          </section>

          <section class="form-actions">
            <button type="submit" class="btn">Update Course</button>
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
