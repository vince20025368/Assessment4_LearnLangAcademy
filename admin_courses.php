<?php
// courses.php — Manage Courses
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_courses';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'guest';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

require __DIR__ . '/backend/courses.php'; // provides: $rows, $total, $page, $pages, $q
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();

// Helpers for pager
function build_page_url(string $base, array $qs, int $p): string {
  $qs['page'] = $p;
  return $base.'?'.http_build_query($qs);
}

$qs = [];
if (!empty($q)) { $qs['q'] = $q; }
$baseUrl = 'admin_courses.php';
$prev = max(1, $page - 1);
$next = min($pages, $page + 1);
$start = max(1, $page - 2);
$end = min($pages, $page + 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Courses | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  <script>
    function confirmAction(message) {
      return confirm(message);
    }
  </script>
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

    <section class="dashboard-content" aria-label="Courses">
      <section class="dash3-card search-card" aria-labelledby="course-list-title">
        <header class="dash3-card__head"><h2 id="course-list-title">Courses</h2></header>

        <!-- Search -->
        <form method="get" action="courses.php" class="search-form" role="search" aria-label="Search Courses">
          <label for="q">Search</label>
          <input id="q" name="q" type="search"
                 value="<?= htmlspecialchars($q ?? '', ENT_QUOTES) ?>"
                 placeholder="Search by course code or title" class="search-input" />
          <button type="submit" class="btn">Search</button>
          <?php if ($currentUserRole === 'admin'): ?>
            <a href="admin_course_add.php" class="btn-ghost">+ Add Course</a>
          <?php endif; ?>
        </form>

        <!-- Table -->
        <table class="table" role="grid" aria-label="Courses Table">
          <thead>
            <tr>
              <th scope="col">Code</th>
              <th scope="col">Title</th>
              <th scope="col">Description</th>

              <?php if ($currentUserRole === 'admin'): ?>
                <th scope="col">Actions</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="<?= $currentUserRole === 'admin' ? 5 : 4 ?>">No courses found.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['course_code'] ?? '', ENT_QUOTES) ?></td>
              <td><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
              <td><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES) ?></td>

              <?php if ($currentUserRole === 'admin'): ?>
              <td class="actions-cell">
                <a class="btn-action" href="admin_course_edit.php?id=<?= urlencode($r['id'] ?? '') ?>" 
                   title="Edit" aria-label="Edit">
                  <img src="images/pencil.png" alt="Edit">
                </a>
                <form method="post" action="backend/course_delete.php" style="display:inline"
                      onsubmit="return confirmAction('Are you sure you want to delete this course?')">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($r['id'] ?? '', ENT_QUOTES) ?>">
                  <button type="submit" class="btn-action btn-action--danger" title="Delete" aria-label="Delete">
                    <img src="images/trash.png" alt="Delete">
                  </button>
                </form>
              </td>
              <?php endif; ?>
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
