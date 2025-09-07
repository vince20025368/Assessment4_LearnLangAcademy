<?php
// admin_users.php — Manage Users
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_users';

// Auth guard
$isLoggedIn      = !empty($_SESSION['account_id'] ?? null);
$currentUserRole = $_SESSION['account_type'] ?? 'user';
if (!$isLoggedIn || $currentUserRole !== 'admin') { header('Location: login.php'); exit; }

// Data + CSRF
require __DIR__ . '/backend/users.php'; // provides: $rows, $total, $page, $pages, $q
require __DIR__ . '/backend/csrf.php';  
$csrf = csrf_token();

// Helpers for pager
function build_page_url(string $base, array $qs, int $p): string { 
  $qs['page'] = $p; 
  return $base.'?'.http_build_query($qs); 
}

$qs = []; if (!empty($q)) { $qs['q'] = $q; }
$baseUrl = 'admin_users.php';
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
  <title>Manage Users | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
  <script>
    function confirmAction(message, checkbox) {
      if (!confirm(message)) {
        if (checkbox) checkbox.checked = !checkbox.checked; // revert toggle if cancelled
        return false;
      }
      return true;
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

    <section class="dashboard-content" aria-label="Users">
      <section class="dash3-card search-card" aria-labelledby="user-list-title">
        <header class="dash3-card__head"><h2 id="user-list-title">User List</h2></header>

        <!-- Search -->
        <form method="get" action="admin_users.php" class="search-form" role="search" aria-label="Search Accounts">
          <label for="q">Search</label>
          <input id="q" name="q" type="search" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES) ?>" placeholder="Search by name, email or role" class="search-input" />
          <button type="submit" class="btn">Search</button>
          <a href="admin_user_add.php" class="btn-ghost">+ Add Users</a>
        </form>

        <!-- Table -->
        <table class="table" role="grid" aria-label="Accounts Table">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Role</th>
              <th scope="col">Active?</th>
              <th scope="col">Verified</th>
              <th scope="col">Created</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7">No users found.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['name'] ?? '', ENT_QUOTES) ?></td>
              <td>
                <?php $em = htmlspecialchars($r['email'] ?? '', ENT_QUOTES); ?>
                <a class="link" href="mailto:<?= $em ?>"><?= $em ?></a>
              </td>
              <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['account_type'] ?? '')), ENT_QUOTES) ?></td>

              <!-- Active? toggle -->
              <td>
                <form method="post" action="backend/user_toggle.php" class="toggle-form" onsubmit="return confirmAction('Are you sure you want to change Active status?')">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                  <input type="hidden" name="id"   value="<?= htmlspecialchars($r['id'] ?? '', ENT_QUOTES) ?>">
                  <input type="hidden" name="action" value="<?= !empty($r['is_active']) ? 'deactivate' : 'activate' ?>">
                  <label class="toggle" title="<?= !empty($r['is_active']) ? 'Click to deactivate' : 'Click to activate' ?>">
                    <input type="checkbox" <?= !empty($r['is_active']) ? 'checked' : '' ?>
                      onchange="if(confirmAction('Are you sure you want to change Active status?', this)) { this.form.action.value=this.checked?'activate':'deactivate'; this.form.submit(); }">
                    <span class="slider"></span>
                  </label>
                </form>
              </td>

              <!-- Verified toggle -->
              <td>
                <form method="post" action="backend/user_toggle.php" class="toggle-form" onsubmit="return confirmAction('Are you sure you want to change Verified status?')">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                  <input type="hidden" name="id"   value="<?= htmlspecialchars($r['id'] ?? '', ENT_QUOTES) ?>">
                  <input type="hidden" name="action" value="<?= !empty($r['is_verified']) ? 'unverify' : 'verify' ?>">
                  <label class="toggle toggle--verify" title="<?= !empty($r['is_verified']) ? 'Click to unverify' : 'Click to verify' ?>">
                    <input type="checkbox" <?= !empty($r['is_verified']) ? 'checked' : '' ?>
                      onchange="if(confirmAction('Are you sure you want to change Verified status?', this)) { this.form.action.value=this.checked?'verify':'unverify'; this.form.submit(); }">
                    <span class="slider"></span>
                  </label>
                </form>
              </td>

              <td>
                <?php
                  $created = $r['created_at'] ?? null;
                  echo $created ? htmlspecialchars(date('M j, Y', strtotime($created)), ENT_QUOTES) : '';
                ?>
              </td>

              <!-- Actions: Edit + Delete (icon-only) -->
              <td class="actions-cell">
                <a class="btn-action" href="admin_user_edit.php?id=<?= urlencode($r['id'] ?? '') ?>" title="Edit" aria-label="Edit">
                  <img src="images/pencil.png" alt="">
                </a>

                <form method="post" action="backend/user_delete.php" style="display:inline" 
                      onsubmit="return confirmAction('Are you sure you want to delete this user?')">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($r['id'] ?? '', ENT_QUOTES) ?>">
                  <button type="submit" class="btn-action btn-action--danger" title="Delete" aria-label="Delete">
                    <img src="images/trash.png" alt="">
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
