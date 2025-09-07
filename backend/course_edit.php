<?php
// backend/course_edit.php — Handle Edit Course
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../courses.php');
  exit;
}

// CSRF validation
if (!csrf_validate($_POST['csrf'] ?? '')) {
  $_SESSION['form_errors'] = ['Invalid request. Please try again.'];
  header('Location: ../courses.php');
  exit;
}

// Collect inputs
$id          = trim($_POST['id'] ?? '');
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

$errors = [];

// Validation
if ($id === '')          $errors[] = 'Course ID is required.';
if ($title === '')       $errors[] = 'Course Title is required.';
if ($description === '') $errors[] = 'Description is required.';

// Update
if (!$errors) {
  $sql = "UPDATE courses 
          SET title = ?, description = ?, updated_at = NOW()
          WHERE id = ?";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param("sss", $title, $description, $id);
    if ($stmt->execute()) {
      $_SESSION['form_success'] = 'Course updated successfully.';
      header("Location: ../admin_course_edit.php?id=" . urlencode($id));
      exit;
    } else {
      $errors[] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

// On error
$_SESSION['form_errors'] = $errors;
header("Location: ../admin_course_edit.php?id=" . urlencode($id));
exit;
