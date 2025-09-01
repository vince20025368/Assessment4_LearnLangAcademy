<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$active     = $active ?? '';
$isLoggedIn = !empty($_SESSION['user_id']);
$authLabel  = $isLoggedIn ? 'Return to Dashboard' : 'Login';
$authHref   = $isLoggedIn ? 'dashboard.php'       : 'login.php';

function navActive(string $key, string $active): string {
  return $key === $active ? 'class="active" aria-current="page"' : '';
}
?>
<header class="site-header">
  <a href="index.php" class="logo-link" aria-label="LearnLang Academy Home">
    <img src="images/learnlang-logo.png" alt="LearnLang Academy logo" class="site-logo" />
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
      <li><a href="index.php"   <?= navActive('home', $active)    ?>>Home</a></li>
      <li><a href="courses.php" <?= navActive('courses', $active) ?>>Courses</a></li>
      <li><a href="pricing.php" <?= navActive('pricing', $active) ?>>Pricing</a></li>
      <li><a href="contact.php" <?= navActive('contact', $active) ?>>Contact Us</a></li>
      <li><a href="<?= htmlspecialchars($authHref, ENT_QUOTES) ?>" <?= navActive('login', $active) ?>>
        <?= htmlspecialchars($authLabel, ENT_QUOTES) ?></a>
      </li>
    </ul>
  </nav>
</header>
