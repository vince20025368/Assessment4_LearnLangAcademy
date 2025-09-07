<?php
// backend/enrollment_delete.php — delete enrollment
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_enrollments.php?error=method');
  exit;
}

// CSRF check
if (!csrf_validate($_POST['csrf'] ?? '')) {
  die("Invalid CSRF token");
}

// Inputs
$enrollment_id = trim($_POST['enrollment_id'] ?? '');
$course_id     = trim($_POST['course_id'] ?? '');

if ($enrollment_id === '' || $course_id === '') {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=missing");
  exit;
}

// Delete enrollment
$sql = "DELETE FROM enrollments WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("DB error: " . $conn->error);
}
$stmt->bind_param("s", $enrollment_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&success=deleted");
} else {
  header("Location: ../admin_enrollment_manage.php?course_id=" . urlencode($course_id) . "&error=failed");
}
exit;
