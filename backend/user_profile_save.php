<?php
// backend/user_profile_save.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../user_profile.php');
  exit;
}

// CSRF check
if (!csrf_validate($_POST['csrf'] ?? '')) {
  die("Invalid CSRF token");
}

$userId   = $_SESSION['account_id'] ?? '';
$name     = trim($_POST['name'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($name === '') {
  $_SESSION['form_errors'] = "Name cannot be empty.";
  header("Location: ../user_profile.php");
  exit;
}

if ($password !== '' && $password !== $confirm) {
  $_SESSION['form_errors'] = "Passwords do not match.";
  header("Location: ../user_profile.php");
  exit;
}

if ($userId === '') {
  $_SESSION['form_errors'] = "Not logged in.";
  header("Location: ../login.php");
  exit;
}

// Update DB
if ($password === '') {
  $sql = "UPDATE accounts SET name = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $name, $userId);
} else {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql = "UPDATE accounts SET name = ?, password_hash = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $name, $hash, $userId);
}

if ($stmt->execute()) {
  $_SESSION['form_success'] = "Profile updated successfully.";
} else {
  $_SESSION['form_errors'] = "Failed to update profile.";
}
$stmt->close();

header("Location: ../user_profile.php");
exit;
