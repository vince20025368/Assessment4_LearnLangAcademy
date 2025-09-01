<?php
// backend/submit_contact.php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../contact.php');
  exit;
}

// CSRF
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  http_response_code(400);
  exit('Invalid request token.');
}

// Honeypot
if (!empty($_POST['website'])) {
  header('Location: ../thank-you.php?title=Thank+you!+%F0%9F%8E%89&msg=Your+inquiry+has+been+received.&back=index.html');
  exit;
}

// Collect
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
  header('Location: ../contact.php?err=1');
  exit;
}

// IDs
$id      = uuidv4();
$refCode = simple_ref('C'); // C + 7 random digits

// Insert
$sql = "INSERT INTO contact_inquiries (id, ref_code, name, email, subject, message)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  exit('Failed to prepare insert.');
}
$stmt->bind_param('ssssss', $id, $refCode, $name, $email, $subject, $message);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
  http_response_code(500);
  exit('Failed to save your message.');
}

unset($_SESSION['csrf']);

// Redirect → thank-you with reference code
$title = urlencode('Thank you! 🎉');
$msg   = urlencode('Your inquiry has been received.');
$back  = urlencode('index.html');

header("Location: ../thank-you.php?title={$title}&msg={$msg}&ref=" . urlencode($refCode) . "&back={$back}");
exit;
