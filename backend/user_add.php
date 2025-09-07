<?php
// backend/user_add.php — Handle Add User (any role)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_user_add.php');
  exit;
}

// CSRF validation
if (!csrf_validate($_POST['csrf'] ?? '')) {
  $_SESSION['form_errors'] = ['Invalid request. Please try again.'];
  header('Location: ../admin_user_add.php');
  exit;
}

// Collect inputs
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';
$role     = trim($_POST['role'] ?? '');

// Preserve form data (except password) on error
$_SESSION['old'] = [
  'name'  => $name,
  'email' => $email,
  'role'  => $role
];

$errors = [];

// Validation
if ($name === '') $errors[] = 'Full Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
if ($password === '' || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';
if (!in_array($role, ['admin','user','human_resource','stakeholder'], true)) {
  $errors[] = 'Valid Role is required.';
}

// Check duplicate email
if (!$errors) {
  $stmt = $conn->prepare("SELECT id FROM accounts WHERE email = ? LIMIT 1");
  if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = 'Email already exists.';
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

// Insert user
if (!$errors) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO accounts 
            (id, name, email, password_hash, account_type, is_active, is_verified, created_at) 
          VALUES 
            (UUID(), ?, ?, ?, ?, 1, 1, NOW())";

  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $hash, $role);
    if ($stmt->execute()) {
      unset($_SESSION['old']); // clear preserved data
      $_SESSION['form_success'] = 'User added successfully.';
      header('Location: ../admin_user_add.php');
      exit;
    } else {
      $errors[] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
  } else {
    $errors[] = 'Database error: ' . $conn->error;
  }
}

// On error redirect back
$_SESSION['form_errors'] = $errors;
header('Location: ../admin_user_add.php');
exit;
