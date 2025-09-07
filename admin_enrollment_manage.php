<?php
// admin_enrollment_manage.php — Manage enrollments for a course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_enrollments';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'guest';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

require __DIR__ . '/backend/enrollment_manage.php'; // gives: $course, $enrollments, $availableUsers, $total, $page, $pages
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();

// Helpers for pager
function build_page_url(string $base, array $qs, int $p): string {
  $qs['page'] = $p;
  return $base.'?'.http_build_query($qs);
}

$qs = ['course_id' => $course['id']];
$baseUrl = 'admin_enrollment_manage.php';
$prev = max(1, $page - 1);
$next = min($pages, $page + 1);
$start = max(1, $page - 2);
$end   = min($pages, $page + 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Enrollments | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  <script>
    function confirmAction(message) { return confirm(message); }
  </script>
</head>
<body class="site">

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="dashboard-layout">
  <aside class="side-panel">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
  </aside>

  <section class="dashboard-content" aria-label="Manage Enrollments">
    <section class="dash3-card" aria-labelledby="enrollment-title">
      <header class="dash3-card__head">
        <a href="admin_enrollments.php" class="btn-ghost back-btn">← Back to Enrollments</a>
        <h2 id="enrollment-title">
          Manage Enrollments — <?= htmlspecialchars($course['course_code']) ?>: <?= htmlspecialchars($course['title']) ?>
        </h2>
      </header>


      <!-- Add Enrollment -->
      <form method="post" action="backend/enrollment_add.php"
            class="enrollment-add-form"
            onsubmit="return confirmAction('Are you sure you want to enroll this user?')">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id'], ENT_QUOTES) ?>">

        <label for="account_id" class="form-label">Add User:</label>
        <select id="account_id" name="account_id" required>
          <option value="">-- Select User --</option>
          <?php foreach ($availableUsers as $u): ?>
            <option value="<?= htmlspecialchars($u['id'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Enroll</button>
      </form>

      <!-- Enrolled Users -->
      <table class="table">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Status</th>
            <th>Enrolled At</th>
            <th class="center-col">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($enrollments)): ?>
          <tr><td colspan="5">No users enrolled.</td></tr>
        <?php else: foreach ($enrollments as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['name']) ?></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td>
              <!-- Update Status -->
              <form method="post" action="backend/enrollment_update.php"
                    style="display:inline"
                    onsubmit="return confirmAction('Are you sure you want to update this status?')">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($e['enrollment_id'], ENT_QUOTES) ?>">
                <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id'], ENT_QUOTES) ?>">
                <label class="visually-hidden" for="status-<?= $e['enrollment_id'] ?>">Status</label>
                <select id="status-<?= $e['enrollment_id'] ?>" name="status" onchange="this.form.requestSubmit()">
                  <option value="active"    <?= $e['status']==='active'?'selected':'' ?>>Active</option>
                  <option value="completed" <?= $e['status']==='completed'?'selected':'' ?>>Completed</option>
                  <option value="dropped"   <?= $e['status']==='dropped'?'selected':'' ?>>Dropped</option>
                </select>
              </form>
            </td>
            <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($e['enrolled_at']))) ?></td>
            <td class="center-col">
              <!-- Delete -->
              <form method="post" action="backend/enrollment_delete.php" style="display:inline"
                    onsubmit="return confirmAction('Remove this enrollment?')">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($e['enrollment_id'], ENT_QUOTES) ?>">
                <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id'], ENT_QUOTES) ?>">
                <button type="submit" class="btn-action btn-action--danger" title="Remove Enrollment">
                  <img src="images/trash.png" alt="Remove">
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>

      <!-- Pagination -->
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
