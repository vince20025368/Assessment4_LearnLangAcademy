<?php
// backend/register_submit.php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.php');
  exit;
}

// CSRF
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  header('Location: ../register.php?err=1&msg=' . urlencode('Invalid request.'));
  exit;
}

// Honeypot
if (!empty($_POST['hp'])) {
  header('Location: ../thank-you.php?title=Account+Created&msg=Your+account+request+was+received.&back=index.html');
  exit;
}

// Collect
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');

// Validate
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 8) {
  header('Location: ../register.php?err=1');
  exit;
}

// Duplicate email
$stmt = $conn->prepare("SELECT 1 FROM accounts WHERE email=? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  $stmt->close();
  header('Location: ../register.php?err=1&msg=' . urlencode('Email already registered.'));
  exit;
}
$stmt->close();

// Insert
$id            = uuidv4();
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$account_type  = 'user';
$is_active     = 1;
$is_verified   = 0;

$sql = "INSERT INTO accounts
        (id, name, email, password_hash, account_type, is_active, is_verified)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  header('Location: ../register.php?err=1&msg=' . urlencode('Failed to prepare statement.'));
  exit;
}
$stmt->bind_param('sssssii', $id, $name, $email, $password_hash, $account_type, $is_active, $is_verified);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
  header('Location: ../register.php?err=1&msg=' . urlencode('Failed to create account.'));
  exit;
}

unset($_SESSION['csrf']);

// 🔁 Updated redirect → generic thank-you (no ref code here)
$title = urlencode('Account Created');
$msg   = urlencode('Your account has been created. You can sign in when login is available.');
$back  = urlencode('index.html');

header("Location: ../thank-you.php?title={$title}&msg={$msg}&back={$back}");
exit;
