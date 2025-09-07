<?php
// backend/login.php
session_start();
require __DIR__ . '/db.php';

// Honeypot check
if (!empty($_POST['website'])) {
  header('Location: ../login.php');
  exit;
}

// Allow POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../login.php');
  exit;
}

// CSRF check
if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
  $_SESSION['login_errors'] = ['Invalid request. Please try again.'];
  header('Location: ../login.php');
  exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

// Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'Please enter a valid email address.';
}
if ($password === '') {
  $errors[] = 'Password is required.';
}

if ($errors) {
  $_SESSION['login_errors'] = $errors;
  $_SESSION['old_login'] = ['email' => $email];
  header('Location: ../login.php');
  exit;
}

// Fetch account from DB
$sql = "SELECT id, name, email, password_hash, account_type, is_active, is_verified 
        FROM accounts 
        WHERE email = ? 
        LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  $_SESSION['login_errors'] = ['Server error. Please try again later.'];
  header('Location: ../login.php');
  exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();
$stmt->close();

// Check password
if (!$account || !password_verify($password, $account['password_hash'])) {
  $_SESSION['login_errors'] = ['Invalid email or password.'];
  $_SESSION['old_login'] = ['email' => $email];
  header('Location: ../login.php');
  exit;
}

// Account status
if ((int)$account['is_active'] !== 1) {
  $_SESSION['login_errors'] = ['Your account has been deactivated.'];
  header('Location: ../login.php');
  exit;
}
if ((int)$account['is_verified'] !== 1) {
  $_SESSION['login_errors'] = ['Please verify your account before logging in.'];
  header('Location: ../login.php');
  exit;
}

// Success → set identity session
$_SESSION['account_id']    = $account['id'];
$_SESSION['account_email'] = $account['email'];
$_SESSION['account_name']  = $account['name'];
$_SESSION['account_type']  = $account['account_type']; // ENUM: 'admin' or 'customer'

// Redirect by role
if ($account['account_type'] === 'admin') {
  header('Location: ../admin_dashboard.php');
} elseif ($account['account_type'] === 'user') {
  header('Location: ../user_enrollments.php');
} else {
  // fallback (if ENUM gets extended later)
  header('Location: ../index.php');
}
exit;
