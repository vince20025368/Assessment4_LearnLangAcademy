<?php
// backend/user_enrollment_add.php — User adds their own enrollment
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

$account_id = $_SESSION['account_id'];
$course_id  = trim($_POST['course_id'] ?? '');

if ($course_id === '') {
  header("Location: ../user_enrollments.php?error=missing");
  exit;
}

// Insert enrollment (prevent duplicates)
$sql = "INSERT INTO enrollments (id, account_id, course_id, status)
        VALUES (UUID(), ?, ?, 'active')";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("DB error: " . $conn->error);
}
$stmt->bind_param("ss", $account_id, $course_id);

if ($stmt->execute()) {
  header("Location: ../user_enrollments.php?success=added");
} else {
  if ($conn->errno == 1062) { // duplicate enrollment
    header("Location: ../user_enrollments.php?error=exists");
  } else {
    header("Location: ../user_enrollments.php?error=failed");
  }
}
exit;
