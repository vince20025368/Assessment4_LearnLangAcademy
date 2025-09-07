<?php
// backend/user_edit.php — Handle Edit User
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_users.php');
  exit;
}

// CSRF validation
if (!csrf_validate($_POST['csrf'] ?? '')) {
  $_SESSION['form_errors'] = ['Invalid request. Please try again.'];
  header('Location: ../admin_users.php');
  exit;
}

// Collect inputs
$id       = trim($_POST['id'] ?? '');
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';
$role     = trim($_POST['role'] ?? '');

$errors = [];

// Validation
if ($id === '') $errors[] = 'User ID is required.';
if ($name === '') $errors[] = 'Full Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
if (!in_array($role, ['admin','user','human_resource','stakeholder'], true)) {
  $errors[] = 'Valid Role is required.';
}
if ($password !== '') {
  if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
  if ($password !== $confirm) $errors[] = 'Passwords do not match.';
}

// Check duplicate email (ignore this user’s own ID)
if (!$errors) {
  $sql = "SELECT id FROM accounts WHERE email = ? AND id <> ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param("ss", $email, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = 'Email already exists.';
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

// Update user
if (!$errors) {
  if ($password !== '') {
    // With password update
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE accounts 
            SET name = ?, email = ?, password_hash = ?, account_type = ?, updated_at = NOW()
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param("sssss", $name, $email, $hash, $role, $id);
      if ($stmt->execute()) {
        $_SESSION['form_success'] = 'User updated successfully (with password change).';
        header("Location: ../admin_user_edit.php?id=" . urlencode($id));
        exit;
      } else {
        $errors[] = 'Database error: ' . $stmt->error;
      }
      $stmt->close();
    } else {
      $errors[] = 'Database error: ' . $conn->error;
    }
  } else {
    // Without password update
    $sql = "UPDATE accounts 
            SET name = ?, email = ?, account_type = ?, updated_at = NOW()
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param("ssss", $name, $email, $role, $id);
      if ($stmt->execute()) {
        $_SESSION['form_success'] = 'User updated successfully.';
        header("Location: ../admin_user_edit.php?id=" . urlencode($id));
        exit;
      } else {
        $errors[] = 'Database error: ' . $stmt->error;
      }
      $stmt->close();
    } else {
      $errors[] = 'Database error: ' . $conn->error;
    }
  }
}

// On error redirect back
$_SESSION['form_errors'] = $errors;
header("Location: ../admin_user_edit.php?id=" . urlencode($id));
exit;
