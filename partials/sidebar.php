<?php
// partials/sidebar.php — styled panel sidebar; logout looks like a link but posts safely
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = $active ?? '';
require_once __DIR__ . '/../backend/csrf.php';
$csrf = csrf_token();

$role = $_SESSION['account_type'] ?? 'user'; 
?>
<aside class="side-panel" aria-label="Sidebar">
  <nav class="side-nav">
    <p class="side-heading">Navigation</p>
    <ul class="side-menu">
      <?php if ($role === 'admin'): ?>
        <li>
          <a class="side-link <?= $active === 'admin_dashboard' ? 'is-active' : '' ?>"
             href="admin_dashboard.php" <?= $active === 'admin_dashboard' ? 'aria-current="page"' : '' ?>>
            Dashboard
          </a>
        </li>
        <li>
          <a class="side-link <?= $active === 'admin_users' ? 'is-active' : '' ?>"
             href="admin_users.php" <?= $active === 'admin_users' ? 'aria-current="page"' : '' ?>>
            User Management
          </a>
        </li>
        <li>
          <a class="side-link <?= $active === 'admin_inquiries' ? 'is-active' : '' ?>"
             href="admin_inquiries.php" <?= $active === 'admin_inquiries' ? 'aria-current="page"' : '' ?>>
            Inquiries
          </a>
        </li>
        <li>
          <a class="side-link <?= $active === 'admin_courses' ? 'is-active' : '' ?>"
             href="admin_courses.php" <?= $active === 'admin_courses' ? 'aria-current="page"' : '' ?>>
            Courses
          </a>
        </li>
        <li>
          <a class="side-link <?= $active === 'admin_enrollments' ? 'is-active' : '' ?>"
             href="admin_enrollments.php" <?= $active === 'admin_enrollments' ? 'aria-current="page"' : '' ?>>
            Enrollments
          </a>
        </li>
      <?php elseif ($role === 'user'): ?>
        <li>
          <a class="side-link <?= $active === 'user_enrollments' ? 'is-active' : '' ?>"
             href="user_enrollments.php" <?= $active === 'user_enrollments' ? 'aria-current="page"' : '' ?>>
            My Enrollments
          </a>
        </li>
        <li>
          <a class="side-link <?= $active === 'user_profile' ? 'is-active' : '' ?>"
             href="user_profile.php" <?= $active === 'user_profile' ? 'aria-current="page"' : '' ?>>
            My Profile
          </a>
        </li>
      <?php else: ?>
        <li><a class="side-link <?= $active === 'home' ? 'is-active' : '' ?>" href="index.php">Home</a></li>
        <li><a class="side-link <?= $active === 'courses' ? 'is-active' : '' ?>" href="courses.php">Courses</a></li>
      <?php endif; ?>
    </ul>

    <hr class="side-sep" />

    <!-- Logout: only show if logged in -->
    <?php if (!empty($_SESSION['account_id'])): ?>
      <form id="logoutForm" action="backend/logout.php" method="post" class="logout-form">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
        <a href="#" class="side-link danger" id="logoutLink" role="link">Logout</a>
        <noscript>
          <button type="submit" class="side-link danger nojs">Logout</button>
        </noscript>
      </form>
    <?php endif; ?>
  </nav>
</aside>

<script>
  (function () {
    var link = document.getElementById('logoutLink');
    var form = document.getElementById('logoutForm');
    if (link && form) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to logout?")) {
          form.submit();
        }
      });
    }
  }());
</script>
