<?php
// partials/header.php — session-aware header for LearnLang

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$active = $active ?? '';

function navActive(string $key, string $active): string {
  return $key === $active ? 'class="active" aria-current="page"' : '';
}

/**
 * Detect logged-in user (supports current keys and older ones)
 */
$accountId   = $_SESSION['account_id']   ?? null;          // current
$accountType = $_SESSION['account_type'] ?? null;          // 'admin' | 'customer'
if (!$accountId) {
  // legacy fallbacks if you ever used these before
  $accountId   = $_SESSION['user']['id'] ?? ($_SESSION['user_id'] ?? null);
  $accountType = $accountType
    ?? ($_SESSION['user']['role'] ?? ($_SESSION['role'] ?? null));
}

$isLoggedIn = !empty($accountId);

/**
 * Normalize role → pick dashboard link
 * In your DB you store: account_type ENUM('admin','customer')
 */
$role = $accountType ?: 'customer';
$role = ($role === 'admin') ? 'admin' : 'customer';

if ($isLoggedIn) {
  $authHref  = ($role === 'admin')
    ? 'admin_dashboard.php'
    : 'user_enrollments.php';   // matches backend redirect
  $authLabel = 'Return to Dashboard';
  if ($role === 'admin') {
    $dashboardKeys = ['admin_dashboard', 'admin_users', 'admin_inquiries', 'admin_courses', 'admin_enrollments'];
  } else {
    $dashboardKeys = ['user_enrollments'];
  }
  
  $authActive = in_array($active, $dashboardKeys, true)
    ? 'class="active" aria-current="page"'
    : '';
} else {
  $authHref   = 'login.php';
  $authLabel  = 'Login';
  $authActive = ($active === 'login') ? 'class="active" aria-current="page"' : '';
}
?>
<header class="site-header">
  <a href="/index.php" class="logo-link" aria-label="LearnLang Academy Home">
    <img src="./images/learnlang-logo.png" alt="LearnLang Academy logo" class="site-logo" />
    <span class="logo-text">LearnLang Academy</span>
  </a>

  <nav aria-label="Main navigation" class="primary-nav">
    <button
      class="menu-toggle"
      aria-label="Toggle menu"
      aria-expanded="false"
      aria-controls="main-menu"
      type="button"
    >
      <span class="bar"></span><span class="bar"></span><span class="bar"></span>
    </button>

    <ul id="main-menu" class="menu">
      <li><a href="./index.php"         <?= navActive('home', $active) ?>>Home</a></li>
      <li><a href="./courses.php"       <?= navActive('courses', $active) ?>>Courses</a></li>
      <li><a href="./pricing.php"       <?= navActive('pricing', $active) ?>>Pricing</a></li>
      <li><a href="./contact.php"       <?= navActive('contact', $active) ?>>Contact Us</a></li>
      <li>
        <a href="<?= htmlspecialchars($authHref, ENT_QUOTES) ?>" <?= $authActive ?>>
          <?= htmlspecialchars($authLabel, ENT_QUOTES) ?>
        </a>
      </li>
    </ul>
  </nav>
</header>
