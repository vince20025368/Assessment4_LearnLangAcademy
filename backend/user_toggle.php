<?php
// backend/user_toggle.php — Toggle Active / Verified for any user
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

function redirect_success(string $msg): void {
  $_SESSION['success'] = $msg;
  header('Location: ../admin_users.php');
  exit;
}
function redirect_error(string $msg): void {
  $_SESSION['error'] = $msg;
  header('Location: ../admin_users.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect_error('Invalid request.');
if (!csrf_validate($_POST['csrf'] ?? '')) redirect_error('Invalid CSRF token.');

$action = $_POST['action'] ?? '';
$id     = trim($_POST['id'] ?? '');
if ($id === '') redirect_error('Missing user id.');

// Disallow deactivating yourself
$currentUserId = $_SESSION['account_id'] ?? '';
if ($action === 'deactivate' && $id === $currentUserId) {
  redirect_error('You cannot deactivate your own account.');
}

// Fetch user
$stmt = $conn->prepare("SELECT id, name, is_active, is_verified FROM accounts WHERE id = ? LIMIT 1");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) redirect_error('User not found.');

// Decide update
$field = '';
$value = 0;
$msg   = '';

switch ($action) {
  case 'activate':
    $field = 'is_active'; $value = 1; $msg = 'User activated.'; break;
  case 'deactivate':
    $field = 'is_active'; $value = 0; $msg = 'User deactivated.'; break;
  case 'verify':
    $field = 'is_verified'; $value = 1; $msg = 'User verified.'; break;
  case 'unverify':
    $field = 'is_verified'; $value = 0; $msg = 'User unverified.'; break;
  default:
    redirect_error('Invalid action.');
}

$stmt = $conn->prepare("UPDATE accounts SET {$field} = ?, updated_at = NOW() WHERE id = ? LIMIT 1");
$stmt->bind_param('is', $value, $id);
if (!$stmt->execute()) {
  $err = $conn->error ?: 'Toggle failed.';
  $stmt->close();
  redirect_error($err);
}
$stmt->close();

redirect_success($msg);
