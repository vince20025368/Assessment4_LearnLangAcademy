<?php
// backend/user_delete.php — Delete a user
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

$id = trim($_POST['id'] ?? '');
if ($id === '') redirect_error('Missing user id.');

// Prevent self-deletion
$currentUserId = $_SESSION['account_id'] ?? '';
if ($id === $currentUserId) {
  redirect_error('You cannot delete your own account.');
}

// Ensure user exists
$stmt = $conn->prepare("SELECT id, name, email FROM accounts WHERE id = ? LIMIT 1");
$stmt->bind_param('s', $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) redirect_error('User not found.');

// Delete user
$stmt = $conn->prepare("DELETE FROM accounts WHERE id = ? LIMIT 1");
$stmt->bind_param('s', $id);
if (!$stmt->execute()) {
  $err = $conn->error ?: 'Delete failed.';
  $stmt->close();
  redirect_error($err);
}
$stmt->close();

redirect_success("User '{$user['name']}' deleted successfully.");
