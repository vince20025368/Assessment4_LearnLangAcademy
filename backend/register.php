<?php
// backend/register.php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.php');
  exit;
}

$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm_password'] ?? '';

$errors = [];

// Validation
if ($name === '') $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($password === '') $errors[] = 'Password is required.';
if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';

// Check duplicate email
if (!$errors) {
  $sql = "SELECT id FROM accounts WHERE email = ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $errors[] = 'That email is already registered.';
    }
    $stmt->close();
  } else {
    $errors[] = "Database error: " . $conn->error;
  }
}

if ($errors) {
  $_SESSION['reg_errors'] = $errors;
  $_SESSION['old'] = ['name' => $name, 'email' => $email];
  header('Location: ../register.php');
  exit;
}

// Insert user
$id   = uuidv4();
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO accounts (id, name, email, password_hash, account_type) 
        VALUES (?, ?, ?, ?, 'user')";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("Prepare failed (insert): " . $conn->error);
}
$stmt->bind_param('ssss', $id, $name, $email, $hash);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
  die("Insert failed: " . $conn->error);
}

unset($_SESSION['csrf'], $_SESSION['reg_errors'], $_SESSION['old']);

// ✅ Success message
$_SESSION['reg_success'] = "Account created successfully! You can now login.";

// Redirect back to register page (shows success msg at top)
header('Location: ../register.php');
exit;
