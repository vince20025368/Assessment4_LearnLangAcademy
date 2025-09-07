<?php
// backend/course_add.php — Handle Add Course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_course_add.php');
  exit;
}

// CSRF validation
if (!csrf_validate($_POST['csrf'] ?? '')) {
  $_SESSION['form_errors'] = ['Invalid request. Please try again.'];
  header('Location: ../admin_course_add.php');
  exit;
}

// Collect inputs
$course_code = trim($_POST['course_code'] ?? '');
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

$_SESSION['old'] = [
  'course_code' => $course_code,
  'title'       => $title,
  'description' => $description
];

$errors = [];

// Validation
if ($course_code === '') $errors[] = 'Course Code is required.';
if ($title === '')       $errors[] = 'Course Title is required.';
if ($description === '') $errors[] = 'Description is required.';

// Check duplicate course code
if (!$errors) {
  $stmt = $conn->prepare("SELECT id FROM courses WHERE course_code = ? LIMIT 1");
  if ($stmt) {
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = 'Course Code already exists.';
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

// Insert
if (!$errors) {
  $id = bin2hex(random_bytes(16)); // UUID-like id
  $stmt = $conn->prepare("INSERT INTO courses (id, course_code, title, description, created_at, updated_at)
                          VALUES (?, ?, ?, ?, NOW(), NOW())");
  if ($stmt) {
    $stmt->bind_param("ssss", $id, $course_code, $title, $description);
    if ($stmt->execute()) {
      unset($_SESSION['old']);
      $_SESSION['form_success'] = 'Course added successfully.';
      header('Location: ../admin_course_add.php');
      exit;
    } else {
      $errors[] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

$_SESSION['form_errors'] = $errors;
header('Location: ../admin_course_add.php');
exit;
