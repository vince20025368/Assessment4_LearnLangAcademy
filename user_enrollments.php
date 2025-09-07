<?php
// user_enrollments.php — My Enrollments
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'user_enrollments';

// Auth guard: only regular users
if (empty($_SESSION['account_id']) || ($_SESSION['account_type'] ?? '') !== 'user') {
  header('Location: login.php');
  exit;
}

require __DIR__ . '/backend/user_enrollments.php'; // gives: $myEnrollments, $availableCourses, $total, $page, $pages
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();

// Helpers for pager
function build_page_url(string $base, array $qs, int $p): string {
  $qs['page'] = $p;
  return $base.'?'.http_build_query($qs);
}

$qs = [];
$baseUrl = 'user_enrollments.php';
$prev  = max(1, $page - 1);
$next  = min($pages, $page + 1);
$start = max(1, $page - 2);
$end   = min($pages, $page + 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Enrollments | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body class="site">

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="dashboard-layout">
    <aside class="side-panel">
      <?php include __DIR__ . '/partials/sidebar.php'; ?>
    </aside>

    <section class="dashboard-content" aria-label="My Enrollments">
      <section class="dash3-card" aria-labelledby="enrollment-title">
        <header class="dash3-card__head">
          <h2 id="enrollment-title">My Enrollments</h2>
        </header>

        <!-- Add Enrollment -->
        <form method="post" action="backend/user_enrollment_add.php"
              class="user-enroll-form"
              onsubmit="return confirm('Are you sure you want to enroll in this course?')">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
          <label for="course_id" class="form-label">Add Course:</label>
          <select id="course_id" name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($availableCourses as $c): ?>
              <option value="<?= htmlspecialchars($c['id'], ENT_QUOTES) ?>">
                <?= htmlspecialchars($c['course_code']) ?> — <?= htmlspecialchars($c['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn">Enroll</button>
        </form>

        <!-- List Enrollments -->
        <table class="table">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Title</th>
              <th>Status</th>
              <th>Enrolled At</th>
              <th class="center-col">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($myEnrollments)): ?>
            <tr><td colspan="5">You are not enrolled in any courses yet.</td></tr>
          <?php else: foreach ($myEnrollments as $e): ?>
            <tr>
              <td><?= htmlspecialchars($e['course_code']) ?></td>
              <td><?= htmlspecialchars($e['title']) ?></td>
              <td><?= htmlspecialchars(ucfirst($e['status'])) ?></td>
              <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($e['enrolled_at']))) ?></td>
              <td class="center-col">
                <form method="post" action="backend/user_enrollment_delete.php"
                      style="display:inline"
                      onsubmit="return confirm('Are you sure you want to drop this course?')">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                  <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($e['enrollment_id'], ENT_QUOTES) ?>">
                  <button type="submit" class="btn-action btn-action--danger" title="Drop Course">
                    <img src="images/trash.png" alt="Drop">
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>

        <!-- Pagination (always shown, even for 1 page) -->
        <?php
          $firstUrl = build_page_url($baseUrl, $qs, 1);
          $prevUrl  = build_page_url($baseUrl, $qs, $prev);
          $nextUrl  = build_page_url($baseUrl, $qs, $next);
          $lastUrl  = build_page_url($baseUrl, $qs, $pages);
        ?>
        <nav class="pager" aria-label="Pagination">
          <a class="pager-btn <?= $page <= 1 ? 'is-disabled' : '' ?>" href="<?= htmlspecialchars($firstUrl) ?>">« First</a>
          <a class="pager-btn <?= $page <= 1 ? 'is-disabled' : '' ?>" href="<?= htmlspecialchars($prevUrl) ?>">‹ Prev</a>

          <span class="pager-pages">
            <?php if ($start > 1): ?>
              <a class="pager-page" href="<?= htmlspecialchars(build_page_url($baseUrl, $qs, 1)) ?>">1</a>
              <?php if ($start > 2): ?><span class="pager-gap">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
              <?php if ($i === $page): ?>
                <span class="pager-page is-current" aria-current="page"><?= $i ?></span>
              <?php else: ?>
                <a class="pager-page" href="<?= htmlspecialchars(build_page_url($baseUrl, $qs, $i)) ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($end < $pages): ?>
              <?php if ($end < $pages - 1): ?><span class="pager-gap">…</span><?php endif; ?>
              <a class="pager-page" href="<?= htmlspecialchars(build_page_url($baseUrl, $qs, $pages)) ?>"><?= $pages ?></a>
            <?php endif; ?>
          </span>

          <a class="pager-btn <?= $page >= $pages ? 'is-disabled' : '' ?>" href="<?= htmlspecialchars($nextUrl) ?>">Next ›</a>
          <a class="pager-btn <?= $page >= $pages ? 'is-disabled' : '' ?>" href="<?= htmlspecialchars($lastUrl) ?>">Last »</a>

          <span class="pager-info"><?= number_format((int)$total) ?> total • Page <?= (int)$page ?> of <?= (int)$pages ?></span>
        </nav>

      </section>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
