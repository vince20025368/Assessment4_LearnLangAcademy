<?php
// backend/inquiry_edit.php — update action_taken
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

if ($_SESSION['account_type'] !== 'admin') {
  http_response_code(403);
  exit('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin_inquiries.php');
  exit;
}

if (!csrf_validate($_POST['csrf'] ?? '')) {
  $_SESSION['error'] = 'Invalid request token.';
  header('Location: ../admin_inquiries.php');
  exit;
}

$id          = trim($_POST['id'] ?? '');
$actionTaken = trim($_POST['action_taken'] ?? '');

if ($id === '') {
  $_SESSION['error'] = 'Missing inquiry ID.';
  header('Location: ../admin_inquiries.php');
  exit;
}

$stmt = $conn->prepare("UPDATE contact_inquiries SET action_taken = ? WHERE id = ? LIMIT 1");
$stmt->bind_param('ss', $actionTaken, $id);

if ($stmt->execute()) {
  $_SESSION['success'] = 'Action updated successfully.';
} else {
  $_SESSION['error'] = 'Database error: ' . $stmt->error;
}

$stmt->close();
header('Location: ../admin_inquiries.php');
exit;
