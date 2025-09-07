<?php
// backend/user_enrollment_delete.php — User deletes their own enrollment
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

// Ensure logged in
if (empty($_SESSION['account_id']) || ($_SESSION['account_type'] ?? '') !== 'user') {
  header("Location: ../login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../user_enrollments.php?error=method");
  exit;
}

// CSRF check
if (!csrf_validate($_POST['csrf'] ?? '')) {
  die("Invalid CSRF token");
}

$account_id    = $_SESSION['account_id'];
$enrollment_id = trim($_POST['enrollment_id'] ?? '');

if ($enrollment_id === '') {
  header("Location: ../user_enrollments.php?error=missing");
  exit;
}

// Delete only if the enrollment belongs to this user
$sql = "DELETE FROM enrollments WHERE id = ? AND account_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("DB error: " . $conn->error);
}
$stmt->bind_param("ss", $enrollment_id, $account_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
  header("Location: ../user_enrollments.php?success=deleted");
} else {
  header("Location: ../user_enrollments.php?error=failed");
}
exit;
