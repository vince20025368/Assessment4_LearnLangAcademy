<?php
// backend/logout.php
declare(strict_types=1);

session_start();

// Only accept POST to prevent CSRF via GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../index.php');
  exit;
}

// CSRF check (expects a token from the header's logout form)
$csrf = $_POST['csrf'] ?? '';
if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
  // Invalid token → drop session anyway and bounce to login
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
  header('Location: ../login.php');
  exit;
}

// Clear all session data
$_SESSION = [];

// Remove session cookie
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destroy session
session_destroy();

// (Optional) start a fresh session to seed a new CSRF for the login page
session_start();
$_SESSION['csrf'] = bin2hex(random_bytes(32));

// Redirect to login
header('Location: ../login.php');
exit;
