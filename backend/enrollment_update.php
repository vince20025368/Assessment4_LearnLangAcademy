<?php
// backend/enrollment_update.php — update enrollment status
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

$enrollment_id = trim($_POST['enrollment_id'] ?? '');
$status        = trim($_POST['status'] ?? '');

$valid_status = ['active', 'completed', 'dropped'];
if ($enrollment_id === '' || !in_array($status, $valid_status, true)) {
  header("Location: ../admin_enrollments.php?error=invalid");
  exit;
}

// ✅ Get course_id first so we can redirect back correctly
$sqlCourse = "SELECT course_id FROM enrollments WHERE id = ?";
$stmtCourse = $conn->prepare($sqlCourse);
$stmtCourse->bind_param("s", $enrollment_id);
$stmtCourse->execute();
$stmtCourse->bind_result($course_id);
$stmtCourse->fetch();
$stmtCourse->close();

if (!$course_id) {
  header("Location: ../admin_enrollments.php?error=notfound");
  exit;
}

// Update status
$sql = "UPDATE enrollments SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("DB error: " . $conn->error); }
$stmt->bind_param("ss", $status, $enrollment_id);

if ($stmt->execute()) {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&success=updated");
} else {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=failed");
}
exit;
