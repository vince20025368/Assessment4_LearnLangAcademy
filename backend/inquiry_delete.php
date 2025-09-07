<?php
// backend/inquiry_delete.php — Delete Inquiry
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';

function redirect_back(string $msg, bool $error = false): void {
  if ($error) {
    $_SESSION['error'] = $msg;
  } else {
    $_SESSION['success'] = $msg;
  }
  header('Location: ../admin_inquiries.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect_back('Invalid request method.', true);
}

// CSRF validation
if (!csrf_validate($_POST['csrf'] ?? '')) {
  redirect_back('Invalid request token.', true);
}

// Collect input
$id = trim($_POST['id'] ?? '');
if ($id === '') {
  redirect_back('Inquiry ID is required.', true);
}

// Delete inquiry
$stmt = $conn->prepare("DELETE FROM contact_inquiries WHERE id = ? LIMIT 1");
if ($stmt) {
  $stmt->bind_param("s", $id);
  if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
      redirect_back('Inquiry deleted successfully.');
    } else {
      redirect_back('Inquiry not found.', true);
    }
  } else {
    redirect_back('Database error: ' . $stmt->error, true);
  }
  $stmt->close();
} else {
  redirect_back('Database error: ' . $conn->error, true);
}
