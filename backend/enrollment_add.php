<?php
// backend/enrollment_add.php — add user enrollment
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_enrollments.php');
  exit;
}

// Validate CSRF
if (!csrf_validate($_POST['csrf'] ?? '')) {
  die("Invalid CSRF token");
}

$course_id  = trim($_POST['course_id'] ?? '');
$account_id = trim($_POST['account_id'] ?? '');

if ($course_id === '' || $account_id === '') {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=missing");
  exit;
}

// ✅ Check if already enrolled
$sqlCheck = "SELECT id FROM enrollments WHERE course_id = ? AND account_id = ? LIMIT 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $course_id, $account_id);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
  $stmtCheck->close();
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=exists");
  exit;
}
$stmtCheck->close();

// ✅ Insert new enrollment
$sql = "INSERT INTO enrollments (id, course_id, account_id, status, enrolled_at) 
        VALUES (UUID(), ?, ?, 'active', NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("DB error: " . $conn->error); }
$stmt->bind_param("ss", $course_id, $account_id);

if ($stmt->execute()) {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&success=added");
} else {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=failed");
}
exit;
