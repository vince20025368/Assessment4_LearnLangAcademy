<?php
// admin_enrollments.php — Manage Enrollments (Course List + Count)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_enrollments';

// Auth guard (admin only)
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'guest';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

// Data + CSRF
require __DIR__ . '/backend/enrollments.php'; // provides: $rows, $total, $page, $pages, $q
require __DIR__ . '/backend/csrf.php';
$csrf = csrf_token();

// Helpers for pager
function build_page_url(string $base, array $qs, int $p): string {
  $qs['page'] = $p;
  return $base.'?'.http_build_query($qs);
}

$qs = [];
if (!empty($q)) { $qs['q'] = $q; }
$baseUrl = 'admin_enrollments.php';
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
  <title>Manage Enrollments | LearnLang</title>
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
        else { echo '<nav><ul><li><a href="#">Sidebar missing</a></li></ul></nav>'; }
      ?>
    </aside>

    <section class="dashboard-content" aria-label="Enrollments">
      <section class="dash3-card search-card" aria-labelledby="enrollment-list-title">
        <header class="dash3-card__head"><h2 id="enrollment-list-title">Courses & Enrollments</h2></header>

        <!-- Search -->
        <form method="get" action="admin_enrollments.php" class="search-form" role="search" aria-label="Search Courses">
          <label for="q">Search</label>
          <input id="q" name="q" type="search"
                 value="<?= htmlspecialchars($q ?? '', ENT_QUOTES) ?>"
                 placeholder="Search by course code or title" class="search-input" />
          <button type="submit" class="btn">Search</button>
        </form>

        <table class="table" role="grid" aria-label="Courses Table">
  <thead>
    <tr>
      <th scope="col">Code</th>
      <th scope="col">Title</th>
      <th scope="col">Description</th>
      <th scope="col" class="center-col">Enrolled Students</th>
      <th scope="col" class="center-col">Actions</th> <!-- Centered header -->
    </tr>
  </thead>
  <tbody>
  <?php if (empty($rows)): ?>
    <tr><td colspan="5">No courses found.</td></tr>
  <?php else: foreach ($rows as $r): ?>
    <tr>
      <td><?= htmlspecialchars($r['course_code'] ?? '', ENT_QUOTES) ?></td>
      <td><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
      <td><?= htmlspecialchars($r['description'] ?? '', ENT_QUOTES) ?></td>
      <td class="center-col"><?= (int)($r['enrolled_count'] ?? 0) ?></td>

      <!-- Centered Actions -->
      <td class="center-col">
        <a class="btn-manage" href="admin_enrollment_manage.php?course_id=<?= urlencode($r['id'] ?? '') ?>"
           title="Manage Enrollments" aria-label="Manage Enrollments">
          <img src="images/pencil.png" alt="" class="icon-manage"> Manage Enrollments
        </a>
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
            <?php for ($i = $start; $i <= $end; $i++): ?>
              <?php if ($i === $page): ?>
                <span class="pager-page is-current" aria-current="page"><?= $i ?></span>
              <?php else: ?>
                <a class="pager-page" href="<?= htmlspecialchars(build_page_url($baseUrl, $qs, $i)) ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
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
